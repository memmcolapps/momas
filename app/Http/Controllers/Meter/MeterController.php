<?php

namespace App\Http\Controllers\Meter;

use App\Contracts\PaymentServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Transaction\TransactionController;
use App\Models\CreditToken;
use App\Models\Estate;
use App\Models\KctMeterToken;
use App\Models\Logger;
use App\Models\Meter;
use App\Models\MeterRequest;
use App\Models\MeterToken;
use App\Models\Tariff;
use App\Models\TarrifState;
use App\Models\Transaction;
use App\Models\Transformer;
use App\Models\User;
use App\Models\UtilitiesPayment;
use App\Models\Utitlity;
use App\Services\StandardResponse;
use App\Services\TokenGenerationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MeterController extends Controller
{


    public function get_estate_tariff(request $request)
    {

        // $tariff = Tariff::where('estate_id', $request->estate_id)->get();
        // return response()->json([
        //     'tariffs' => $tariff
        // ]);

        $tariffs = Tariff::where('estate_id', $request->estate_id)
                    ->select('id', 'title', 'type', 'tariff_index')
                    ->get();

        return response()->json([
        'tariffs' => $tariffs
        ]);

    }


    public function searchMeters(request $request)
    {
        $query = $request->get('q');
        $meters = Meter::where('meterNo', 'LIKE', '%' . $query . '%')
            ->where([
                'user_id' => null,
                'estate_id' => $request->estate_id
            ])->get();


        return response()->json($meters);
    }


    public function searchMeter(request $request)
    {
        $query = $request->get('q');
        $meters = Meter::where('meterNo', 'LIKE', '%' . $query . '%')->where('user_id', null)->get();
        return response()->json($meters);
    }


    public function validate_mobile_meter(request $request)
    {
        $user = User::where('meterNo', $request->meterNo)->first() ?? null;
        $get_user_estate_id = User::where('meterNo', $request->meterNo)->first()->estate_id ?? null;


        $meter = Meter::where('meterNo', $request->meterNo)->where('estate_id', $request->estateId)->first() ?? null;
        if ($meter == null) {
            $message = "Validation Failed, please check meter number or estate selected";
            $code = 422;
            return error($message, $code);
        }


        if (! $meter->isActive()) {

            return StandardResponse::error(403, 'Meter Blocked', []);
        }


        if($get_user_estate_id == null){
            $message = "Meter is not properly attached to a customer or an estate";
            $code = 422;
            return error($message, $code);

        }



        $get_tar = Tariff::where('estate_id', $get_user_estate_id)->where('status', 2)->first() ?? null;
        if ($get_tar == null) {
            $message = "Tariff not properly configured";
            $code = 422;
            return error($message, $code);
        }


        // $data['meter_type'] = $meter_type;

        $es_id = $request->estateId ?? null;
        $duration = Estate::where('id', $es_id)->first()->duration ?? null;
        $estate_id = $es_id;
        $user_id = $user->id;


        if ($duration == null || $estate_id == null) {
            $minvend = "Not set";
        } else {


            $get_vend = vend($duration, $estate_id, $user_id);



            if ($get_vend == null) {
                $minvend = "Not set";
            } else {
                $minvend = $get_vend;
            }


        }

        $min_pur = Estate::where('id', $request->estateId)->first()->min_pur ?? null;
        $max_pur = Estate::where('id', $request->estateId)->first()->max_pur ?? null;
        $data['min_purchase'] = (int)$min_pur;
        $user_info = User::where('meterNo', $request->meterNo)->first();
        $estate_id = $user_info->estate_id ?? null;
        if ($estate_id == null) {

            $message = "User not attached to any estate";
            $code = 422;
            return error($message, $code);

        }
        $title = Tariff::where('estate_id', $user_info->estate_id)->first()->title ?? null;

        if ($title == null) {

            $message = "Set a tariff for estate selected";
            $code = 422;
            return error($message, $code);

        }


        $tariffs = Tariff::where('estate_id', $user_info->estate_id)->get();


        $data['customer_name'] = $user->first_name . " " . $user->last_name;
        $data['address'] = $user->address . ", " . $user->city . ", " . $user->state;
        $data['tariffs'] = $tariffs;

        // Properly retrieve amount and VAT from TarrifState for each tariff
        foreach ($tariffs as $tariff) {
            $tariffState = TarrifState::where('tariff_id', $tariff->id)->where('status', 2)->first();
            $tariff->amount = $tariffState ? $tariffState->amount : null;
            $tariff->vat = $tariffState ? $tariffState->vat : null;
        }


        $pur['min_purchase'] = (int)$min_pur;
        $pur['max_purchase'] = (int)$max_pur;
        $pur['min_vending'] = (int)$minvend;
        $data['purchase'] = $pur;

        return response()->json([
            'status' => true,
            'data' => $data

        ]);


    }


    public function validate_meter(request $request)
    {



        $user = User::where('meterNo', $request->meterNo)->first() ?? null;


        $meter = Meter::where('meterNo', $request->meterNo)->where('estate_id', $request->estateId)->first() ?? null;
        if ($meter == null) {
            $message = "Validation Failed, please check meter number or estate selected";
            $code = 422;
            return error($message, $code);
        }


        $get_tar = Tariff::where('estate_id', $user->estate_id)->where('status', 2)->first() ?? null;
        if ($get_tar == null) {
            $message = "Tariff not properly configured";
            $code = 422;
            return error($message, $code);
        }


        $data['customer_name'] = $user->first_name . " " . $user->last_name;
        $data['address'] = $user->address . ", " . $user->city . ", " . $user->state;

        $es_id = $request->estateId ?? null;
        $duration = Estate::where('id', $es_id)->first()->duration ?? null;
        $estate_id = $es_id;
        $user_id = $user->id;


        if ($duration == null || $estate_id == null) {
            $minvend = "Not set";
        } else {

            $get_vend = vend($duration, $estate_id, $user_id);


            if ($get_vend == null) {
                $minvend = "Not set";
            } else {
                $minvend = $get_vend;
            }

        }



        $min_pur = Estate::where('id', $request->estateId)->first()->min_pur ?? null;
        $max_pur = Estate::where('id', $request->estateId)->first()->max_pur ?? null;
        $data['min_purchase'] = (int)$min_pur;
        $user_info = User::where('meterNo', $request->meterNo)->first();
        $estate_id = $user_info->estate_id ?? null;
        if ($estate_id == null) {
            return back()->with('error', "User not attached to any estate");
        }
        $title = Tariff::where('estate_id', $user_info->estate_id)->first()->title ?? null;
        if ($title == null) {
            return back()->with('error', "Set a tariff for estate selected");
        }

        $tariff_index = User::where('id', $user_info->tariffid)->first()->tariff_index ?? null;
        if ($tariff_index == null) {
            return back()->with('error', "Tariff Index not set for Customer");
        }

        $get_tariffs = Tariff::where('user_id', $user_info->id)->first() ?? null;
        if ($get_tariffs == null) {
            $tarf = new Tariff();
            $tarf->title = $title;
            $tarf->tariff_index = $user_info->tariffid;
            $tarf->estate_id = $user_info->estate_id;
            $tarf->user_id = $user_info->id;
            $tarf->type = $user_info->source;
            $tarf->status = 1;
            $tarf->save();
        }

        $tariffs = Tariff::where('user_id', $user_info->id)->get();

        $data['tariffs'] = $tariffs;
        $pur['min_purchase'] = (int)$min_pur;
        $pur['max_purchase'] = (int)$max_pur;
        $pur['min_vending'] = (int)$minvend;
        $data['purchase'] = $pur;


        return response()->json([
            'status' => true,
            'data' => $data

        ]);


    }

    public function calculate_token_fees(request $request) {

        $auth_user = Auth::user();

        $validator = Validator::make($request->all(), [
            'tariff_id' => ['required', 'numeric', Rule::exists('tariff_states', 'tariff_id')
                                                        ->where('estate_id', $auth_user->estate_id)
                                                        ->where('status', 2)
            ],
            'trx_id' => ['required', 'string', Rule::exists('transactions', 'trx_id')
                                                    ->where('user_id', $auth_user->id)
                                                    ->where('status', 3)
            ],

        ]);

        if ($validator->fails()) {

            return StandardResponse::error(422, 'Validation Error', [
                'validation_error' => $validator->errors(),
            ]);
        }

        $meter = meter();

        if (!$meter->isActive()) {

            Logger::error("User $auth_user->id failed to calculate meter fees for token due to blocked meter", [
                'user_id' => $auth_user->id,
                'meter' => $meter,
                'trx_id' => $request->trx_id,
            ]);

            return StandardResponse::error(401, 'Meter blocked please reach out to estate admin for support', [], [
                'user' => $auth_user,
                'meter' => $meter,
            ]);

        }


        $trx = Transaction::where('trx_id', $request->trx_id)->first();

        $values = $meter->calculateTokenValues($request->tariff_id, $trx);
    }

    public function buy_meter_token(request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tariff_id' => 'required|integer|exists:tariffs,id',
                'trxref' => 'required_without:ref|string',
                'ref' => 'required_without:trxref|string',
            ]);

            if ($validator->fails()) {
                return StandardResponse::error(code: 422, message: 'Validation error', data: [
                    'validation_error' => $validator->errors()
                ]);
            }


            $trx_id = $request->trxref ?? $request->ref;
            $tariff_id = $request->tariff_id;
            $utility_amount = $request->utility_amount ?? 0;
            $auth_user = Auth::user();

            if (!$trx_id) {
                return StandardResponse::error(422, "Transaction reference missing");
            }


            // ============================
            // Check Existing Transaction
            // ============================

            $trx = Transaction::where('trx_id', $trx_id)->where('user_id', Auth::user()->id)->first();

            if (!$trx) {
                return StandardResponse::error(404, 'Resource not found: Invalid transaction reference', []);
            }

            if ($trx->status === 1) {
                return StandardResponse::error(403, 'Transaction yet to be verified or failed', []);
            }

            if ($trx->status === 2) {
                // Transaction already completed - return existing receipt
                $existingCredit = CreditToken::where('trx_id', $trx_id)->first();
                $receipt = TransactionController::getReceiptData($trx->id, Auth::user()->id);

                return StandardResponse::success(200, 'Transaction has previously been completed', [
                    'receipt' => $receipt,
                ]);
            }

            if ($trx->status === 0) {
                $verifier = app()->makeWith(PaymentServiceInterface::class, ['provider' => $trx->pay_type]);
                $verifier->verifyTransaction($trx->trx_id);

                if (! $verifier['is_successful']) {

                    $trx->status = 1;
                    $trx->save();
                    Logger::warning("User {$auth_user->id} tried buying a token with a failed transaction {$trx->trx_id}");

                    return StandardResponse::error(403, 'Payment failed please try again', []);
                }
            }

            $meter = Meter::where('meterNo', Auth::user()->meterNo)
                ->where('estate_id', Auth::user()->estate_id)
                ->first();

            if (!$meter) {
                return StandardResponse::error(404, "Meter not found");
            }

            if (! $meter->isActive()) {
                return StandardResponse::error(403, 'Meter unable to perform this action reach out to your estate admin for support', []);
            }

            $estate = Estate::where('id', Auth::user()->estate_id)->first();
            $duration = $estate->duration ?? null;

            // ============================
            // Optional Utility Settlement
            // ============================

            if ($utility_amount > 0 && in_array($duration, ["weekly", "monthly", "yearly"])) {
                handle_pay_arrears($trx_id, Auth::user()->id, 'utilities');
            }

            // ============================
            // Call getNewToken
            // ============================

            // Using "null" for verify since transaction is already verified
            $meter->getNewToken(
                $tariff_id,
                $trx_id,
                "null",
                '',
                'momas_meter'
            );

            // Get the created CreditToken to retrieve the token
            $credit = CreditToken::where('trx_id', $trx_id)->first();
            $token = $credit->token ?? null;
            $total_paid = $trx->amount;

            // ============================
            // Receipt
            // ============================

            $receipt = [
                'full_name'               => Auth::user()->first_name . " " . Auth::user()->last_name,
                'trx_id'                  => $trx_id,
                'meterNo'                 => $meter->meterNo,
                'token'                   => $token,
                'amount'                  => (string) $total_paid,
                'estate'                  => $estate->title ?? null,
                'vending_amount'          => (string) round($credit->amount ?? 0, 2),
                'vat_amount'              => (string) round($credit->vatAmount ?? 0, 2),
                'vend_amount_kw_per_naira'=> (string) round($credit->unitkwh ?? 0, 2),
                'status'                  => $credit->status ?? 2
            ];

            return StandardResponse::success(code: 200, message: 'Bought token successfully', data:[
                'receipt' => $receipt,
            ]);

        } catch (Exception $e) {
            Logger::error('MeterController error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return StandardResponse::error(code: 500, message: 'An Error Occured', debug: [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }


    public function retry_meter_token(request $request)
    {

        $amount = $request->amount;
        $meterNo = $request->meterNo;
        $trx_id = $request->trxref;


        $trx = Transaction::where('trx_id', $trx_id)->first();
        if ($trx) {
            return response()->json([
                'status' => false,
                'message' => "Transaction not found, contact our support for more support",
            ], 422);
        }

        if ($trx->status == 2) {
            return response()->json([
                'status' => false,
                'message' => "Transaction already successful, contact our support for more support",
            ], 422);

        }


        $user_wallet = User::where('id', $trx->user_id)->first()->main_wallet;
        if($user_wallet < $trx->amount){
            return response()->json([
                'status' => false,
                'message' => "Insufficient Funds to retry vending",
            ], 422);
        }



        $tariff_index = Tariff::where('id', $trx->tariff_id)->first()->tariff_index ?? null;
        $user = User::where('id', $trx->user_id)->first();
        $meter = Meter::where('user_id', $trx->user_id)->first();
        $need_kct = $meter->NeedKCT;

        $token_gen = TokenGenerationService::generateMeterToken($meter, $tariff_index, $unit, $need_kct);



        if ($meter != null && $meter->NeedKCT == "on") {
            $databody = [
                'meterType' => $meter->KRN2,
                'meterNo' => $meter->meterNo,
                'sgc' => (int)$meter->NewSGC,
                'ti' => $tariff_index, //TRARRRIF INDEX
                'amount' => $trx->unit_amount,
            ];
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
            ])->post('http://169.239.189.91:19071/tokenGen', $databody);

            if ($response->successful()) {
                $gdata = $response->json();
                $data = json_decode($gdata, true);
                $status = $data['code'] ?? null;

                if ($status == "SUCCESS") {

                    $token = $data['tokens'][0];

                    $kctdatabody = [
                        'meterType' => $meter->KRN1,
                        'tometerType' => $meter->KRN1,
                        'meterNo' => $meter->meterNo,
                        'sgc' => (int)$meter->OldSGC,
                        'tosgc' => (int)$meter->NewSGC,
                        'ti' => $tariff_index,
                        'toti' => 1,
                        'allow' => false,
                        'allowkrn' => true,
                    ];

                    $kct_response = Http::withOptions([
                        'verify' => false,
                        'timeout' => 10,
                    ])->post('http://169.239.189.91:19071/kcttokenGen', $kctdatabody);

                    if ($kct_response->successful()) {
                        $kct = $kct_response->json();
                        $kct_data = json_decode($kct, true);
                        $status = $kct_data['code'] ?? null;

                        if ($status == "SUCCESS") {

                            $met = new MeterToken ();
                            $met->user_id = $trx->user_id;
                            $met->trx_id = $trx->trx_id;
                            $met->meterNo = $meter->meterNo;
                            $met->token = $token;
                            $met->amount = $trx->amount;
                            $met->unit = $trx->unit_amount;
                            $met->kct_tokens = $kct_data['tokens'][0] . "," . $kct_data['tokens'][1];
                            $met->vat = $trx->vat;
                            $met->estate_id = $user->estate_id;
                            $met->status = 2;
                            $met->save();

                            Transaction::where('trx_id', $trx_id)->update(['status' => 2, 'note' => "Tokens generated successfully"]);


                            $data2['full_name'] = $user->first_name . " " . $user->last_name;
                            $data2['address'] = $user->address . "," . $user->city . "," . $user->state;
                            $data2['service'] = "MOMAS METER";
                            $data2['trx_id'] = $trx_id;
                            $data2['token'] = $token;
                            $data2['amount'] = "$trx->amount";
                            $data2['vending_amount'] = "$trx->vending_amount";
                            $data2['vend_amount_kw_per_naira'] = "$trx->unit_amount";
                            $data2['kct_token1'] = $kct_data['tokens'][0];
                            $data2['kct_token2'] = $kct_data['tokens'][1];
                            $data2['vat_amount'] = "$trx->vat";


                            $email = $user->email;
                            $kct_token = $kct_data['tokens'];
                            $kct_token1 = $kct_token[0];
                            $kct_token2 = $kct_token[1];


                            User::where('id', $user->id)->decrement('main_wallet', $trx->amount);


                            send_kct_email_token($email, $token, $amount, $kct_token1, $kct_token2);

                            return response()->json([
                                'status' => true,
                                'data' => $data2
                            ], 200);


                        }
                    } else {


                    }

                }


            } else {

                return response()->json([
                    'status' => false,
                    'message' => "Meter vending failed, Retry again on transaction history"
                ], 422);

            }

        }


        if ($meter != null && $meter->NeedKCT == null) {

            $databody = [
                'meterType' => $meter->KRN2,
                'meterNo' => $meter->meterNo,
                'sgc' => (int)$meter->NewSGC,
                'ti' => $tariff_index,
                'amount' => $trx->unit_amount,
            ];
            $no_kct_response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
            ])->post('http://169.239.189.91:19071/tokenGen', $databody);


            if ($no_kct_response->successful()) {
                $no_kct = $no_kct_response->json();
                $no_kct_data = json_decode($no_kct, true);
                $status = $no_kct_data['code'] ?? null;

                if ($status == "SUCCESS") {

                    $no_kct_token = $no_kct_data['tokens'][0];
                    $met = new MeterToken ();
                    $met->user_id = $trx->user_id;
                    $met->trx_id = $trx->trx_id;
                    $met->meterNo = $meter->meterNo;
                    $met->token = $no_kct_token;
                    $met->amount = $trx->amount;
                    $met->unit = $trx->unit_amount;
                    $met->vat = $trx->vat;
                    $met->estate_id = $user->estate_id;
                    $met->status = 2;
                    $met->save();

                    Transaction::where('trx_id', $trx_id)->update(['status' => 2, 'note' => "Tokens generated successfully"]);


                    $data['full_name'] = $user->first_name . " " . $user->last_name;
                    $data['address'] = $user->address . "," . $user->city . "," . $user->state;
                    $data['service'] = "MOMAS METER";
                    $data['trx_id'] = $trx_id;
                    $data['token'] = $no_kct_token;
                    $data['amount'] = "$trx->amount";
                    $data['vending_amount'] = "$trx->vending_amount";
                    $data['vend_amount_kw_per_naira'] = "$trx->unit_amount";
                    $data['vat_amount'] = "$trx->vat";

                    $email = $user->email;

                    send_email_token($email, $no_kct_token, $amount);

                    User::where('id', $user->id)->decrement('main_wallet', $trx->amount);


                    return response()->json([
                        'status' => true,
                        'data' => $data
                    ], 200);


                } else {


                    Transaction::where('trx_id', $trx_id)->update([
                        'service_type' => "token Purchase",
                        'service' => "Meter",
                        'status' => 3,
                        'note' => json_encode($no_kct_response)
                    ]);

                    return response()->json([
                        'status' => false,
                        'message' => "Meter vending failed, Retry again on transaction history"
                    ], 422);


                }


            }


        }

        return response()->json([

            'status' => false,
            'message' => "Something went wrong, Contact our support",
        ], 422);


    }


    public function pay_for_others_meter_token(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'meter_no' => 'required|string|exists:meters,meterNo',
                'total_paid_amount' => 'required|numeric|min:1',
                'vend_amount_kw_per_naira' => 'required|numeric|min:0',
                'vending_amount' => 'required|numeric|min:1',
                'vat_amount' => 'required|numeric|min:0',
                'tariff_id' => 'required|integer|exists:tariffs,id',
                'trxref' => 'required|string',
                'ref' => 'nullable|string',
                'utility_amount' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return StandardResponse::error(
                    code: 422,
                    message: 'Validation error',
                    data: ['validation_error' => $validator->errors()]
                );
            }

            // ============================
            // Use Gateway Reference
            // ============================

            $trx_id = $request->trxref ?? $request->ref;

            if (!$trx_id) {
                return StandardResponse::error(422, "Transaction reference missing");
            }

            $buyer = Auth::user();

            $receiver_meterNo        = $request->meter_no;
            $total_paid     = $request->total_paid_amount;
            $unit           = $request->vend_amount_kw_per_naira;
            $vending_amount = $request->vending_amount;
            $vat_amount     = $request->vat_amount;
            $tariff_id      = $request->tariff_id;
            $utility_amount = $request->utility_amount ?? 0;

            // $auth_user = Auth::user();

            $meter = Meter::where('meterNo', $receiver_meterNo)
                ->where('estate_id', Auth::user()->estate_id)
                ->first();
            if (!$meter) {
                return StandardResponse::error(404, "Meter not found Or Meter is not a part of your estate");
            }

            $estate       = Estate::where('id', $meter->estate_id)->first();
            $tariff_index = Tariff::where('id', $tariff_id)->value('tariff_index');

            if (!$tariff_index) {
                return StandardResponse::error(422, "Invalid tariff configuration");
            }

            // ============================
            // Check Existing Transaction
            // ============================

            $trx_history = Transaction::where('trx_id', $trx_id)
                ->where('user_id', $buyer->id)
                ->first();

            if (!$trx_history) {
                return StandardResponse::error(404, 'Resource not found: Invalid transaction reference');
            }

            if ($trx_history->status === 0 || $trx_history->status === 1) {
                return StandardResponse::error(403, 'Transaction yet to be verified or failed');
            }

            if ($trx_history->status === 2) {
                // Transaction already completed - return existing receipt
                $existingCredit = CreditToken::where('trx_id', $trx_id)->first();
                $receipt = [
                    'full_name'               => $buyer->first_name . " " . $buyer->last_name,
                    'trx_id'                  => $trx_id,
                    'meterNo'                 => $buyer->meterNo,
                    'receiver_meterNo'        => $receiver_meterNo,
                    'token'                   => $existingCredit->token ?? null,
                    'amount'                  => (string) $total_paid,
                    'estate'                  => $estate->title ?? null,
                    'vending_amount'          => (string) round($vending_amount, 2),
                    'vat_amount'              => (string) round($vat_amount, 2),
                    'vend_amount_kw_per_naira'=> (string) round($unit, 2),
                    'status'                  => 2
                ];

                return StandardResponse::success(200, 'Transaction has previously been completed', [
                    'receipt' => $receipt,
                ]);
            }

            // ============================
            // Optional Utility Settlement
            // ============================

            $paid_utility = false;

            if ($utility_amount > 0) {
                handle_pay_arrears($trx_id, $buyer->id, 'utilities');

                $paid_utility = true;
            }

            // ============================
            // Call getNewToken with receiver_meterNo
            // ============================


            // Use the buyer's meter to call getNewToken, passing receiver's meter number
            $buyerMeter = Meter::where('meterNo', Auth::user()->meterNo)
                ->where('estate_id', Auth::user()->estate_id)
                ->first();

            if (!$buyerMeter) {
                return StandardResponse::error(404, "Buyer meter not found");
            }

            // dd($buyerMeter->toArray(), Auth::user()->toArray());

            // Call getNewToken with receiver_meterNo as the last argument
            // Using "null" for verify since transaction is already verified
            $buyerMeter->getNewToken(
                $tariff_id,
                $trx_id,
                "null",
                $receiver_meterNo,
                'momas_meter'
            );

            // Get the created CreditToken to retrieve the token
            $credit = CreditToken::where('trx_id', $trx_id)->first();
            $token = $credit->token ?? null;

            // ============================
            // Receipt
            // ============================

            $receipt = [
                'full_name'               => $buyer->first_name . " " . $buyer->last_name,
                'trx_id'                  => $trx_id,
                'meterNo'                 => $buyerMeter->meterNo,
                'receiver_meterNo'        => $receiver_meterNo,
                'token'                   => $token,
                'amount'                  => (string) $total_paid,
                'estate'                  => $estate->title ?? null,
                'vending_amount'          => (string) round($vending_amount, 2),
                'vat_amount'              => (string) round($vat_amount, 2),
                'vend_amount_kw_per_naira'=> (string) round($unit, 2),
                'status'                  => 2
            ];

            return StandardResponse::success(
                code: 200,
                message: "Purchased token for another meter successfully",
                data: ['receipt' => $receipt]
            );

        } catch (\Exception $e) {

            return StandardResponse::error(
                code: 500,
                message: 'An error occurred',
                debug: [
                    'error' => $e->getMessage(),
                    'line'  => $e->getLine(),
                    'file'  => $e->getFile(),
                ]
            );
        }
    }




    public
    function reprint_meter_token(request $request)
    {

        $token = MeterToken::where('status', 2)->where('user_id', Auth::id())->get();
        return response()->json([
            'status' => true,
            'data' => $token
        ], 200);


    }


    public
    function get_token(request $request)
    {


        $data['token'] = MeterToken::where('id', $request->token_id)->get();
        $data['full_name'] = Auth::user()->first_name . " " . Auth::user()->last_name;
        $data['address'] = Auth::user()->address . "," . Auth::user()->city . "," . Auth::user()->state;
        $data['service'] = "Reprint";


        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);

    }


    public function filter_by_estate(request $request)
    {

        if (Auth::user()->role == 0) {

            if ($request->meterNo == null) {

                $data['meters'] = Meter::count();
                $data['meter_lists'] = Meter::orderBy('created_at', 'desc')->where('estate_id', $request->estate_id)->paginate('20');
                $data['estate'] = Estate::where('status', 2)->get();
                return view('admin/meter/meter-lists', $data);

            } else {
                $data['meters'] = Meter::count();
                $data['meter_lists'] = Meter::orderBy('created_at', 'desc')->where('meterNo', $request->meterNo)->paginate('20');
                $data['estate'] = Estate::where('status', 2)->get();
                return view('admin/meter/meter-lists', $data);
            }


        } elseif (Auth::user()->role == 1) {


        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {

            if ($request->meterNo == null) {

                $data['estate'] = Estate::where('id', Auth::user()->estate_id)->get();
                $data['meters'] = Meter::count();
                $data['meter_lists'] = Meter::orderBy('created_at', 'desc')->where('estate_id', Auth::user()->estate_id)->paginate('20');
                return view('admin/meter/meter-lists', $data);

            } else {

                $data['meters'] = Meter::count();
                $data['meter_lists'] = Meter::orderBy('created_at', 'desc')->where('meterNo', $request->meterNo)->paginate('20');
                return view('admin/meter/meter-lists', $data);
            }


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }

    public function list_meter(request $request)
    {

        if (Auth::user()->role == 0) {

            $data['meters'] = Meter::count();
            $data['meter_lists'] = Meter::orderBy('created_at', 'desc')->paginate('20');
            $data['estate'] = Estate::where('status', 2)->get();
            return view('admin/meter/meter-lists', $data);

        } elseif (Auth::user()->role == 1) {


        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {

            $data['meters'] = Meter::where('estate_id', Auth::user()->estate_id)->count();
            $data['meter_lists'] = Meter::orderBy('created_at', 'desc')->where('estate_id', Auth::user()->estate_id)->paginate('20');
            $data['estate'] = Estate::where('id', Auth::user()->estate_id)->get();

            return view('admin/meter/meter-lists', $data);

        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }


    public
    function new_meter()
    {


        if (Auth::user()->role == 0) {

            $data['estate'] = Estate::where('status', 2)->get();
            $data['transformer'] = Transformer::latest()->where('status', 2)->get();
            $data['tariff'] = Tariff::latest()->where('status', 2)->get();
            $data['tariffdual'] = Tariff::latest()->where('isDualTariff', "on")->get();


            return view('admin/meter/new-meter', $data);

        } elseif (Auth::user()->role == 1) {


        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {

            $data['estate'] = Estate::where('id', Auth::user()->estate_id)->first();
            $data['transformer'] = Transformer::latest()->where('estate_id', Auth::user()->estate_id)->get();
            $data['tariff'] = Tariff::latest()->where('status', 2)->where('estate_id', Auth::user()->estate_id)->get();
            $data['tariffdual'] = Tariff::latest()->where('isDualTariff', "on")->get();
            $data['meter'] = TarrifState::where('id', Auth::user()->estate_id)->first();


            return view('admin/meter/new-meter', $data);


        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }


    }


    public
    function add_new_meter(request $request)
    {

        $ck = Meter::where('meterNo', $request->meterNo)->first()->meterNo ?? null;
        if ($ck == $request->meterNo) {
            return back()->with('error', "Meter Already Exists");
        }

        // Meter::create($request->all());

        // Handles the checkbox for isDualTariff and KCT fields
        $meterData = $request->all();
        $meterData['isDualTariff'] = $request->has('isDualTariff') ? 'on' : null;
        $meterData['NeedKCT'] = $request->has('NeedKCT') ? 1 : 0;

        // Normalize meterModel and CreditTypeID to lowercase for consistency
        if (isset($meterData['meterModel'])) {
            $meterData['meterModel'] = strtolower($meterData['meterModel']);
        }
        if (isset($meterData['CreditTypeID'])) {
            $meterData['CreditTypeID'] = strtolower($meterData['CreditTypeID']);
        }

        // For estate admin (role == 3), force KRN1 and KRN2 to be STS6
        if (Auth::user()->role == 3) {
            $meterData['KRN1'] = 'STS6';
            $meterData['KRN2'] = 'STS6';
        }

        Meter::create($meterData);

        return redirect('admin/meter-list')->with('message', "Meter added successfully");

    }

    public
    function view_meter(request $request)
    {

        $meter = Meter::where('id', $request->id)->first();

        // Estate admins can only view meters from their estate
        if(Auth::user()->role == 3) {
            if($meter->estate_id != Auth::user()->estate_id) {
                return redirect('admin/meter-list')->with('error', 'Unauthorized access');
            }
        }


        $data['estate'] = Estate::where('status', 2)->get();
        $data['transformer'] = Transformer::latest()->where('status', 2)->get();

        // Filter tariffs based on user role
        if(Auth::user()->role == 0) {
            $data['tariff'] = Tariff::latest()->where('status', 2)->get();
        } else {
            $data['tariff'] = Tariff::latest()->where('status', 2)->where('estate_id', Auth::user()->estate_id)->get();
        }

        $data['meter'] = $meter;
        $data['trans_title'] = Transformer::where('id', $data['meter']->TransformerID)->first()->Title ?? null;
        // $data['NewTariffID'] = Tariff::where('id', $data['meter']->NewTariffID)->first()->title ?? null;
        // $data['OldTariffID'] = Tariff::where('id', $data['meter']->OldTariffID)->first()->title ?? null;
        // $data['tariffdual'] = Tariff::latest()->where('isDualTariff', "on")->get();
        // $data['new_tariff_title'] = Tariff::where('id', $data['meter']->NewTariffID)->first()->title ?? "No title set";
        // $data['old_tariff_title'] = Tariff::where('id', $data['meter']->OldTariffID)->first()->title ?? "No title set";

        $data['new_tariff_title'] = "No tariff set";
        $data['old_tariff_title'] = "No tariff set";
        $data['new_gen_tariff_title'] = "No Gen tariff set";
        $data['old_gen_tariff_title'] = "No Gen tariff set";

         if($meter->NewTariffID) {
            // Try to find by ID first
            $newTariff = Tariff::find($meter->NewTariffID);

            // If not found or doesn't belong to meter's estate, try finding by index (for legacy data)
            if(!$newTariff || $newTariff->estate_id != $meter->estate_id) {
                $newTariff = Tariff::where('estate_id', $meter->estate_id)
                    ->where('tariff_index', $meter->NewTariffID)
                    ->where('type', 'nepa')
                    ->first(); // Get first match if multiple tariffs have same index
            }

            $data['new_tariff_title'] = $newTariff
                ? $newTariff->title . " (Index: " . $newTariff->tariff_index . ")"
                : "Tariff ID: " . $meter->NewTariffID;
        }

        if($meter->OldTariffID) {
            // Try to find by ID first
            $oldTariff = Tariff::find($meter->OldTariffID);

            // If not found or doesn't belong to meter's estate, try finding by index (for legacy data)
            if(!$oldTariff || $oldTariff->estate_id != $meter->estate_id) {
                $oldTariff = Tariff::where('estate_id', $meter->estate_id)
                    ->where('tariff_index', $meter->OldTariffID)
                    ->where('type', 'nepa')
                    ->first(); // Get first match if multiple tariffs have same index
            }

            $data['old_tariff_title'] = $oldTariff
                ? $oldTariff->title . " (Index: " . $oldTariff->tariff_index . ")"
                : "Tariff ID: " . $meter->OldTariffID;
        }

        // Handle dual tariff data - use correct column name with ID suffix
        if($meter->NewTariffDualID) {
            // Try to find by ID first
            $newGenTariff = Tariff::find($meter->NewTariffDualID);

            // If not found or doesn't belong to meter's estate, try finding by index (for legacy data)
            if(!$newGenTariff || $newGenTariff->estate_id != $meter->estate_id) {
                $newGenTariff = Tariff::where('estate_id', $meter->estate_id)
                    ->where('tariff_index', $meter->NewTariffDualID)
                    ->where('type', 'gen')
                    ->first(); // Get first match if multiple tariffs have same index
            }

            if($newGenTariff) {
                $title = $newGenTariff->title ?? 'Untitled Tariff';
                $index = $newGenTariff->tariff_index ?? 'N/A';
                $data['new_gen_tariff_title'] = $title . " (Index: " . $index . ")";
            } else {
                $data['new_gen_tariff_title'] = "Gen Tariff ID: " . $meter->NewTariffDualID . " (Not Found)";
            }
        }

        if($meter->OldTariffDualID) {
            // Try to find by ID first
            $oldGenTariff = Tariff::find($meter->OldTariffDualID);

            // If not found or doesn't belong to meter's estate, try finding by index (for legacy data)
            if(!$oldGenTariff || $oldGenTariff->estate_id != $meter->estate_id) {
                $oldGenTariff = Tariff::where('estate_id', $meter->estate_id)
                    ->where('tariff_index', $meter->OldTariffDualID)
                    ->where('type', 'gen')
                    ->first(); // Get first match if multiple tariffs have same index
            }

            if($oldGenTariff) {
                $title = $oldGenTariff->title ?? 'Untitled Tariff';
                $index = $oldGenTariff->tariff_index ?? 'N/A';
                $data['old_gen_tariff_title'] = $title . " (Index: " . $index . ")";
            } else {
                $data['old_gen_tariff_title'] = "Gen Tariff ID: " . $meter->OldTariffDualID . " (Not Found)";
            }
        }

        $data['transactions'] = CreditToken::latest()->where('meterNo', $data['meter']->meterNo)->where('status', 2)->paginate(20);

        return view('admin/meter/view-meter', $data);

    }


    public function fetchMeterTariffs(Request $request)
        {
            $estate_id = $request->input('estate_id');
            $meterNo = $request->input('meterNo');

            // Find the user and meter
            $user_info = User::where('meterNo', $meterNo)->first();
            if (!$user_info) {
                return 1; // User not found
            }

            // For estate admin, use their estate_id
            if (Auth::user()->role == 3) {
                $estate_id = Auth::user()->estate_id;
            } else {
                $estate_id = $user_info->estate_id ?? $estate_id;
            }

            if ($estate_id == null) {
                return 1; // User not attached to any estate
            }

            // Get the meter details
            $meter = Meter::where('meterNo', $meterNo)->first();
            if (!$meter) {
                return 1; // Meter not found
            }

            // Get only the tariffs assigned to this specific meter
            // Check both column variations (with and without ID suffix)
            $assignedTariffIds = array_filter([
                $meter->NewTariffID,
                $meter->OldTariffID,
                $meter->NewTariffDual,
                $meter->OldTariffDual,
                $meter->NewTariffDualID,
                $meter->OldTariffDualID
            ]);

            if (empty($assignedTariffIds)) {
                return 3; // No tariffs assigned to this meter
            }

            // Fetch only the assigned tariffs
            $tariffs = Tariff::whereIn('id', $assignedTariffIds)
                            ->select('id', 'title', 'type', 'tariff_index')
                            ->get();

            if ($tariffs->isEmpty()) {
                return 2; // Assigned tariffs not found in database
            }

            // Return tariffs along with meter info
            return response()->json([
                'tariffs' => $tariffs,
                'meter' => [
                    'isDualTariff' => $meter->isDualTariff,
                    'NewTariffID' => $meter->NewTariffID,
                    'OldTariffID' => $meter->OldTariffID,
                    'NewTariffDual' => $meter->NewTariffDual,
                    'OldTariffDual' => $meter->OldTariffDual,
                    'NewTariffDualID' => $meter->NewTariffDualID,
                    'OldTariffDualID' => $meter->OldTariffDualID
                ]
            ]);
        }


    public
    function delete_meter(request $request)
    {

        $meter = Meter::where('id', $request->id)->first();

        if (isset($meter->user_id) || User::where('meterNo', $meter->meterNo)->exists()) {
            return redirect('admin/meter-list')->with('error', "Deactivate meter before deleting");
        }


        return redirect('admin/meter-list')->with('message', "Meter deleted successfully");

    }

    public
    function update_meter_info(request $request)
    {


        $meter = Meter::find($request->id);

        // Estate admins can only update meters from their estate
        if(Auth::user()->role == 3) {
            if($meter->estate_id != Auth::user()->estate_id) {
                return back()->with('error', 'You can only update meters from your estate');
            }
        }

        // Get all request data
        $updateData = $request->all();

        // Handle checkbox: checked = "on", unchecked = null
        $updateData['isDualTariff'] = $request->has('isDualTariff') ? 'on' : null;
        $updateData['NeedKCT'] = $request->has('NeedKCT') ? 1 : 0;

        // Normalize meterModel and CreditTypeID to lowercase for consistency
        if (isset($updateData['meterModel'])) {
            $updateData['meterModel'] = strtolower($updateData['meterModel']);
        }
        if (isset($updateData['CreditTypeID'])) {
            $updateData['CreditTypeID'] = strtolower($updateData['CreditTypeID']);
        }

        $meter->update($updateData);

        return redirect('admin/meter-list')->with('message', "Meter updated successfully");


    }


    public
    function update_meter(request $request)
    {

        if ($request->meterNo !== null) {
            $m_id = Meter::where('meterNo', $request->meterNo)->first()->id ?? null;

            if ($m_id == null) {
                return back()->with('error', "Meter not found, contact admin");
            }

            Meter::where('meterNo', $request->old_value)->update(['user_id' => null]);

            User::where('id', $request->user_id)->update(['meterNo' => $request->meterNo, 'meterid' => $m_id]);

            Meter::where('meterNo', $request->meterNo)->update(['user_id' => $request->user_id]);

            return back()->with('message', "Meter Attached  successfully");

        }


    }


    public
    function meter_deactivate(request $request)
    {
        $meter = Meter::where('id', $request->id)->first();

        if (!$meter) {
            return redirect('/admin/meter-list')->with('message', 'Meter Deactivated successfully.');
        }

        // Get the user_id before nullifying
        $user_id = $meter->user_id;

        // Update meter: set status to blocked (3), remove user assignment and account number
        Meter::where('id', $request->id)->update([
            'status' => 0,  // 0 = Blocked status
            'user_id' => null,
            'AccountNo' => null
        ]);

        // If meter was assigned to a customer, remove meter reference from customer
        if ($user_id) {
            User::where('id', $user_id)->update([
                'meterNo' => null
            ]);
        }

        return redirect('/admin/meter-list')->with('message', 'Meter Deactivated successfully.');

    }


    public
    function meter_activate(request $request)
    {

        Meter::where('id', $request->id)->update(['status' => 2]);

        return redirect('/admin/meter-list')->with('message', 'Meter Activated successfully.');


    }


    public function meter_block(request $request)
    {
        Meter::where('id', $request->id)->update(['status' => 0]);

        return redirect('/admin/meter-list')->with('message', 'Meter blocked successfully.');
    }

    public
    function vending_properties(request $request)
    {

        $duration = Utitlity::where('estate_id', Auth::user()->estate_id)->first()->duration ?? null;
        $estate_id = Auth::user()->estate_id ?? null;
        $user_id = Auth::id();

        if ($duration == null || $estate_id == null) {
            $minvend = "Not set";
        } else {
            $get_vend = vend($duration, $estate_id, $user_id);
            if ($get_vend == null) {
                $minvend = "Not set";
            } else {
                $minvend = $get_vend;
            }
        }

        $min_pur = Estate::where('id', Auth::user()->estate_id)->first()->min_pur ?? null;

        return response()->json([
            'status' => true,
            'min_purchase' => (int)$min_pur,
            'min_vend' => (int)$minvend
        ]);


    }

    public
    function generate_kct_token(request $request)
    {

        $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
        if ($meter == null) {
            return back()->with('error', "Meter Not found");
        }

        if ($meter->OldSGC == null) {
            return back()->with('error', "Meter Not properly configured");
        }

        $trx = "TRX-" . random_int(000000, 999999);

        $kctdatabody = [
            'meterType' => $meter->KRN1,
            'tometerType' => $meter->KRN1,
            'meterNo' => $request->meterNo,
            'sgc' => (int)$meter->OldSGC,
            'tosgc' => (int)$meter->NewSGC,
            'ti' => 1,
            'toti' => 1,
            'allow' => false,
            'allowkrn' => true,
        ];

        $kct_response = Http::withOptions([
            'verify' => false,
            'timeout' => 10,
        ])->post('http://169.239.189.91:19071/kcttokenGen', $kctdatabody);

        $estate_id = User::where('id', $request->user_id)->first()->estate_id;

        if ($kct_response->successful()) {
            $kct = $kct_response->json();
            $kct_data = json_decode($kct, true);
            $status = $kct_data['code'] ?? null;

            if ($status == "SUCCESS") {

                $vat = TarrifState::where('tariff_id', $request->tariff_id)->where('status', 2)->first()->amount ?? 0;
                $met = new KctMeterToken();
                $met->user_id = $request->user_id;
                $met->meterNo = $request->meterNo;
                $met->NewSGC = $request->NewSGC;
                $met->OldSGC = $request->OldSGC;
                $met->kct_token = $kct_data['tokens'][0] . "," . $kct_data['tokens'][1];
                $met->estate_id = $estate_id;
                $met->save();

                return back()->with('message', "Meter KCT token has been generated");

            }


            return back()->with('error', "An error occurred");


        }


    }

    public
    function generate_meter_token(request $request)
    {


        $trx = "TRX-" . random_int(000000, 999999);


        $meter = Meter::where('meterNo', $request->meterNo)->first() ?? null;
        $databody = [
            'meterType' => $meter->KRN1,
            'meterNo' => Auth::user()->meterNo,
            'sgc' => (int)$meter->OldSGC,
            'ti' => 1,
            'amount' => $request->amount,
        ];
        $no_kct_response = Http::withOptions([
            'verify' => false,
            'timeout' => 10,
        ])->post('http://169.239.189.91:19071/tokenGen', $databody);

        if ($no_kct_response->successful()) {
            $no_kct = $no_kct_response->json();
            $no_kct_data = json_decode($no_kct, true);
            $status = $no_kct_data['code'] ?? null;

            $user = User::where('id', $request->user_id)->first() ?? null;


            if ($status == "SUCCESS") {

                $vat = TarrifState::where('tariff_id', $request->tariff_id)->where('status', 2)->first()->amount ?? 0;


                $met = new MeterToken ();
                $met->user_id = $request->user_id;
                $met->trx_id = $trx;
                $met->meterNo = $request->MeterNo;
                $met->amount = $request->amount;
                $met->vat = $vat;
                $met->token = $no_kct_data['tokens'][0];
                $met->estate_id = $user->estate_id;
                $met->save();


                $amount = $request->amount;
                $email = $user->email;
                $token = $no_kct_data['tokens'][0];
                send_email_token($email, $token, $amount);

                return back()->with('message', "Meter token has been generated");


            }

            return back()->with('error', "Meter token can not be generated");


        }

        return back()->with('error', "An error occured");

    }


    public
    function detach_meter(request $request)
    {

        Meter::where('meterNo', $request->meterNo)->update(['user_id' => null]);
        User::where('meterNo', $request->meterNo)->update(['meterNo' => null]);

        return back()->with('message', 'Meter has been successfully detached');

    }


    public function fetchTariff(request $request)
    {
        $estate_id = $request->input('estate_id');
        $meterNo = $request->input('meterNo');


        $user_info = User::where('meterNo', $request->meterNo)->first();
        $estate_id = $user_info->estate_id ?? null;
        if ($estate_id == null) {
            return 1;
        }

        $title = Tariff::where('estate_id', $user_info->estate_id)->first()->title ?? null;
        if ($title == null) {
            return 2;
        }

        $tariffs = Tariff::where('estate_id', $user_info->estate_id)->get(['id', 'title', 'type']);
        return response()->json(['tariffs' => $tariffs]);
    }


    public function request_meter(request $request)
    {


        $ck_email = MeterRequest::where('email', $request->email)->where('status', 0)->first() ?? null;
        if ($ck_email) {


            return response()->json([
                'status' => false,
                'message' => "Your request is processing...."
            ], 200);


        } else {

            $met = new MeterRequest();
            $met->user_id = Auth::user()->id;
            $met->email = $request->email;
            $met->fullName = $request->fullName;
            $met->phoneNumber = $request->phoneNumber;
            $met->address = $request->address;
            $met->save();

            return response()->json([
                'status' => true,
                'message' => "We have received your request, We will get back to you shortly"
            ], 200);

        }
    }


    public function meter_report(request $request)
    {

        if (Auth::user()->role == 0) {

            $data['meters'] = Meter::count();
            $data['meter_lists'] = Meter::orderBy('created_at', 'desc')->paginate('20');
            $data['estate'] = Estate::where('status', 2)->get();
            return view('admin/report/metersreport', $data);

        } elseif (Auth::user()->role == 1) {


        } elseif (Auth::user()->role == 2) {

        } elseif (Auth::user()->role == 3) {

            $data['meters'] = Meter::where('estate_id', Auth::user()->estate_id)->count();
            $data['meter_lists'] = Meter::orderBy('created_at', 'desc')->where('estate_id', Auth::user()->estate_id)->paginate('20');
            $data['estate'] = Estate::where('id', Auth::user()->estate_id)->get();

            return view('admin/report/metersreport', $data);

        } elseif (Auth::user()->role == 4) {

        } elseif (Auth::user()->role == 5) {

        } else {

        }



    }

    public function meter_transaction_report(request $request)
    {
        if (Auth::user()->role == 0) {
            $data['total_amount'] = (float) CreditToken::where('status', 2)->sum('amount');
            $data['total_vat'] = (float) CreditToken::where('status', 2)->sum('vatAmount');
            $data['total_units'] = (float) CreditToken::where('status', 2)->sum('unitkwh');

            $data['meter_transactions'] = CreditToken::with(['user:id,first_name,last_name,email,phone'])
                ->where('status', 2)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin/report/meter-transaction-report', $data);

        } elseif (Auth::user()->role == 1) {
        } elseif (Auth::user()->role == 2) {
        } elseif (Auth::user()->role == 3) {

            $data['total_amount'] = (float) CreditToken::where('estate_id', Auth::user()->estate_id)
                ->where('status', 2)
                ->sum('amount');

            $data['total_vat'] = (float) CreditToken::where('estate_id', Auth::user()->estate_id)
                ->where('status', 2)
                ->sum('vatAmount');

            $data['total_units'] = (float) CreditToken::where('estate_id', Auth::user()->estate_id)
                ->where('status', 2)
                ->sum('unitkwh');

            $data['meter_transactions'] = CreditToken::with(['user:id,first_name,last_name,email,phone'])
                ->where('estate_id', Auth::user()->estate_id)
                ->where('status', 2)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin/report/meter-transaction-report', $data);

        } elseif (Auth::user()->role == 4) {
        } elseif (Auth::user()->role == 5) {
        } else {
        }
    }

    public function search_meter_transactions(request $request)
    {
        if (Auth::user()->role == 3) {
            $meterNo = $request->meter_no;
            $startofday = $request->from;
            $endofday = $request->to;
            $estate_id = Auth::user()->estate_id;

            $query = CreditToken::with(['user:id,first_name,last_name,email,phone'])
                ->where('estate_id', $estate_id)
                ->where('status', 2);

            if ($startofday && $endofday) {
                $query->whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59']);
            }

            if ($meterNo) {
                $query->where('meterNo', 'LIKE', '%' . $meterNo . '%');
            }

            $data['meter_transactions'] = $query->orderBy('created_at', 'desc')
                ->take(50000)
                ->paginate(50);

            $data['total_amount'] = $query->sum('amount');
            $data['total_vat'] = $query->sum('vatAmount');
            $data['total_units'] = $query->sum('unitkwh');

            return view('admin/report/meter-transaction-report', $data);
        }

        return back()->with('error', 'Unauthorized access');
    }

}
