<?php

namespace App\Http\Controllers\Meter;

use App\Http\Controllers\Controller;
use App\Models\CreditToken;
use App\Models\Estate;
use App\Models\KctMeterToken;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
        $tariffAmounts = TarrifState::whereIn('tariff_id', $tariffs->pluck('id'))->where('status', 2)
            ->pluck('amount', 'tariff_id', 'vat');
        $tariffVats = TarrifState::whereIn('tariff_id', $tariffs->pluck('id'))->where('status', 2)
            ->pluck('tariff_id', 'vat');

        $tariffs->transform(function ($tariffs) use ($tariffAmounts, $tariffVats) {
            $tariffs->amount = $tariffAmounts[$tariffs->id] ?? null;
            $tariffs->vat = 0.75;
            return $tariffs;
        });


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
    public function buy_meter_token(request $request)
    {

        $amount = $request->amount;
        $meterNo = $request->meterNo;
        $trx = $request->trxref ?? $request->ref;
        $utility_amount = $request->utility_amount;
        $total_paid = $request->total_paid_amount;
        $unit = $request->vend_amount_kw_per_naira;
        $vendong_amount = $request->vending_amount;
        $vat_amount = $request->vat_amount;
        $tariff_id = $request->tariff_id;


        $tariff_index = Tariff::where('id', $request->tariff_id)->first()->tariff_index ?? null;
        $duration = Estate::where('id', Auth::user()->estate_id)->first()->duration ?? null;
        if ($duration == "weekly" && $utility_amount > 0) {
            UtilitiesPayment::where('user_id', Auth::id())->where('estate_id', Auth::user()->estate_id)->decrement('amount', $utility_amount);
            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->pay_type = "utility";
            $trx->amount = $request->utility_amount;
            $trx->fee = 0;
            $trx->status = 2;
            $trx->trx_id = "UTL" . random_int(0000, 9999);
            $trx->payment_ref = 0 ?? null;
            $trx->service_type = "utility_payment";
            $trx->save();
        } elseif ($duration == "monthly" && $utility_amount > 0) {

            UtilitiesPayment::where('user_id', Auth::id())->where('estate_id', Auth::user()->estate_id)->decrement('amount', $utility_amount);
            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->pay_type = "utility";
            $trx->amount = $request->utility_amount;
            $trx->fee = 0;
            $trx->status = 2;
            $trx->trx_id = "UTL" . random_int(0000, 9999);
            $trx->payment_ref = 0 ?? null;
            $trx->service_type = "utility_payment";
            $trx->save();

        } elseif ($duration == "yearly" && $utility_amount > 0) {

            UtilitiesPayment::where('user_id', Auth::id())->where('estate_id', Auth::user()->estate_id)->decrement('amount', $utility_amount);
            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->pay_type = "utility";
            $trx->amount = $request->utility_amount;
            $trx->fee = 0;
            $trx->status = 2;
            $trx->trx_id = "UTL" . random_int(0000, 9999);
            $trx->payment_ref = 0 ?? null;
            $trx->service_type = "utility_payment";
            $trx->save();


        }


        $meter = Meter::where('MeterNo', $meterNo)->first() ?? null;

        if ($meter != null && $meter->NeedKCT == "on" || $meter->NeedKCT == 1) {
            $databody = [
                'meterType' => $meter->KRN1,
                'meterNo' => Auth::user()->meterNo,
                'sgc' => (int)$meter->OldSGC,
                'ti' => $tariff_index, //TRARRRIF INDEX
                'amount' => $unit,
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
                        'meterNo' => $request->meterNo,
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


                            $trx_id = "TRX" . random_int(00000, 999999);
                            $estate_id = Auth::user()->estate_id;
                            $cdt = new CreditToken();
                            $cdt->user_id = Auth::user()->id;
                            $cdt->trx_id = $trx_id;
                            $cdt->meterNo = $meterNo;
                            $cdt->amount = $total_paid ?? 0;
                            $cdt->vat = $vat_amount ?? 0;
                            $cdt->estate_name = Estate::where('id', Auth::user()->estate_id)->first()->title ?? "NAME";
                            $cdt->estate_id = $estate_id;
                            $cdt->tariff_id = TarrifState::where('estate_id', $estate_id)->where('status', 2)->first()->tariff_id;
                            $cdt->vatAmount = $vat_amount;
                            $cdt->costOfUnit = $request->vending_amount;
                            $cdt->tariffPerKWatt = $request->vend_amount_kw_per_naira;
                            $cdt->save();


                            $met = new MeterToken();
                            $met->user_id = Auth::user()->id;
                            $met->trx_id = $trx;
                            $met->meterNo = $meterNo;
                            $met->token = $token;
                            $met->amount = $total_paid ?? 0;
                            $met->unit = $unit;
                            $met->kct_tokens = $kct_data['tokens'][0] . "," . $kct_data['tokens'][1];
                            $met->vat = $vat_amount;
                            $met->estate_id = Auth::user()->estate_id;
                            $met->status = 2;
                            $met->save();

                            Transaction::where('trx_id', $trx)->update(['service' => "METER PURCHASE", 'service_type' => "meter", 'unit_amount' => $vendong_amount, 'vat' => $vat_amount, 'tariff_id' => $request->tariff_id,
                            ]);


                            $data2['full_name'] = Auth::user()->first_name . " " . Auth::user()->last_name;
                            $data2['address'] = Auth::user()->address . "," . Auth::user()->city . "," . Auth::user()->state;
                            $data2['service'] = "MOMAS METER";
                            $data2['trx_id'] = $trx;
                            $data2['token'] = $token;
                            $data2['amount'] = $total_paid;
                            $data2['vending_amount'] = $vendong_amount;
                            $data2['vend_amount_kw_per_naira'] = $unit;
                            $data2['kct_token1'] = $kct_data['tokens'][0];
                            $data2['kct_token2'] = $kct_data['tokens'][1];
                            $data2['vat_amount'] = $vat_amount;


                            $email = Auth::user()->email;
                            $kct_token = $kct_data['tokens'];
                            $kct_token1 = $kct_token[0];
                            $kct_token2 = $kct_token[1];

                            send_kct_email_token($email, $token, $amount, $kct_token1, $kct_token2);

                            return response()->json([
                                'status' => true,
                                'data' => $data2
                            ], 200);


                        }
                    } else {

                        Transaction::where('trx_id', $trx)->update([
                            'service' => "METER PURCHASE",
                            'service_type' => "meter",
                            'status' => 3,
                            'tariff_id' => $request->tariff_id,
                            'unit_amount' => $vendong_amount,
                            'note' => $kct_response . "| " . json_encode($databody)

                        ]);


                        User::where('id', Auth::id())->increment('main_wallet', $request->amount);


                        return response()->json([

                            'status' => false,
                            'message' => "Vending server not connected, Retry again on transaction history",
                        ], 422);
                    }

                }


            } else {

                return response()->json([
                    'status' => false,
                    'message' => "Meter vending failed, Retry again using your wallet"
                ], 422);

            }

        }


        if ($meter != null && $meter->NeedKCT == null || $meter->NeedKCT == 0) {


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

                if ($status == "SUCCESS") {

                    $no_kct_token = $no_kct_data['tokens'][0];
                    $vat = TarrifState::where('tariff_id', $request->tariff_id)->where('status', 2)->first()->amount ?? 0;
                    $met = new CreditToken();
                    $met->user_id = Auth::user()->id;
                    $met->trx_id = $trx;
                    $met->meterNo = $meterNo;
                    $met->token = $no_kct_token;
                    $met->amount = $total_paid;
                    $met->vatAmount = round($vat_amount, 2);
                    $met->costOfUnit = round($vendong_amount, 2);
                    $met->unitkwh = round($unit, 2);
                    $met->estate_name = Estate::where('id', $request->estate_id)->first()->title;
                    $met->vat = 0;
                    $met->tariff_amount = TarrifState::where('tariff_id', $tariff_id)->where('status', 2)->first()->amount ?? 0;
                    $met->estate_id = Auth::user()->estate_id;
                    $met->amount_charged = $total_paid;
                    $met->status = 2;
                    $met->save();

                    Transaction::where('trx_id', $trx)->update(['service' => "METER PURCHASE", 'service_type' => "credit_token", 'unit_amount' => $vendong_amount]);

                    $data['full_name'] = Auth::user()->first_name . " " . Auth::user()->last_name;
                    $data['address'] = Auth::user()->address . "," . Auth::user()->city . "," . Auth::user()->state;
                    $data['service'] = "MOMAS METER";
                    $data['trx_id'] = $trx;
                    $data['token'] = $no_kct_data['tokens'][0];
                    $data['amount'] = $total_paid;
                    $data['meterNo'] = $request->meterNo;

                    $vend_amount = round($vendong_amount, 2);
                    $vatt = round($vat_amount, 2);
                    $uun = round($unit, 2);


                    $data['vending_amount'] = "$vend_amount";
                    $data['vat_amount'] = "$vatt";
                    $data['vend_amount_kw_per_naira'] = "$uun";
                    $email = Auth::user()->email;
                    $token = $no_kct_data['tokens'][0];
                    //send_email_token($email, $token, $amount);

                    return response()->json([
                        'status' => true,
                        'data' => $data
                    ], 200);


                } else {


                    Transaction::where('trx_id', $trx)->update([
                        'service' => "METER PURCHASE",
                        'service_type' => "meter",
                        'status' => 3,
                        'tariff_id' => $request->tariff_id,
                        'unit_amount' => $vendong_amount,
                        'note' => $no_kct_response . "|" . json_encode($databody)


                    ]);

                    User::where('id', Auth::id())->increment('main_wallet', $request->amount);


                    return response()->json([
                        'status' => false,
                        'message' => "Vending server not connected, Retry again on transaction history",
                    ], 422);

                }


            }


        }

        return response()->json([

            'status' => false,
            'message' => "Something went wrong, Contact our support",
        ], 422);


    }

    public function retry_meter_token(request $request)
    {

        $amount = $request->amount;
        $meterNo = $request->meterNo;
        $trx_id = $request->trxref;


        $trx = Transaction::where('trx_id', $trx_id)->first() ?? null;
        if ($trx == null) {
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


        $user_wallet = User::where('id', Auth::id())->first()->main_wallet;
        if($user_wallet < $trx->amount){
            return response()->json([
                'status' => false,
                'message' => "Insufficient Funds to retry vending",
            ], 422);
        }



        $tariff_index = Tariff::where('id', $trx->tariff_id)->first()->tariff_index ?? null;
        $user = User::where('id', $trx->user_id)->first();
        $meter = Meter::where('user_id', $trx->user_id)->first();



        if ($meter != null && $meter->NeedKCT == "on") {
            $databody = [
                'meterType' => $meter->KRN1,
                'meterNo' => $meter->meterNo,
                'sgc' => (int)$meter->OldSGC,
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

                        Transaction::where('trx_id', $trx)->update([
                            'service_type' => "token Purchase",
                            'service' => "Meter",
                            'status' => 3,
                            'note' => json_encode($kct_response)


                        ]);


                        return response()->json([
                            'status' => false,
                            'message' => "Vending server not connected, Retry again later using your wallet",
                        ], 422);
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
                'meterType' => $meter->KRN1,
                'meterNo' => $meter->meterNo,
                'sgc' => (int)$meter->OldSGC,
                'ti' => 1,
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


    public function pay_for_others_meter_token(request $request)
    {

        $amount = $request->amount;
        $meterNo = $request->meterNo;
        $meterType = $request->meterType;
        $estate_id = $request->estate_id;
        $trx = $request->trxref;
        $date = date('ymd');
        $percentage = 2.5 / 100;
        $final_amount = $percentage * $amount;
        $dater = date('d-m-y');


        $user = User::where('meterNo', $request->meterNo)->first() ?? null;
        $duration = Utitlity::where('estate_id', $request->estate_id)->first()->duration;


        $duration = Utitlity::where('estate_id', Auth::user()->estate_id)->first()->duration;
        if ($request->min_vend_amount > 0) {
            $utl = new UtilitiesPayment();
            $utl->user_id = $user->id ?? null;
            $utl->estate_id = $request->estate_id;
            $utl->amount = $request->min_vend_amount;
            $utl->duration = $duration;
            $utl->status = 2;
            $utl->save();
        }


//////////////////////////////////////////////////////////////////////////test

        $data['full_name'] = Auth::user()->first_name . " " . Auth::user()->last_name;
        $data['address'] = Auth::user()->address . "," . Auth::user()->city . "," . Auth::user()->state;
        $data['service'] = "MOMAS";
        $data['trx_id'] = $trx;
        $data['token'] = "123456944885756";
        $data['amount'] = $amount;
        $data['date'] = $dater;


        $ved = new MeterToken();
        $ved->user_id = Auth::id();
        $ved->trx_id = $trx;
        $ved->meterNo = $meterNo;
        $ved->token = "123456944885756";
        $ved->estate_id = $estate_id;
        $ved->amount = $amount;
        $ved->unit = "0099847";
        $ved->vat = "1234";
        $ved->estate_name = Estate::where('id', $estate_id)->first()->title;
        $ved->status = 2;
        $ved->save();


        $email = Auth::user()->email;
        $token = "123456944885756";
        $unit = "0099847";

        send_email_token($email, $token, $amount, $unit);

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);


//        $databody = array();
//
//        $url = "http://41.216.166.163:8080/MomasPayService/api/Payment/$meterNo/$meterType/999/$trx/$amount/999/$date/$final_amount/false";
//
//        $body = json_encode($databody);
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => $url,
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => $body,
//            CURLOPT_HTTPHEADER => array(
//                'Accept: application/json',
//                'Content-Type: application/json'
//            ),
//        ));
//
//        $var = curl_exec($curl);
//        curl_close($curl);
//        $var = json_decode($var);
//        $response = $var->responsecode ?? null;


//        if ($response == "00") {
//            $data['full_name'] = Auth::user()->first_name . " " . Auth::user()->last_name;
//            $data['address'] = Auth::user()->address . "," . Auth::user()->city . "," . Auth::user()->state;
//            $data['service'] = "MOMAS";
//            $data['trx_id'] = $trx;
//            $data['token'] = $var->recieptNumber;
//            $data['amount'] = $amount;
//            $data['date'] = $dater;
//
//
//            $ved = new MeterToken();
//            $ved->user_id = Auth::id();
//            $ved->trx_id = $trx;
//            $ved->meterNo = $meterNo;
//            $ved->token = $var->recieptNumber;
//            $ved->estate_id = $estate_id;
//            $ved->amount = $amount;
//            $ved->unit = $var->unit;
//            $ved->vat = $var->vat;
//            $ved->estate_name = Estate::where('id', $estate_id)->first()->title;
//            $ved->status = 2;
//            $ved->save();
//
//
//            $email = Auth::user()->email;
//            $token = $var->recieptNumber;
//            $unit = $var->unit;
//
//            send_email_token($email, $token, $amount, $unit);
//
//        } else {
//
//            User::where('id', Auth::id())->increment('main_wallet', $amount);
//            return response()->json([
//                'status' => false,
//                'message' => "Meter vending failed, Retry again using your wallet"
//            ], 200);
//
//
//        }


        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);


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
            $assignedTariffIds = array_filter([
                $meter->NewTariffID,
                $meter->OldTariffID,
                $meter->NewTariffDual,
                $meter->OldTariffDual
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
                    'OldTariffDual' => $meter->OldTariffDual
                ]
            ]);
        }
    

    public
    function delete_meter(request $request)
    {
        Meter::where('id', $request->id)->delete();
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
            return back()->with('error', "Meter not found");
        }

        // Get the user_id before nullifying
        $user_id = $meter->user_id;

        // Update meter: set status to blocked (3), remove user assignment and account number
        Meter::where('id', $request->id)->update([
            'status' => 3,  // 3 = Blocked status
            'user_id' => null,
            'AccountNo' => null
        ]);

        // If meter was assigned to a customer, remove meter reference from customer
        if ($user_id) {
            User::where('id', $user_id)->update([
                'meterNo' => null
            ]);
        }

        return back()->with('message', "Meter deactivated and unassigned successfully");

    }


    public
    function meter_activate(request $request)
    {

        Meter::where('id', $request->id)->update(['status' => 2]);

        return back()->with('message', "Meter Activated successfully");


    }


    public function meter_block(request $request)
    {
        Meter::where('id', $request->id)->update(['status' => 0]);

        return back()->with('message', "Meter blocked successfully");
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
            $data['total_amount'] = CreditToken::where('status', 2)->sum('amount');
            $data['total_vat'] = CreditToken::where('status', 2)->sum('vatAmount');
            $data['total_units'] = CreditToken::where('status', 2)->sum('unitkwh');

            $data['meter_transactions'] = CreditToken::with(['user:id,first_name,last_name,email,phone'])
                ->where('status', 2)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin/report/meter-transaction-report', $data);

        } elseif (Auth::user()->role == 1) {
        } elseif (Auth::user()->role == 2) {
        } elseif (Auth::user()->role == 3) {

            $data['total_amount'] = CreditToken::where('estate_id', Auth::user()->estate_id)
                ->where('status', 2)
                ->sum('amount');

            $data['total_vat'] = CreditToken::where('estate_id', Auth::user()->estate_id)
                ->where('status', 2)
                ->sum('vatAmount');

            $data['total_units'] = CreditToken::where('estate_id', Auth::user()->estate_id)
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
