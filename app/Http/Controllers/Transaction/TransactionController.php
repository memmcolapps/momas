<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\CreditToken;
use App\Models\Estate;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UtilitiesPayment;
use App\Models\VirtualAccountTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{

    public function arrears(request $request)
    {

        $get_trx = UtilitiesPayment::where('user_id', Auth::id())->get();
        return response()->json([
            'status' => true,
            'data' => $get_trx
        ]);


    }


    public function pay_arrears(request $request)
    {

        if ($request->type === "single") {
            UtilitiesPayment::where('user_id', Auth::id())->where('id', $request->id)->update(['status' => 1]);
            Transaction::where('trx_id', $request->ref)->update(['service_type' => "Arrears Payment", 'service' => "Arrears", 'status' => 2]);

            $message = "Arrears Payment Completed";
            return success($message);
        }


        if ($request->type === "all") {
            UtilitiesPayment::where('user_id', Auth::id())->update(['status' => 2]);
            Transaction::where('trx_id', $request->ref)->update(['service_type' => "Arrears Payment", 'service' => "Arrears", 'status' => 2]);

            $message = "Arrears Payment Completed";
            return success($message);
        }


        UtilitiesPayment::where('user_id', Auth::id())->get();
        return response()->json([
            'status' => true,
            'data' => $get_trx
        ]);


    }


    public function flutter_payment(request $request)
    {

//        $fl = Setting::where('id', 1)->first();
//        $flkey['flutterwave_secret'] = $fl->flutterwave_secret;
//        $flkey['flutterwave_public'] = $fl->flutterwave_public;
//        $pkkey['paystack_secret'] = $fl->paystack_secret;
//        $pkkey['paystack_public'] = $fl->paystack_public;
//
//        $data['amount'] = $request->amount;
//        $data['trx_id'] = $request->trx_id;
//        $data['email'] = $request->email;
//        $data['key'] = $flkey['flutterwave_public'];
//
//
//        return view('flutter-pay', $data);


        $message = "flutterwave = " . json_encode($request->all());
        send_notification($message);

        $fl = Setting::where('id', 1)->first();
        $secretKey = $fl->flutterwave_secret;
        $transactionId = $request->transaction_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transactionId/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $secretKey",
                "Cache-Control: no-cache",
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;

        if ($status == 'success') {
            Transaction::where('trx_id', $request->tx_ref)->update(['status' => 2]);
            $ref = Transaction::where('trx_id', $request->tx_ref)->first()->trx_id;
            $url = url('') . "/payment?ref=$request->tx_ref&status=success";
            return redirect($url);
        } else {
            $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
            $url = url('') . "/payment?ref=$request->tx_ref&status=failure";
            return redirect($url);
        }

    }


    public function make_payment(request $request)
    {

//        $estate_id = Auth::id()->estate_id;
//        $est = Estate::where('id', $estate_id)->first();
//
//        if($est->charge_fee_flat == null){
//            $fee  = ($est->charge_fee_precent / 100) * (int)$request->amount;
//        }else{
//            $fee  = $est->charge_fee_flat;
//        }
//
//        $customer_email = Auth::user()->email;
//        $amount = $request->amount - $fee;
//        $trx_id = "TRX" . random_int(000000000, 9999999999);
//
//
//
//        $cdt = new CreditToken();
//        $cdt->user_id = Auth::id();
//        $cdt->trx_id = $trx_id;
//        $cdt->meterNo = Auth::user()->meterNo;
//        $cdt->amount = $amount;
//        $cdt->amount_charged = $request->amount;
//        $cdt->customer_email = $customer_email;
//        $cdt->fee = $fee;
//        $cdt->vat = $request->vat;
//        $cdt->estate_name = Estate::where('id', Auth::user()->estate_id)->first()->title;;
//        $cdt->estate_id = $estate_id;
//        $cdt->tariff_id = TarrifState::where('amount', $request->tariff_amount)->first()->tariff_id;
//        $cdt->tariff_amount = $request->tariff_amount;
//        $cdt->vatAmount = $request->vatAmount;
//        $cdt->costOfUnit = $request->costOfUnit;
//        $cdt->unitkwh = $request->unit;
//        $cdt->tariffPerKWatt = $request->tariffPerKWatt;
//        $cdt->save();
//
//
//
//        try {
//
//            if ($request->pay_type == 'flutterwave') {
//
//
//                $estate_id = $request->estate_id;
//                $est = Estate::where('id', $estate_id)->first();
//                if($est->charge_fee_flat == null){
//                    $fee  = ($est->charge_fee_precent / 100) * (int)$request->amount;
//                }else{
//                    $fee  = $est->charge_fee_flat;
//                }
//
//
//                $email = Auth::user()->email;
//                $phone = Auth::user()->phone ?? "012345678";
//                $fl = Setting::where('id', 1)->first();
//                $secretKey = $fl->flutterwave_secret;
//                $fpublic = $fl->flutterwave_public;
//                $url = url('');
//
//                $databody = array(
//                    'title' => 'Payment for services',
//                    'amount' => $request->amount,
//                    'currency' => 'NGN',
//                    'redirect_url' => $url . "/admin/pay-flutter-web",
//                    'customer' => [
//                        'email' => $customer_email,
//                        'phonenumber' => $phone,
//                        'name' => Auth::user()->first_name . " " . Auth::user()->last_name,
//                    ],
//                    'tx_ref' => $trx_id,
//
//                );
//
//                $body = json_encode($databody);
//                $curl = curl_init();
//
//                curl_setopt_array($curl, array(
//                    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
//                    CURLOPT_RETURNTRANSFER => true,
//                    CURLOPT_ENCODING => '',
//                    CURLOPT_MAXREDIRS => 10,
//                    CURLOPT_TIMEOUT => 0,
//                    CURLOPT_FOLLOWLOCATION => true,
//                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                    CURLOPT_CUSTOMREQUEST => 'POST',
//                    CURLOPT_POSTFIELDS => $body,
//                    CURLOPT_HTTPHEADER => array(
//                        'Accept: application/json',
//                        'Content-Type: application/json',
//                        'Authorization: Bearer ' . $secretKey,
//                    ),
//                ));
//
//                $var = curl_exec($curl);
//                curl_close($curl);
//                $var = json_decode($var);
//                $status = $var->status ?? null;
//
//
//                $trx = new Transaction();
//                $trx->user_id = Auth::id();
//                $trx->estate_id = Auth::user()->estate_id;
//                $trx->pay_type = "flutterwave";
//                $trx->service_type = $request->service;
//                $trx->amount = $request->amount;
//                $trx->fee = $fee;
//                $trx->trx_id = $trx_id;
//                $trx->save();
//
//                if ($status == "success") {
//                    return redirect()->away($var->data->link);
//
//                }
//
//
//            }
//
//        } catch (Exception $e) {
//            return back()->with('error', $e);
//        }
//
//
//        if ($request->pay_type == 'paystack') {
//
////            try {
//
//            $estate_id = $request->estate_id ?? null;
//            if ($estate_id === null) {
//                $estate_id = Auth::user()->estate_id;
//            }
//            $est = Estate::where('id', $estate_id)->first();
//
//
//            if($est->charge_fee_flat == null){
//                $fee  = ($est->charge_fee_precent / 100) * (int)$request->amount;
//            }else{
//                $fee  = $est->charge_fee_flat;
//            }
//
//
//            $fl = Setting::where('id', 1)->first();
//            $flkey['flutterwave_secret'] = $fl->flutterwave_secret;
//            $flkey['flutterwave_public'] = $fl->flutterwave_public;
//            $paystackkey = $fl->paystack_secret;
//            $pkkey['paystack_public'] = $fl->paystack_public;
//
//            $databody = array(
//                "amount" => $request->amount * 100,
//                "email" => $customer_email,
//                "ref" => $trx_id,
//                'callback_url' => url('') . "/admin/paystack-check-web",
//                'subaccount' => $est->paystack_subaccount,
//                'metadata' => ["ref" => $trx_id],
//            );
//
//            $body = json_encode($databody);
//            $curl = curl_init();
//            curl_setopt_array($curl, array(
//                CURLOPT_URL => 'https://api.paystack.co/transaction/initialize',
//                CURLOPT_RETURNTRANSFER => true,
//                CURLOPT_ENCODING => '',
//                CURLOPT_MAXREDIRS => 10,
//                CURLOPT_TIMEOUT => 0,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//                CURLOPT_CUSTOMREQUEST => 'POST',
//                CURLOPT_POSTFIELDS => $body,
//                CURLOPT_HTTPHEADER => array(
//                    'Accept: application/json',
//                    'Content-Type: application/json',
//                    'Authorization: Bearer ' . $paystackkey,
//                ),
//            ));
//
//            $var = curl_exec($curl);
//            curl_close($curl);
//            $var = json_decode($var);
//            $status = $var->status;
//
//
//
//            if ($status == true) {
//                $trx = new Transaction();
//                $trx->user_id = $request->user_id;
//                $trx->pay_type = "paystack";
//                $trx->amount = $request->amount;
//                $trx->fee = $fee;
//                $trx->trx_id = $trx_id;
//                $trx->payment_ref = $var->data->access_code ?? null;
//                $trx->service_type = "credit_token";
//                $trx->save();
//
//                return redirect()->away($var->data->authorization_url);
//
//            }
//
//            $code = 422;
//            $message = "Payment not available at the moment, Kindly select other payment option";
//            return error($message, $code);
//
////            } catch (Exception $e) {
////                return back()->with('error', $e);
////            }
//
//        }
//
//
//        try {
//
//            if ($request->pay_type == 'remita') {
//                $trx_id = "TRX" . random_int(0000000, 9999999);
//                $email = Auth::user()->email;
//                $trx = new Transaction();
//                $trx->user_id = Auth::id();
//                $trx->pay_type = "remita";
//                $trx->service_type = "fund";
//                $trx->amount = $request->amount;
//                $trx->trx_id = $trx_id;
//                $trx->save();
//
//                return response()->json([
//                    'status' => true,
//                    'url' => url('') . "/pay-remita?amount=$request->amount&trx_id=$trx_id&email=$email"
//                ], 200);
//            }
//
//        } catch (Exception $e) {
//            return back()->with('error', $e);
//        }
//
//
//        try {
//            if ($request->pay_type == 'wallet') {
//                $trx_id = "TRX" . random_int(0000000, 9999999);
//                $email = Auth::user()->email;
//
//
//                if (Auth::user()->main_wallet < $request->amount) {
//                    $code = 422;
//                    $message = "Insufficient Funds";
//                    return error($message, $code);
//                }
//
//
//                User::where('id', Auth::id())->decrement('main_wallet', $request->amount);
//
//                $trx = new Transaction();
//                $trx->user_id = Auth::id();
//                $trx->pay_type = "wallet";
//                $trx->amount = $request->amount;
//                $trx->service_type = $request->service;
//                $trx->trx_id = $trx_id;
//                $trx->save();
//
//                return response()->json([
//                    'status' => "success",
//                    'ref' => $trx_id,
//                ], 200);
//
//            }
//
//
//        } catch (Exception $e) {
//            return back()->with('error', $e);
//        }


        if ($request->pay_type == 'flutterwave') {
            $trx_id = "TRXFLW" . random_int(0000000, 9999999);
            $email = Auth::user()->email;
            $phone = Auth::user()->phone ?? "012345678";
            $fl = Setting::where('id', 1)->first();
            $secretKey = $fl->flutterwave_secret;
            $fpublic = $fl->flutterwave_public;
            $url = url('');

            $databody = array(
                'title' => 'Payment for services',
                'amount' => $request->amount,
                'currency' => 'NGN',
                'redirect_url' => $url . "/pay-flutter",
                'customer' => [
                    'email' => $email,
                    'phonenumber' => $phone,
                    'name' => Auth::user()->first_name . " " . Auth::user()->last_name,
                ],
                'tx_ref' => $trx_id,

            );

            $body = json_encode($databody);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $secretKey,
                ),
            ));

            $var = curl_exec($curl);
            curl_close($curl);
            $var = json_decode($var);
            $status = $var->status ?? null;


            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->estate_id = Auth::user()->estate_id;
            $trx->pay_type = "flutterwave";
            $trx->service_type = $request->service;
            $trx->amount = $request->amount;
            $trx->trx_id = $trx_id;
            $trx->save();

            if ($status == "success") {
                return response()->json([
                    'status' => true,
                    'url' => $var->data->link
                ], 200);
            }


        }


        if ($request->pay_type === 'paystack') {
            $fl = Setting::where('id', 1)->first();
            $flkey['flutterwave_secret'] = $fl->flutterwave_secret;
            $flkey['flutterwave_public'] = $fl->flutterwave_public;
            $paystackkey = $fl->paystack_secret;
            $pkkey['paystack_public'] = $fl->paystack_public;


            $trx_id = "TRX" . random_int(0000000, 9999999);
            $email = Auth::user()->email;


            $databody = array(
                "amount" => $request->amount * 100,
                "email" => $email,
                "ref" => $trx_id,
                'callback_url' => url('') . "/paystack-check",
                'metadata' => ["ref" => $trx_id],

            );

            $body = json_encode($databody);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.paystack.co/transaction/initialize',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $paystackkey,
                ),
            ));

            $var = curl_exec($curl);
            curl_close($curl);
            $var = json_decode($var);
            $status = $var->status;


            if ($status == true) {
                $trx = new Transaction();
                $trx->user_id = Auth::id();
                $trx->pay_type = "paystack";
                $trx->estate_id = Auth::user()->estate_id;
                $trx->amount = $request->amount;
                $trx->trx_id = $trx_id;
                $trx->payment_ref = $var->data->access_code ?? null;
                $trx->service_type = $request->service;
                $trx->save();

                return response()->json([
                    'status' => true,
                    'url' => $var->data->authorization_url
                ], 200);

            }

            $code = 422;
            $message = "Payment not available at the moment, Kindly select other payment option";
            return error($message, $code);

        }


        if ($request->pay_type === 'remita') {
            $trx_id = "TRX" . random_int(0000000, 9999999);
            $email = Auth::user()->email;
            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->pay_type = "remita";
            $trx->service_type = "fund";
            $trx->amount = $request->amount;
            $trx->trx_id = $trx_id;
            $trx->save();

            return response()->json([
                'status' => true,
                'url' => url('') . "/pay-remita?amount=$request->amount&trx_id=$trx_id&email=$email"
            ], 200);
        }


        if ($request->pay_type === 'wallet') {
            $trx_id = "TRX" . random_int(0000000, 9999999);
            $email = Auth::user()->email;


            if (Auth::user()->main_wallet < $request->amount) {
                $code = 422;
                $message = "Insufficient Funds";
                return error($message, $code);
            }


            User::where('id', Auth::id())->decrement('main_wallet', $request->amount);

            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->pay_type = "wallet";
            $trx->amount = $request->amount;
            $trx->service_type = $request->service;
            $trx->trx_id = $trx_id;
            $trx->save();

            return response()->json([
                'status' => "success",
                'ref' => $trx_id,
            ], 200);

        }

        if ($request->pay_type === 'enkpay') {

            $trx_id = "TRXENK" . random_int(0000000, 9999999);
            $email = Auth::user()->email;
            $phone = Auth::user()->phone ?? "012345678";
            $fl = Setting::where('id', 1)->first();
            $key = $fl->enkpay_key;

            $url = "https://web.sprintpay.online/pay?amount=$request->amount&key=$key&ref=$trx_id&email=$email";

            $trx = new Transaction();
            $trx->user_id = Auth::id();
            $trx->estate_id = Auth::user()->estate_id;
            $trx->pay_type = "enkpay";
            $trx->service_type = $request->service;
            $trx->amount = $request->amount;
            $trx->trx_id = $trx_id;
            $trx->save();

            return response()->json([
                'status' => true,
                'url' => $url
            ], 200);


        }


    }


    public function all_transactions(request $request)
    {

        $trx = Transaction::latest()->where('user_id', Auth::id())->take(1000)->get()->makeHidden('note');
        return response()->json([
            'status' => true,
            'data' => $trx,
        ], 200);

    }

    public function get_trx(request $request)
    {
        $get_trx = Transaction::where('id', $request->id)->first();
        $meterNo = CreditToken::where('trx_id', $get_trx->trx_id)->first()->meterNo ?? null;
        $token = CreditToken::where('trx_id', $get_trx->trx_id)->first()->token ?? null;
        $get_trx['token'] = $token;
        $get_trx['meterNo'] = $meterNo;

        return response()->json([
            'status' => true,
            'data' => $get_trx,
        ], 200);

    }


    public function estate_transactions(request $request)
    {

        $trx = Transaction::where('user_id', Auth::id())->take(1000)->get();
        return response()->json([
            'status' => true,
            'data' => $trx,
        ], 200);

    }

    public function enkpay_payment_verify(request $request)
    {

        if ($request->status === 'success') {
            Transaction::where('trx_id', $request->trans_id)->update(['status' => 4]);
            $ref = $request->trans_id;
            $url = url('') . "/payment?ref=$ref&status=success";
            return redirect($url);
        }


    }



    public function flutter_verify(request $request)
    {



        $fl = Setting::where('id', 1)->first();
        $flsecret = $fl->flutterwave_secret;
        $flkey['flutterwave_public'] = $fl->flutterwave_public;
        $transactionId = $request->transaction_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/$transactionId/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $flsecret,
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);

        $status = $var->status ?? null;
        $ref = $var->data->tx_ref ?? null;

        $ck_transaction = Transaction::where('trx_id', $var->data->tx_ref)->first()->status ?? null;
        if ($ck_transaction == null) {

            if ($status == 'success') {
                Transaction::where('trx_id', $var->data->tx_ref)->update(['status' => 4]);
                $ref = $var->data->tx_ref;
                $url = url('') . "/payment?ref=$ref&status=success";
                return redirect($url);
            }

        } else {
            $url = url('') . "/payment?ref=$ref&status=failure";
            return redirect($url);
        }


    }




    public function paystack_verify(request $request)
    {

        $message = "paystack=" . json_encode($request->all());
        send_notification($message);

        $fl = Setting::where('id', 1)->first();
        $pksecret = $fl->paystack_secret;
        $transactionId = $request->reference;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$transactionId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $pksecret",
                "Cache-Control: no-cache",
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;
        $ref = $var->data->reference ?? null;


        $ck_transaction = Transaction::where('trx_id', $var->data->reference)->first()->status ?? null;
        if ($ck_transaction == null) {

            if ($status == 'success') {
                Transaction::where('trx_id', $var->data->metadata->ref)->update(['status' => 2]);
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=success";
                return redirect($url);
            } else {
                $ref = Transaction::where('trx_id', $var->data->metadata->ref)->first()->trx_id;
                $url = url('') . "/payment?ref=$ref&status=failure";
                return redirect($url);
            }

        }

    }


    public function transaction_reports(request $request)
    {


        if (Auth::user()->role == 0) {

            $data['transactions'] = Transaction::latest()->paginate('20');
            $data['total'] = Transaction::where('status', 2)->sum('amount');
            $data['estate'] = Estate::all();

            return view('admin.report.transactionreport', $data);


        } elseif (Auth::user()->role == 1) {

        } elseif
        (Auth::user()->role == 2) {

        } elseif
        (Auth::user()->role == 3) {

            $data['transactions'] = Transaction::latest()->where('estate_id', Auth::user()->estate_id)->paginate('20');
            $data['total'] = Transaction::where('status', 2)->where('estate_id', Auth::user()->estate_id)->sum('amount');

            return view('admin.report.transactionreport', $data);

        } elseif
        (Auth::user()->role == 4) {

        } elseif
        (Auth::user()->role == 5) {

        }


    }


    public function search_trx(request $request)
    {

        if (Auth::user()->role == 0) {


            $rrn = $request->rrn;
            $startofday = $request->from;
            $endofday = $request->to;
            $transaction_type = $request->transaction_type;
            $status = $request->status;
            $estate_id = $request->estate_id;
            $data['estate'] = Estate::all();


            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type == null && $status == null) {

                $data['transactions'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->latest()
                    ->where('status', 2)
                    ->take(50000)
                    ->paginate(50);

                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where('status', 2)
                    ->sum('amount') ?? 0;


                return view('admin.report.transactionreport', $data);

            }

            if ($rrn != null) {

                $data['transactions'] = Transaction::where('trx_id', $rrn)->paginate(50);
                $data['total'] = Transaction::where('trx_id', $rrn)->sum('amount') ?? 0;

                return view('admin.report.transactionreport', $data);


            }


            if ($estate_id != null) {

                if ($estate_id == "all") {

                    $data['transactions'] = Transaction::
                    latest()
                        ->where('status', 2)
                        ->take(50000)
                        ->paginate(50);

                    $data['total'] = Transaction::where('status', 2)->sum('amount') ?? 0;


                    return view('admin.report.transactionreport', $data);
                }

                $data['transactions'] = Transaction::where('estate_id', $estate_id)
                    ->latest()
                    ->where('status', 2)
                    ->take(50000)
                    ->paginate(50);

                $data['total'] = Transaction::where('estate_id', $estate_id)->where('status', 2)
                    ->sum('amount') ?? 0;


                return view('admin.report.transactionreport', $data);

            }


            if ($estate_id != null) {

                if ($estate_id == "all") {

                    $data['transactions'] = Transaction::
                    latest()
                        ->where('status', 2)
                        ->take(50000)
                        ->paginate(50);

                    $data['total'] = Transaction::where('status', 2)->sum('amount') ?? 0;


                    return view('admin.report.transactionreport', $data);
                }

                $data['transactions'] = Transaction::where('estate_id', $estate_id)
                    ->latest()
                    ->where('status', 2)
                    ->take(50000)
                    ->paginate(50);

                $data['total'] = Transaction::where('estate_id', $estate_id)
                    ->where('status', 2)
                    ->sum('amount') ?? 0;


                return view('admin.report.transactionreport', $data);

            }


            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type != null && $status == null) {


                $data['transactions'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->latest()
                    ->where('status', 2)
                    ->take(50000)
                    ->where('service_type', $transaction_type)
                    ->paginate(50);

                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where('service_type', $transaction_type)
                    ->where('status', 2)
                    ->sum('amount') ?? 0;


                return view('admin.report.transactionreport', $data);


            }


            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type != null && $status != null) {
                $data['transactions'] = Transaction::latest()->take(50000)->whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $transaction_type,
                ])->paginate('50') ?? null;


                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $transaction_type,
                ])->sum('amount') ?? 0;


                return view('admin.report.transactionreport', $data);

            }


            return back()->with('error', 'Select a field');

        }


        if (Auth::user()->role == 3) {

            $rrn = $request->rrn;
            $startofday = $request->from;
            $endofday = $request->to;
            $transaction_type = $request->transaction_type;
            $status = $request->status;

            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type == null && $status == null) {

                $data['transactions'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->latest()
                    ->where('estate_id', Auth::user()->estate_id)
                    ->take(50000)
                    ->paginate(50);

                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where('estate_id', Auth::user()->estate_id)
                    ->sum('amount') ?? 0;

                return view('admin.report.transactionreport', $data);

            }


            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type != null && $status == null) {


                $data['transactions'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where([
                        'status' => $status,
                        'service_type' => $transaction_type,
                        'estate_id' => Auth::user()->estate_id
                    ])->paginate('50') ?? null;

                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where([
                        'status' => $status,
                        'service_type' => $transaction_type,
                        'estate_id' => Auth::user()->estate_id
                    ])->paginate('50')
                    ->sum('amount') ?? 0;

                return view('admin.report.transactionreport', $data);

            }


            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type != null && $status != null) {
                $data['transactions'] = Transaction::latest()->take(50000)->whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $transaction_type,
                    'estate_id' => Auth::user()->estate_id
                ])->paginate('50') ?? null;


                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $transaction_type,
                    'estate_id' => Auth::user()->estate_id

                ])->sum('amount') ?? 0;


                return view('admin.report.transactionreport', $data);

            }


            return back()->with('error', 'Select a field');


        }


    }

    public function get_account_details(request $request)
    {
        $amount = Setting::where('id', 1)->first()->first()->admin_fee;
        $key = Setting::where('id', 1)->first()->first()->enkpay_key;
        $trx_id = "MOMAS" . random_int(000000, 999999);
        $email = Auth::user()->email;
        $pt = "momas";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://web.sprintpay.online/paynow?amount=$amount&key=$key&ref=$trx_id&email=$email&platform=$pt",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);


        VirtualAccountTransaction::where('v_account_no', $request->account_no)->delete();

        $va = new VirtualAccountTransaction();
        $va->user_id = Auth::id();
        $va->v_account_no = $var->account_no;
        $va->v_account_name = $var->account_name;
        $va->amount = $var->amount - 100;
        $va->type = "admin_fee";
        $va->v_bank_name = $var->bank;
        $va->status = 0;
        $va->save();


        return response()->json([
            'status' => true,
            'bank' => $var->bank,
            'account_no' => $var->account_no,
            'account_name' => $var->account_name,
            'amount' => $var->amount,
        ]);


    }


    public function check_admin_fee(request $request)
    {
        $admin_fee_get = UtilitiesPayment::where('user_id', Auth::id())
            ->where('type', 'admin_fee')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->latest('created_at')
            ->first()->status;

        if ($admin_fee_get == 2) {

            return response()->json([
                'status' => true,
                'monthly_admin_fee' => "1"
            ]);

        } else {

            return response()->json([
                'status' => true,
                'monthly_admin_fee' => "0"
            ]);
        }
    }

    public function enkpay_webhook(request $request)
    {


        $get_user_id = VirtualAccountTransaction::where('v_account_no', $request->account_no)->first()->user_id ?? null;
        $get_pay_type = VirtualAccountTransaction::where('v_account_no', $request->account_no)->first()->type ?? null;

        $amount = $request->amount - 100;

        if ($get_pay_type === "admin_fee" && $get_user_id != null) {

            $update_payment = VirtualAccountTransaction::where('v_account_no', $request->account_no)->where('amount', $request->amount)->update(['status' => 2, 'session_id' => $request->session_id]);
            if ($update_payment) {
                $user = User::where('id', $get_user_id)->first();
                $utl = new UtilitiesPayment();
                $utl->estate_id = $user->estate_id;
                $utl->user_id = $get_user_id;
                $utl->amount = $amount;
                $utl->duration = "monthly";
                $utl->type = "admin_fee";
                $utl->status = 2;
                $utl->save();

                $type = "Monthly Administration Fee";
                $duration = Carbon::now()->format('F');
                payment_email($user->email, $type, $amount, $duration);

                return response()->json([
                    'status' => true,
                    'message' => "Transaction Completed"
                ]);


            } else {

                return response()->json([
                    'status' => false,
                    'message' => "something went wrong"
                ], 422);
            }

        }


        if ($get_pay_type === "wallet_funding" && $get_user_id != null) {

            $update_payment = VirtualAccountTransaction::where('v_account_no', $request->account_no)->where('amount', $request->amount)->update(['status' => 2, 'session_id' => $request->session_id]);
            if ($update_payment) {
                return response()->json([
                    'status' => true,
                    'message' => "Transaction Completed"
                ]);
            } else {

                return response()->json([
                    'status' => false,
                    'message' => "something went wrong"
                ], 422);
            }

        }


        if ($get_pay_type === null && $get_user_id === null) {

            Transaction::where('trx_id', $request->order_id)->update(['status' => 4]);

            $va = new VirtualAccountTransaction();
            $va->v_account_no = $request->account_no;
            $va->v_account_name = "woven";
            $va->amount = $request->amount;
            $va->type = $request->order_id;
            $va->v_bank_name = "VFD";
            $va->session_id = $request->session_id;
            $va->status = 2;
            $va->save();

            return response()->json([
                'status' => true,
                'message' => "Transaction Completed"
            ]);


        }


    }


    public function utility_payment(request $request)
    {

        $data['total_pending'] = UtilitiesPayment::where('status', 0)->sum('total_amount');
        $data['total_completed'] = UtilitiesPayment::where('status', 2)->sum('total_amount');
        $data['payment'] = UtilitiesPayment::latest()->take('1000')->paginate(50);
        $data['estate'] = Estate::all();
        $data['customer'] = User::latest()->where('status', 2)->get();

        return view('admin.report.payment', $data);


    }

    public function uncomplete_payment(request $request)
    {

        UtilitiesPayment::where('id', $request->id)->update(['status' => 0]);
        return back()->with('message', "Payment has been updated successfully");

    }

    public function complete_payment(request $request)
    {
        UtilitiesPayment::where('id', $request->id)->update(['status' => 2]);
        return back()->with('message', "Payment has been updated successfully");

    }




    public function search_utility_trx(request $request)
    {

        if (Auth::user()->role == 0) {


            $customer = $request->user_id;
            $startofday = $request->from;
            $endofday = $request->to;
            $transaction_type = $request->type;
            $status = $request->status;
            $estate_id = $request->estate_id;
            $data['estate'] = Estate::all();


            if ($startofday != null && $endofday != null && $customer == null && $transaction_type == null && $status == null) {

                $data['payment'] = UtilitiesPayment::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->latest()
                    ->take(50000)
                    ->paginate(50);

                $data['total_pending'] = UtilitiesPayment::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where('status', 0)
                    ->sum('amount') ?? 0;

                $data['total_completed'] = UtilitiesPayment::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where('status', 2)
                    ->sum('amount') ?? 0;


                $data['estate'] = Estate::all();
                $data['customer'] = User::latest()->where('status', 2)->get();


                return view('admin.report.payment', $data);

            }

            if ($customer != null) {


                $data['payment'] = UtilitiesPayment::where('user_id', $customer)->paginate(50);
                $data['total_pending'] = UtilitiesPayment::where('status', 0)->sum('amount') ?? 0;
                $data['total_completed'] = UtilitiesPayment::where('status', 2)->sum('amount') ?? 0;

                $data['estate'] = Estate::all();
                $data['customer'] = User::latest()->where('status', 2)->get();

                return view('admin.report.payment', $data);


            }


            if ($estate_id != null) {

                if ($estate_id == "all") {

                    $data['payment'] = UtilitiesPayment::
                    latest()
                        ->take(50000)
                        ->paginate(50);

                    $data['total_pending'] = UtilitiesPayment::where('status', 0)->sum('amount') ?? 0;
                    $data['total_completed'] = UtilitiesPayment::where('status', 2)->sum('amount') ?? 0;

                    $data['estate'] = Estate::all();
                    $data['customer'] = User::latest()->where('status', 2)->get();


                    return view('admin.report.payment', $data);
                }

                $data['payment'] = UtilitiesPayment::where('estate_id', $estate_id)
                    ->latest()
                    ->take(50000)
                    ->paginate(50);

                $data['total_completed'] = UtilitiesPayment::where('estate_id', $estate_id)->where('status', 2)
                    ->sum('amount') ?? 0;

                $data['total_pending'] = UtilitiesPayment::where('estate_id', $estate_id)->where('status', 0)
                    ->sum('amount') ?? 0;

                $data['estate'] = Estate::all();
                $data['customer'] = User::latest()->where('status', 2)->get();


                return view('admin.report.payment', $data);

            }


            if ($startofday != null && $endofday != null && $customer == null && $type != null && $status == null) {


                $data['payment'] = UtilitiesPayment::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->latest()
                    ->take(50000)
                    ->where('type', $type)
                    ->paginate(50);

                $data['total_completed'] = UtilitiesPayment::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where('service_type', $type)
                    ->where('status', 2)
                    ->sum('amount') ?? 0;

                $data['total_pending'] = UtilitiesPayment::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where('service_type', $type)
                    ->where('status', 0)
                    ->sum('amount') ?? 0;

                $data['estate'] = Estate::all();
                $data['customer'] = User::latest()->where('status', 2)->get();


                return view('admin.report.payment', $data);


            }


            if ($startofday != null && $endofday != null && $customer == null && $type != null && $status != null) {
                $data['payment'] = UtilitiesPayment::latest()->take(50000)->whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $type,
                ])->paginate('50') ?? null;


                $data['total_completed'] = UtilitiesPayment::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $type,
                ])->sum('amount') ?? 0;

                $data['total_pending'] = UtilitiesPayment::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $type,
                ])->sum('amount') ?? 0;

                $data['estate'] = Estate::all();
                $data['customer'] = User::latest()->where('status', 2)->get();


                return view('admin.report.payment', $data);

            }


            return back()->with('error', 'Select a field');

        }


        if (Auth::user()->role == 3) {

            $rrn = $request->rrn;
            $startofday = $request->from;
            $endofday = $request->to;
            $transaction_type = $request->transaction_type;
            $status = $request->status;

            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type == null && $status == null) {

                $data['transactions'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->latest()
                    ->where('estate_id', Auth::user()->estate_id)
                    ->take(50000)
                    ->paginate(50);

                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where('estate_id', Auth::user()->estate_id)
                    ->sum('amount') ?? 0;

                return view('admin.report.transactionreport', $data);

                $data['estate'] = Estate::all();
                $data['customer'] = User::latest()->where('status', 2)->get();

            }


            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type != null && $status == null) {


                $data['transactions'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where([
                        'status' => $status,
                        'service_type' => $transaction_type,
                        'estate_id' => Auth::user()->estate_id
                    ])->paginate('50') ?? null;

                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])
                    ->where([
                        'status' => $status,
                        'service_type' => $transaction_type,
                        'estate_id' => Auth::user()->estate_id
                    ])->paginate('50')
                    ->sum('amount') ?? 0;

                $data['estate'] = Estate::all();
                $data['customer'] = User::latest()->where('status', 2)->get();

                return view('admin.report.transactionreport', $data);

            }


            if ($startofday != null && $endofday != null && $rrn == null && $transaction_type != null && $status != null) {
                $data['transactions'] = Transaction::latest()->take(50000)->whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $transaction_type,
                    'estate_id' => Auth::user()->estate_id
                ])->paginate('50') ?? null;


                $data['total'] = Transaction::whereBetween('created_at', [$startofday . ' 00:00:00', $endofday . ' 23:59:59'])->
                where([
                    'status' => $status,
                    'service_type' => $transaction_type,
                    'estate_id' => Auth::user()->estate_id

                ])->sum('amount') ?? 0;

                $data['estate'] = Estate::all();
                $data['customer'] = User::latest()->where('status', 2)->get();


                return view('admin.report.payment', $data);

            }


            return back()->with('error', 'Select a field');


        }
    }


    public function fund_wallet(request $request)
    {

        $amount = $request->amount;
        $key = Setting::where('id', 1)->first()->first()->enkpay_key;
        $trx_id = "MOMASFUND" . random_int(000000, 999999);
        $email = Auth::user()->email;
        $pt = "momas";


        $url = "https://web.sprintpay.online/pay?amount=$amount&key=$key&ref=$trx_id&email=$email";


        return redirect()->away($url);


    }


}

