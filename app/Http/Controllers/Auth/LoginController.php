<?php

namespace App\Http\Controllers\Auth;

use App\Constants\Feature;
use App\Http\Controllers\Controller;
use App\Models\Estate;
use App\Models\EstateModFeature;
use App\Models\Logger;
use App\Models\ModFeature;
use App\Models\Setting;
use App\Models\Tariff;
use App\Models\TarrifState;
use App\Models\User;
use App\Models\UtilitiesPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // ─── 1. Resolve credentials ───────────────────────────────────────────────
        $isEmailLogin  = !empty($request->email);
        $isMeterLogin  = !empty($request->meterNo);

        if (!$isEmailLogin && !$isMeterLogin) {
            return error("Email or Meter Number is required", 422);
        }

        $credentials = $isEmailLogin
            ? $request->only('email', 'password')
            : $request->only('meterNo', 'password');

        // ─── 2. Find user ─────────────────────────────────────────────────────────
        $usr = User::when($isEmailLogin,
                fn($q) => $q->where('email', $request->email),
                fn($q) => $q->where('meterNo', $request->meterNo)
            )->first();

        if ($usr === null) {
            return error("User does not exist", 404);
        }

        if ($usr->status == 0) {
            return error("Your Account has been deactivated reach out to your estate admin for help", 403);
        }

        // ─── 3. Attempt auth ──────────────────────────────────────────────────────
        Passport::tokensExpireIn(Carbon::now()->addHours(2));
        Passport::refreshTokensExpireIn(Carbon::now()->addHours(2));

        if (!auth()->attempt($credentials)) {
            $field = $isEmailLogin ? "Email" : "Meter No";
            return error("{$field} or Password Incorrect", 422);
        }

        flush_token();

        // ─── 4. Shared context ────────────────────────────────────────────────────
        $userId   = Auth::id();
        $estateId = Auth::user()->estate_id;
        $estate   = Estate::where('id', $estateId)->first();

        $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
            ->where('estate_id', $estateId)
            ->get();

        foreach ($tariffs as $tariff) {
            $tariffState = TarrifState::where('tariff_id', $tariff->id)
                ->where('amount', '>', 0)
                ->first();
            $tariff->amount = $tariffState?->amount;
        }

        $adminFeeAmount = $estate->getAdminFee();
        $adminFeeFlag   = "0";
        $meter          = meter();
        $meter = $meter ? $meter : null;
        $user           = user();
        $utilityAmount  = $estate->total_utility_amount ?? 0;
        $duration       = $estate->duration ?? null;
        $mod_features   = [];

        // ─── 5. Transaction: backfill utilities & admin fees ─────────────────────
        DB::transaction(function () use (
            $estateId, $userId, $tariffs, $adminFeeAmount,
            &$adminFeeFlag, $meter, &$user, $utilityAmount,
            $duration, &$mod_features
        ) {
            // ── Helper ────────────────────────────────────────────────────────────
            $createPayment = function (string $type, float $amount, string $duration, Carbon $startDate) use ($userId, $estateId) {
                $nextDueDate = $startDate->copy();

                match ($duration) {
                    'weekly'  => $nextDueDate->addWeek(),
                    'monthly' => $nextDueDate->addMonth(),
                    'yearly'  => $nextDueDate->addYear(),
                    default   => send_notification("Unknown duration '{$duration}'"),
                };

                return UtilitiesPayment::create([
                    'estate_id'     => $estateId,
                    'user_id'       => $userId,
                    'amount'        => $amount,
                    'total_amount'  => $amount,
                    'next_due_date' => $nextDueDate,
                    'duration'      => $duration,
                    'type'          => $type,
                    'status'        => 0,
                ]);
            };

            if ($duration === null) {
                // Can't throw inside a transaction cleanly — flag it and handle after
                // Alternatively, validate $duration before the transaction starts (recommended)
                return;
            }

            // ── Utility backfill ──────────────────────────────────────────────────
            if ($utilityAmount > 0) {
                $lastUtilityDate = UtilitiesPayment::where('user_id', $userId)
                    ->where('type', 'utilities')
                    ->orderByDesc('created_at')
                    ->value('created_at');

                $backfillFrom = $lastUtilityDate
                    ? Carbon::parse($lastUtilityDate)->addMonth()->startOfMonth()
                    : Carbon::parse(Auth::user()->created_at)->startOfMonth();

                $now = Carbon::now()->startOfMonth();

                while ($backfillFrom->lte($now)) {
                    $exists = UtilitiesPayment::where('user_id', $userId)
                        ->where('type', 'utilities')
                        ->whereYear('created_at', $backfillFrom->year)
                        ->whereMonth('created_at', $backfillFrom->month)
                        ->exists();

                    if (!$exists) {
                        $createPayment('utilities', $utilityAmount, $duration, $backfillFrom->copy());
                    }

                    $backfillFrom->addMonth();
                }
            }

            // ── Admin fee backfill ────────────────────────────────────────────────
            if ($adminFeeAmount > 0) {
                $lastAdminFeeDate = UtilitiesPayment::where('user_id', $userId)
                    ->where('type', 'admin_fee')
                    ->orderByDesc('created_at')
                    ->value('created_at');

                $backfillFrom = $lastAdminFeeDate
                    ? Carbon::parse($lastAdminFeeDate)->addMonth()->startOfMonth()
                    : Carbon::parse(Auth::user()->created_at)->startOfMonth();

                $now = Carbon::now()->startOfMonth();

                while ($backfillFrom->lte($now)) {
                    $exists = UtilitiesPayment::where('user_id', $userId)
                        ->where('type', 'admin_fee')
                        ->whereYear('created_at', $backfillFrom->year)
                        ->whereMonth('created_at', $backfillFrom->month)
                        ->exists();

                    if (!$exists) {
                        $createPayment('admin_fee', $adminFeeAmount, 'monthly', $backfillFrom->copy());
                    }

                    $backfillFrom->addMonth();
                }
            }

            // ── Admin fee paid flag (current month) ───────────────────────────────
            $adminFeeFlag = UtilitiesPayment::where('user_id', $userId)
                ->where('type', 'admin_fee')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 2)
                ->exists() ? "1" : "0";

            // ── Build response user object ─────────────────────────────────────────
            $token = auth()->user()->createToken('API token')->accessToken;

            $user['token']             = $token;
            $user['meter']             = $meter;
            $user['tariff']            = $tariffs;
            $user['monthly_admin_fee'] = $adminFeeFlag;
            $user['meter_status']      = $meter?->status;

            // ── Mod features ──────────────────────────────────────────────────────
            $features =  EstateModFeature::byUser($user)
                ->join('mod_features', 'mod_features.id', 'estate_mod_features.mod_feature_id')
                ->select([
                        'estate_mod_features.status as estate_status',
                        'estate_mod_features.estate_id',
                        'mod_features.title',
                        'mod_features.slug',
                        'mod_features.status as mod_status'
                    ])
                ->get();


            $mod_features = [];

            foreach ($features as $feature) {
                $final_status = $feature->mod_status;

                if ($feature->mod_status == ModFeature::AVAILABLE_STATUS) {
                    $final_status = $feature->estate_status;

                    if (
                        in_array($feature->slug, [\App\Constants\Feature::MOMAS_METER, \App\Constants\Feature::OTHER_METER])
                        && $feature->estate_status == ModFeature::AVAILABLE_STATUS
                        && ! $meter?->isActive()
                    ) {
                        $final_status = ModFeature::TEMPORARY_DOWNTIME_STATUS;
                    }
                }

                $mod_features[$feature->slug] = (int) $final_status;
            }
        });

        if (empty($mod_features)) {
            $mod_features = null;
        }

        // ─── 6. Guard: duration must be set (validate before transaction ideally) ─
        if ($duration === null) {
            return error("Estate utility duration not set, Contact support", 404);
        }

        // ─── 7. Log ───────────────────────────────────────────────────────────────
        Logger::info('LOGIN DEBUG', [
            'login_via'        => $isEmailLogin ? 'email' : 'meterNo',
            'identifier'       => $isEmailLogin ? $request->email : $request->meterNo,
            'user_status'      => $usr->status,
            'meter_status'     => $meter?->status,
            'tariffs_exist'    => $tariffs->isNotEmpty(),
            'admin_fee_amount' => $adminFeeAmount,
            'admin_fee_paid'   => $adminFeeFlag,
            'utility_amount'   => $utilityAmount,
            'utility_duration' => $duration,
        ]);

        // ─── 8. Response ──────────────────────────────────────────────────────────
        return response()->json([
            'status'   => true,
            'user'     => $user,
            'features' => $mod_features,
        ]);
    }

    public function delete_user(request $request)
    {
        User::where('email', $request->email)->update(['status' => 9]);

        return response()->json([
            'status' => true,
            'message' => "User Deleted successfully"
        ], 200);


    }
    public function reset_password(request $request)
    {
        $email = $request->email;

        if($request->password != $request->confirm_password){
            $code = 422;
            $message = "Password does not match";
            return error($message, $code);
        }
        User::where('email', $email)->update(['password' => bcrypt($request->password)]);

        return response()->json([
            'status' => true,
            'message' => "Password successfully updated"
        ], 200);

    }

    public function get_user(request $request)
    {

        // $fl = Setting::where('id', 1)->first();
        // $flkey['flutterwave_secret'] = $fl->flutterwave_secret;
        // $flkey['flutterwave_public'] = $fl->flutterwave_public;
        // $pkkey['paystack_secret'] = $fl->paystack_secret;
        // $pkkey['paystack_public'] = $fl->paystack_public;

        $admin_fee_get = UtilitiesPayment::where('user_id', Auth::id())
            ->where('type', 'admin_fee')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->latest('created_at')
            ->first();

        if($admin_fee_get && $admin_fee_get->status == 2){
            $admin_fee =  "1";
        }else{
            $admin_fee = "0";
        }



        $token = auth()->user()->createToken('API token')->accessToken;
        $meter = meter();
        $user = user();
        $user['token'] = $token;
        $user['meter_status'] = $meter->status;
        $user['meter'] = $meter;
        // $user['flutterwave_keys'] =  $flkey;
        // $user['paystack_keys'] =  $pkkey;
        $user['monthly_admin_fee'] = $admin_fee;







        return response()->json([
            'status' => true,
            'user' => $user
        ]);



    }


    public function support(request $request)
    {


        $set = Setting::where('id', 1)->first();

        $user['payment_support'] = $set->payment_support;
        $user['meter_support'] = $set->meter_support;
        $user['general_support'] = $set->general_support;


        return response()->json([
            'status' => true,
            'data' => $user
        ]);



    }


}
