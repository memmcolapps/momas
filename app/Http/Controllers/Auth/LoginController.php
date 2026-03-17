<?php

namespace App\Http\Controllers\Auth;

use App\Models\Estate;
use App\Models\Setting;
use App\Models\Tariff;
use App\Models\TariffState;
use App\Models\TarrifState;
use App\Models\UtilitiesPayment;
use App\Models\Utitlity;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Meter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Passport;
use App\Models\OauthAccessToken;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //Tariffs state parameters corrected
    // public function login(request $request)
    // {

    //     if($request->email == null) {
    //         $credentials = request(['meterNo', 'password']);
    //         $usr = User::where('meterNo', $request->meterNo)->first() ?? null;
    //         $status = User::where('meterNo', $request->meterNo)->first()->status ?? null;

    //         if($status == 9){
    //             $message = "User does not exist";
    //             $code = 401;
    //             return error($message, $code);
    //         }

    //         if ($usr == null) {
    //             $message = "User does not exist";
    //             $code = 404;
    //             return error($message, $code);
    //         }

    //         Passport::tokensExpireIn(Carbon::now()->addMinutes(20));
    //         Passport::refreshTokensExpireIn(Carbon::now()->addMinutes(20));

    //         if (!auth()->attempt($credentials)) {
    //             $message = "Meter No or Password Incorrect";
    //             $code = 422;
    //             return error($message, $code);
    //         }

    //         flush_token();


    //         //Updated to fetch data using estate id
    //         // $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
    //         //     ->where('user_id', Auth::id())
    //         //     ->get();
    //         // foreach ($tariffs as $tariff) {
    //         //     $tariffState = TarrifState::where('tariff_id', $tariff->id)->first();
    //         //     $tariff->amount = $tariffState ? $tariffState->amount : null;
    //         // }

    //         //Updated to fetch data using estate id
    //         $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
    //             ->where('estate_id', Auth::user()->estate_id)
    //             ->get();
    //         foreach ($tariffs as $tariff) {
    //             $tariffState = TarrifState::where('tariff_id', $tariff->id)
    //                 ->where('amount', '>', 0)
    //                 ->first();
    //             $tariff->amount = $tariffState ? $tariffState->amount : null;
    //         }



    //         $admin_fee_get = UtilitiesPayment::where('user_id', Auth::id())
    //             ->where('type', 'admin_fee')
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->latest('created_at')
    //             ->first();

    //         if($admin_fee_get && $admin_fee_get->status == 2){
    //             $admin_fee =  "1";
    //         }else{
    //             $admin_fee = "0";
    //         }

    //         $ck_utility = UtilitiesPayment::where('user_id', Auth::id())->where('type', 'utilities')->first();
    //         if($ck_utility){






    //         }else{

    //             $utility_amount = Estate::where('id', Auth::user()->estate_id)->first()->total_utility_amount ?? 0;
    //             $duration = Estate::where('id', Auth::user()->estate_id)->first()->duration ?? null;

    //             if($duration == null ){
    //                 $message = "Estate utility duration not set, Contact support";
    //                 $code = 404;
    //                 return error($message, $code);
    //             }



    //             $nextDueDate = Carbon::now();
    //             switch ($duration) {
    //                 case 'weekly':
    //                     $nextDueDate->addWeek();
    //                     break;
    //                 case 'monthly':
    //                     $nextDueDate->addMonth();
    //                     break;
    //                 case 'yearly':
    //                     $nextDueDate->addYear();
    //                     break;
    //                 default:

    //                     $mssage = "Unknown duration '{$duration}'";
    //                     send_notification($mssage);

    //             }

    //             $utli = new UtilitiesPayment();
    //             $utli->estate_id = Auth::user()->estate_id;
    //             $utli->user_id = Auth::id();
    //             $utli->amount = $utility_amount;
    //             $utli->next_due_date = $nextDueDate;
    //             $utli->duration = $duration;
    //             $utli->type = "utilities";
    //             $utli->total_amount = $utility_amount;
    //             $utli->save();

    //         }


    //         //checkAdmin fee
    //         $admin_fee_amount = Setting::where('id', 1)->first()->admin_fee;
    //         $ck_admin_fee = UtilitiesPayment::where('user_id', Auth::id())
    //             ->where('type', 'admin_fee')
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->first();

    //         $ck_admin_fee_status = UtilitiesPayment::where('user_id', Auth::id())
    //             ->where('type', 'admin_fee')
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->first()->status ?? null;

    //         $former_admin_fee_date = UtilitiesPayment::where('user_id', Auth::id())
    //             ->where('type', 'admin_fee')
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->first()->created_at ?? null;


    //         if($ck_admin_fee){

    //         }elseif($ck_admin_fee && $ck_admin_fee_status == 2 ){

    //             $duration = "monthly";
    //             $nextDueDate =  $former_admin_fee_date;
    //             switch ($duration) {
    //                 case 'weekly':
    //                     $nextDueDate->addWeek();
    //                     break;
    //                 case 'monthly':
    //                     $nextDueDate->addMonth();
    //                     break;
    //                 case 'yearly':
    //                     $nextDueDate->addYear();
    //                     break;
    //                 default:
    //                     $mssage = "Unknown duration '{$duration}'";
    //                     send_notification($mssage);

    //             }

    //             $utli = new UtilitiesPayment();
    //             $utli->estate_id = Auth::user()->estate_id;
    //             $utli->user_id = Auth::id();
    //             $utli->amount = $admin_fee_amount;
    //             $utli->next_due_date = $nextDueDate;
    //             $utli->duration = $duration;
    //             $utli->total_amount = $admin_fee_amount;
    //             $utli->type = "admin_fee";
    //             $utli->save();


    //         }elseif($ck_admin_fee && $ck_admin_fee_status == null){

    //         $duration = "monthly";
    //         $nextDueDate =  Carbon::now();
    //         switch ($duration) {
    //             case 'weekly':
    //                 $nextDueDate->addWeek();
    //                 break;
    //             case 'monthly':
    //                 $nextDueDate->addMonth();
    //                 break;
    //             case 'yearly':
    //                 $nextDueDate->addYear();
    //                 break;
    //             default:
    //                 $mssage = "Unknown duration '{$duration}'";
    //                 send_notification($mssage);

    //         }

    //         $utli = new UtilitiesPayment();
    //         $utli->estate_id = Auth::user()->estate_id;
    //         $utli->user_id = Auth::id();
    //         $utli->amount = $admin_fee_amount;
    //         $utli->next_due_date = $nextDueDate;
    //         $utli->duration = $duration;
    //         $utli->total_amount = $admin_fee_amount;
    //         $utli->type = "admin_fee";
    //         $utli->save();


    //     }else{

    //             $duration = "monthly";
    //             $nextDueDate =  Carbon::now();
    //             switch ($duration) {
    //                 case 'weekly':
    //                     $nextDueDate->addWeek();
    //                     break;
    //                 case 'monthly':
    //                     $nextDueDate->addMonth();
    //                     break;
    //                 case 'yearly':
    //                     $nextDueDate->addYear();
    //                     break;
    //                 default:
    //                     $mssage = "Unknown duration '{$duration}'";
    //                     send_notification($mssage);

    //             }

    //             $utli = new UtilitiesPayment();
    //             $utli->estate_id = Auth::user()->estate_id;
    //             $utli->user_id = Auth::id();
    //             $utli->amount = $admin_fee_amount;
    //             $utli->next_due_date = $nextDueDate;
    //             $utli->duration = $duration;
    //             $utli->total_amount = $admin_fee_amount;
    //             $utli->type = "admin_fee";
    //             $utli->save();


    //         }




    //         $token = auth()->user()->createToken('API token')->accessToken;
    //         $meter = meter();
    //         $user = user();
    //         $user['token'] = $token;
    //         $user['meter'] = $meter;
    //         $user['tariff'] = $tariffs;
    //         $user['monthly_admin_fee'] = $admin_fee;






    //         return response()->json([
    //             'status' => true,
    //             'user' => $user
    //         ]);


    //     }











    //     if($request->meterNo == null) {
    //         $credentials = request(['email', 'password']);

    //         $usr = User::where('email', $request->email)->first() ?? null;
    //         $status = User::where('email', $request->email)->first()->status ?? null;
    //         if($status == 9){
    //             $message = "User does not exist";
    //             $code = 401;
    //             return error($message, $code);
    //         }

    //         if ($usr == null) {
    //             $message = "User does not exist";
    //             $code = 404;
    //             return error($message, $code);
    //         }

    //         Passport::tokensExpireIn(Carbon::now()->addMinutes(20));
    //         Passport::refreshTokensExpireIn(Carbon::now()->addMinutes(20));

    //         if (!auth()->attempt($credentials)) {
    //             $message = "Email No or Password Incorrect";
    //             $code = 422;
    //             return error($message, $code);
    //         }

    //         flush_token();


    //         $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
    //             ->where('user_id', Auth::id())
    //             ->get();
    //         foreach ($tariffs as $tariff) {
    //             $tariffState = TarrifState::where('tariff_id', $tariff->id)->first();
    //             $tariff->amount = $tariffState ? $tariffState->amount : null;
    //         }



    //         $admin_fee_get = UtilitiesPayment::where('user_id', Auth::id())
    //             ->where('type', 'admin_fee')
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->latest('created_at')
    //             ->first();

    //         if($admin_fee_get && $admin_fee_get->status == 2){
    //             $admin_fee =  "1";
    //         }else{
    //             $admin_fee = "0";
    //         }

    //         $ck_utility = UtilitiesPayment::where('user_id', Auth::id())->where('type', 'utilities')->first();
    //         if($ck_utility){






    //         }else{

    //             $utility_amount = Estate::where('id', Auth::user()->estate_id)->first()->total_utility_amount ?? 0;
    //             $duration = Estate::where('id', Auth::user()->estate_id)->first()->duration ?? null;

    //             if($duration == null){
    //                 $message = "Estate utility duration not set, Contact support";
    //                 $code = 404;
    //                 return error($message, $code);
    //             }

    //             $nextDueDate = Carbon::now();
    //             switch ($duration) {
    //                 case 'weekly':
    //                     $nextDueDate->addWeek();
    //                     break;
    //                 case 'monthly':
    //                     $nextDueDate->addMonth();
    //                     break;
    //                 case 'yearly':
    //                     $nextDueDate->addYear();
    //                     break;
    //                 default:
    //                     $mssage = "Unknown duration '{$duration}'";
    //                     send_notification($mssage);

    //             }

    //             $utli = new UtilitiesPayment();
    //             $utli->estate_id = Auth::user()->estate_id;
    //             $utli->user_id = Auth::id();
    //             $utli->amount = $utility_amount;
    //             $utli->next_due_date = $nextDueDate;
    //             $utli->duration = $duration;
    //             $utli->type = "utilities";
    //             $utli->total_amount = $utility_amount;
    //             $utli->save();

    //         }


    //         //checkAdmin fee
    //         $admin_fee_amount = Setting::where('id', 1)->first()->admin_fee;
    //         $ck_admin_fee = UtilitiesPayment::where('user_id', Auth::id())
    //             ->where('type', 'admin_fee')
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->first();

    //         $ck_admin_fee_status = UtilitiesPayment::where('user_id', Auth::id())
    //             ->where('type', 'admin_fee')
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->first()->status ?? null;

    //         $former_admin_fee_date = UtilitiesPayment::where('user_id', Auth::id())
    //             ->where('type', 'admin_fee')
    //             ->whereMonth('created_at', Carbon::now()->month)
    //             ->whereYear('created_at', Carbon::now()->year)
    //             ->first()->created_at ?? null;



    //         if($ck_admin_fee){

    //         }elseif($ck_admin_fee && $ck_admin_fee_status == 2){

    //             $duration = "monthly";
    //             $nextDueDate =  $former_admin_fee_date;
    //             switch ($duration) {
    //                 case 'weekly':
    //                     $nextDueDate->addWeek();
    //                     break;
    //                 case 'monthly':
    //                     $nextDueDate->addMonth();
    //                     break;
    //                 case 'yearly':
    //                     $nextDueDate->addYear();
    //                     break;
    //                 default:
    //                     $mssage = "Unknown duration '{$duration}'";
    //                     send_notification($mssage);

    //             }

    //             $utli = new UtilitiesPayment();
    //             $utli->estate_id = Auth::user()->estate_id;
    //             $utli->user_id = Auth::id();
    //             $utli->amount = $admin_fee_amount;
    //             $utli->next_due_date = $nextDueDate;
    //             $utli->duration = $duration;
    //             $utli->total_amount = $admin_fee_amount;
    //             $utli->type = "admin_fee";
    //             $utli->save();


    //         }elseif($ck_admin_fee && $ck_admin_fee_status == null){

    //             $duration = "monthly";
    //             $nextDueDate =  Carbon::now();
    //             switch ($duration) {
    //                 case 'weekly':
    //                     $nextDueDate->addWeek();
    //                     break;
    //                 case 'monthly':
    //                     $nextDueDate->addMonth();
    //                     break;
    //                 case 'yearly':
    //                     $nextDueDate->addYear();
    //                     break;
    //                 default:
    //                     $mssage = "Unknown duration '{$duration}'";
    //                     send_notification($mssage);

    //             }

    //             $utli = new UtilitiesPayment();
    //             $utli->estate_id = Auth::user()->estate_id;
    //             $utli->user_id = Auth::id();
    //             $utli->amount = $admin_fee_amount;
    //             $utli->next_due_date = $nextDueDate;
    //             $utli->duration = $duration;
    //             $utli->total_amount = $admin_fee_amount;
    //             $utli->type = "admin_fee";
    //             $utli->save();


    //         }

    //         else{

    //             $duration = "monthly";
    //             $nextDueDate =  Carbon::now();
    //             switch ($duration) {
    //                 case 'weekly':
    //                     $nextDueDate->addWeek();
    //                     break;
    //                 case 'monthly':
    //                     $nextDueDate->addMonth();
    //                     break;
    //                 case 'yearly':
    //                     $nextDueDate->addYear();
    //                     break;
    //                 default:
    //                     $mssage = "Unknown duration '{$duration}'";
    //                     send_notification($mssage);

    //             }

    //             $utli = new UtilitiesPayment();
    //             $utli->estate_id = Auth::user()->estate_id;
    //             $utli->user_id = Auth::id();
    //             $utli->amount = $admin_fee_amount;
    //             $utli->next_due_date = $nextDueDate;
    //             $utli->duration = $duration;
    //             $utli->total_amount = $admin_fee_amount;
    //             $utli->type = "admin_fee";
    //             $utli->save();


    //         }



    //         $token = auth()->user()->createToken('API token')->accessToken;
    //         $meter = meter();
    //         $user = user();
    //         $user['token'] = $token;
    //         $user['meter'] = $meter;
    //         $user['tariff'] = $tariffs;
    //         $user['monthly_admin_fee'] = $admin_fee;


    //         return response()->json([
    //             'status' => true,
    //             'user' => $user
    //         ]);

    //     }


    // }


        public function login(request $request)
    {

        if($request->email == null) {
            $credentials = request(['meterNo', 'password']);
            $usr = User::where('meterNo', $request->meterNo)->first() ?? null;
            $status = User::where('meterNo', $request->meterNo)->first()->status ?? null;

            if($status == 9){
                $message = "User does not exist";
                $code = 401;
                return error($message, $code);
            }

            if ($usr == null) {
                $message = "User does not exist";
                $code = 404;
                return error($message, $code);
            }

            Passport::tokensExpireIn(Carbon::now()->addMinutes(20));
            Passport::refreshTokensExpireIn(Carbon::now()->addMinutes(20));

            if (!auth()->attempt($credentials)) {
                $message = "Meter No or Password Incorrect";
                $code = 422;
                return error($message, $code);
            }

            flush_token();


            //Updated to fetch data using estate id
            // $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
            //     ->where('user_id', Auth::id())
            //     ->get();
            // foreach ($tariffs as $tariff) {
            //     $tariffState = TarrifState::where('tariff_id', $tariff->id)->first();
            //     $tariff->amount = $tariffState ? $tariffState->amount : null;
            // }

            //Updated to fetch data using estate id
            $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
                ->where('estate_id', Auth::user()->estate_id)
                ->get();
            foreach ($tariffs as $tariff) {
                $tariffState = TarrifState::where('tariff_id', $tariff->id)
                    ->where('amount', '>', 0)
                    ->first();
                $tariff->amount = $tariffState ? $tariffState->amount : null;
            }



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

            $ck_utility = UtilitiesPayment::where('user_id', Auth::id())->where('type', 'utilities')->first();
            $utility_amount = Estate::where('id', Auth::user()->estate_id)->first()->total_utility_amount ?? 0;
            $duration = Estate::where('id', Auth::user()->estate_id)->first()->duration ?? null;

            if($duration == null ){
                $message = "Estate utility duration not set, Contact support";
                $code = 404;
                return error($message, $code);
            }
            if(!$ck_utility && $utility_amount > 0){

                $nextDueDate = Carbon::now();
                switch ($duration) {
                    case 'weekly':
                        $nextDueDate->addWeek();
                        break;
                    case 'monthly':
                        $nextDueDate->addMonth();
                        break;
                    case 'yearly':
                        $nextDueDate->addYear();
                        break;
                    default:

                        $mssage = "Unknown duration '{$duration}'";
                        send_notification($mssage);

                }

                $utli = new UtilitiesPayment();
                $utli->estate_id = Auth::user()->estate_id;
                $utli->user_id = Auth::id();
                $utli->amount = $utility_amount;
                $utli->next_due_date = $nextDueDate;
                $utli->duration = $duration;
                $utli->type = "utilities";
                $utli->total_amount = $utility_amount;
                $utli->save();

            }


            //checkAdmin fee
            $admin_fee_amount = Setting::where('id', 1)->first()->admin_fee;
            $ck_admin_fee = UtilitiesPayment::where('user_id', Auth::id())
                ->where('type', 'admin_fee')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->first();

            $ck_admin_fee_status = UtilitiesPayment::where('user_id', Auth::id())
                ->where('type', 'admin_fee')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->first()->status ?? null;

            $former_admin_fee_date = UtilitiesPayment::where('user_id', Auth::id())
                ->where('type', 'admin_fee')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->first()->created_at ?? null;

            if ($admin_fee_amount > 0) {

                    if($ck_admin_fee){

                    }elseif($ck_admin_fee && $ck_admin_fee_status == 2){

                        $duration = "monthly";
                        $nextDueDate =  $former_admin_fee_date;
                        switch ($duration) {
                            case 'weekly':
                                $nextDueDate->addWeek();
                                break;
                            case 'monthly':
                                $nextDueDate->addMonth();
                                break;
                            case 'yearly':
                                $nextDueDate->addYear();
                                break;
                            default:
                                $mssage = "Unknown duration '{$duration}'";
                                send_notification($mssage);

                        }

                        $utli = new UtilitiesPayment();
                        $utli->estate_id = Auth::user()->estate_id;
                        $utli->user_id = Auth::id();
                        $utli->amount = $admin_fee_amount;
                        $utli->next_due_date = $nextDueDate;
                        $utli->duration = $duration;
                        $utli->total_amount = $admin_fee_amount;
                        $utli->type = "admin_fee";
                        $utli->save();


                    }elseif($ck_admin_fee && $ck_admin_fee_status == null){

                    $duration = "monthly";
                    $nextDueDate =  Carbon::now();
                    switch ($duration) {
                        case 'weekly':
                            $nextDueDate->addWeek();
                            break;
                        case 'monthly':
                            $nextDueDate->addMonth();
                            break;
                        case 'yearly':
                            $nextDueDate->addYear();
                            break;
                        default:
                            $mssage = "Unknown duration '{$duration}'";
                            send_notification($mssage);

                    }

                    $utli = new UtilitiesPayment();
                    $utli->estate_id = Auth::user()->estate_id;
                    $utli->user_id = Auth::id();
                    $utli->amount = $admin_fee_amount;
                    $utli->next_due_date = $nextDueDate;
                    $utli->duration = $duration;
                    $utli->total_amount = $admin_fee_amount;
                    $utli->type = "admin_fee";
                    $utli->save();


                }else{
                        $duration = "monthly";
                        $nextDueDate =  Carbon::now();
                        switch ($duration) {
                            case 'weekly':
                                $nextDueDate->addWeek();
                                break;
                            case 'monthly':
                                $nextDueDate->addMonth();
                                break;
                            case 'yearly':
                                $nextDueDate->addYear();
                                break;
                            default:
                                $mssage = "Unknown duration '{$duration}'";
                                send_notification($mssage);

                        }

                        $utli = new UtilitiesPayment();
                        $utli->estate_id = Auth::user()->estate_id;
                        $utli->user_id = Auth::id();
                        $utli->amount = $admin_fee_amount;
                        $utli->next_due_date = $nextDueDate;
                        $utli->duration = $duration;
                        $utli->total_amount = $admin_fee_amount;
                        $utli->type = "admin_fee";
                        $utli->save();


                    }
            }


            $token = auth()->user()->createToken('API token')->accessToken;
            $meter = meter();
            $user = user();
            $user['token'] = $token;
            $user['meter'] = $meter;
            $user['tariff'] = $tariffs;
            $user['monthly_admin_fee'] = $admin_fee;
            $user['meter_status'] = $meter->status;

            Log::info('LOGIN DEBUG START', [

                // Core identity
                'meterNo' => $request->meterNo ?? null,
                'user_status' => $status ?? null,
                'meter_status' => $meter->status,

                // Tariffs
                'tariffs_exist' => isset($tariffs) && $tariffs->count() > 0 ? 'exists' : 'null',

                // Admin fee checks
                'admin_fee_get_exist' => $admin_fee_get ? 'exists' : 'null',
                'admin_fee_get_status' => $admin_fee_get->status ?? null,
                'admin_fee_flag' => $admin_fee ?? null,

                'admin_fee_amount' => $admin_fee_amount ?? null,
                'ck_admin_fee_exist' => $ck_admin_fee ? 'exists' : 'null',
                'ck_admin_fee_status' => $ck_admin_fee_status ?? null,
                'former_admin_fee_date' => $former_admin_fee_date ?? null,

                // Utility checks
                'ck_utility_exist' => $ck_utility ? 'exists' : 'null',
                'utility_amount' => $utility_amount ?? null,
                'utility_duration' => $duration ?? null,
                'utility_next_due_date' => isset($nextDueDate) ? $nextDueDate->toDateTimeString() : null,

                // Objects
                'meter_object' => $meter ? 'exists' : 'null',
                'user_object' => $user ? 'exists' : 'null',
            ]);

            Log::info('LOGIN DEBUG END');


            return response()->json([
                'status' => true,
                'user' => $user
            ]);
        }











        if($request->meterNo == null) {
            $credentials = request(['email', 'password']);

            $usr = User::where('email', $request->email)->first() ?? null;
            $status = User::where('email', $request->email)->first()->status ?? null;
            if($status == 9){
                $message = "User does not exist";
                $code = 401;
                return error($message, $code);
            }

            if ($usr == null) {
                $message = "User does not exist";
                $code = 404;
                return error($message, $code);
            }

            Passport::tokensExpireIn(Carbon::now()->addMinutes(20));
            Passport::refreshTokensExpireIn(Carbon::now()->addMinutes(20));

            if (!auth()->attempt($credentials)) {
                $message = "Email No or Password Incorrect";
                $code = 422;
                return error($message, $code);
            }

            flush_token();


            //Updated to fetch data using estate id
            // $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
            //     ->where('user_id', Auth::id())
            //     ->get();
            // foreach ($tariffs as $tariff) {
            //     $tariffState = TarrifState::where('tariff_id', $tariff->id)->first();
            //     $tariff->amount = $tariffState ? $tariffState->amount : null;
            // }

            //Updated to fetch data using estate id
            $tariffs = Tariff::select('id', 'type', 'estate_id', 'title')
                ->where('estate_id', Auth::user()->estate_id)
                ->get();
            foreach ($tariffs as $tariff) {
                $tariffState = TarrifState::where('tariff_id', $tariff->id)
                    ->where('amount', '>', 0)
                    ->first();
                $tariff->amount = $tariffState ? $tariffState->amount : null;
            }




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

            $ck_utility = UtilitiesPayment::where('user_id', Auth::id())->where('type', 'utilities')->first();
            $utility_amount = Estate::where('id', Auth::user()->estate_id)->first()->total_utility_amount ?? 0;
            $duration = Estate::where('id', Auth::user()->estate_id)->first()->duration ?? null;

            if($duration == null){
                $message = "Estate utility duration not set, Contact support";
                $code = 404;
                return error($message, $code);
                }

            if(!$ck_utility && $utility_amount > 0){

                $nextDueDate = Carbon::now();
                switch ($duration) {
                    case 'weekly':
                        $nextDueDate->addWeek();
                        break;
                    case 'monthly':
                        $nextDueDate->addMonth();
                        break;
                    case 'yearly':
                        $nextDueDate->addYear();
                        break;
                    default:
                        $mssage = "Unknown duration '{$duration}'";
                        send_notification($mssage);

                }

                $utli = new UtilitiesPayment();
                $utli->estate_id = Auth::user()->estate_id;
                $utli->user_id = Auth::id();
                $utli->amount = $utility_amount;
                $utli->next_due_date = $nextDueDate;
                $utli->duration = $duration;
                $utli->type = "utilities";
                $utli->total_amount = $utility_amount;
                $utli->save();

            }


            //checkAdmin fee
            $admin_fee_amount = Setting::where('id', 1)->first()->admin_fee;
            $ck_admin_fee = UtilitiesPayment::where('user_id', Auth::id())
                ->where('type', 'admin_fee')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->first();

            $ck_admin_fee_status = UtilitiesPayment::where('user_id', Auth::id())
                ->where('type', 'admin_fee')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->first()->status ?? null;

            $former_admin_fee_date = UtilitiesPayment::where('user_id', Auth::id())
                ->where('type', 'admin_fee')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->first()->created_at ?? null;


            if ($admin_fee_amount > 0) {
                if($ck_admin_fee){

                }elseif($ck_admin_fee && $ck_admin_fee_status == 2){

                    $duration = "monthly";
                    $nextDueDate =  $former_admin_fee_date;
                    switch ($duration) {
                        case 'weekly':
                            $nextDueDate->addWeek();
                            break;
                        case 'monthly':
                            $nextDueDate->addMonth();
                            break;
                        case 'yearly':
                            $nextDueDate->addYear();
                            break;
                        default:
                            $mssage = "Unknown duration '{$duration}'";
                            send_notification($mssage);

                    }

                    $utli = new UtilitiesPayment();
                    $utli->estate_id = Auth::user()->estate_id;
                    $utli->user_id = Auth::id();
                    $utli->amount = $admin_fee_amount;
                    $utli->next_due_date = $nextDueDate;
                    $utli->duration = $duration;
                    $utli->total_amount = $admin_fee_amount;
                    $utli->type = "admin_fee";
                    $utli->save();


                }elseif($ck_admin_fee && $ck_admin_fee_status == null){

                    $duration = "monthly";
                    $nextDueDate =  Carbon::now();
                    switch ($duration) {
                        case 'weekly':
                            $nextDueDate->addWeek();
                            break;
                        case 'monthly':
                            $nextDueDate->addMonth();
                            break;
                        case 'yearly':
                            $nextDueDate->addYear();
                            break;
                        default:
                            $mssage = "Unknown duration '{$duration}'";
                            send_notification($mssage);

                    }

                    $utli = new UtilitiesPayment();
                    $utli->estate_id = Auth::user()->estate_id;
                    $utli->user_id = Auth::id();
                    $utli->amount = $admin_fee_amount;
                    $utli->next_due_date = $nextDueDate;
                    $utli->duration = $duration;
                    $utli->total_amount = $admin_fee_amount;
                    $utli->type = "admin_fee";
                    $utli->save();


                }

                else{

                    $duration = "monthly";
                    $nextDueDate =  Carbon::now();
                    switch ($duration) {
                        case 'weekly':
                            $nextDueDate->addWeek();
                            break;
                        case 'monthly':
                            $nextDueDate->addMonth();
                            break;
                        case 'yearly':
                            $nextDueDate->addYear();
                            break;
                        default:
                            $mssage = "Unknown duration '{$duration}'";
                            send_notification($mssage);

                    }

                    $utli = new UtilitiesPayment();
                    $utli->estate_id = Auth::user()->estate_id;
                    $utli->user_id = Auth::id();
                    $utli->amount = $admin_fee_amount;
                    $utli->next_due_date = $nextDueDate;
                    $utli->duration = $duration;
                    $utli->total_amount = $admin_fee_amount;
                    $utli->type = "admin_fee";
                    $utli->save();


                }

            }

            $token = auth()->user()->createToken('API token')->accessToken;
            $meter = meter();
            $user = user();
            $user['token'] = $token;
            $user['meter'] = $meter;
            $user['tariff'] = $tariffs;
            $user['monthly_admin_fee'] = $admin_fee;

             Log::info('LOGIN DEBUG START', [

                // Core identity
                'meterNo' => $request->meterNo ?? null,
                'user_status' => $status ?? null,

                // Tariffs
                'tariffs_exist' => isset($tariffs) && $tariffs->count() > 0 ? 'exists' : 'null',

                // Admin fee checks
                'admin_fee_get_exist' => $admin_fee_get ? 'exists' : 'null',
                'admin_fee_get_status' => $admin_fee_get->status ?? null,
                'admin_fee_flag' => $admin_fee ?? null,

                'admin_fee_amount' => $admin_fee_amount ?? null,
                'ck_admin_fee_exist' => $ck_admin_fee ? 'exists' : 'null',
                'ck_admin_fee_status' => $ck_admin_fee_status ?? null,
                'former_admin_fee_date' => $former_admin_fee_date ?? null,

                // Utility checks
                'ck_utility_exist' => $ck_utility ? 'exists' : 'null',
                'utility_amount' => $utility_amount ?? null,
                'utility_duration' => $duration ?? null,
                'utility_next_due_date' => isset($nextDueDate) ? $nextDueDate->toDateTimeString() : null,

                // Objects
                'meter_object' => $meter ? 'exists' : 'null',
                'user_object' => $user ? 'exists' : 'null',
            ]);

            Log::info('LOGIN DEBUG END');


            return response()->json([
                'status' => true,
                'user' => $user
            ]);

        }

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
