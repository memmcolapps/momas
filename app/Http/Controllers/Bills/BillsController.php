<?php

namespace App\Http\Controllers\Bills;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Logger;

class BillsController extends Controller
{

    public function buy_airtime(request $request)
    {

        $token = token();
        $databody = array(
            "service_id" => $request->service_id,
            "amount" => $request->amount,
            "phone" => $request->phone,
        );

        $body = json_encode($databody);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web.sprintpay.online/api/buy-ng-airtime',
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
                'Authorization: Bearer ' . $token,
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;

        Logger::info("Airtime purchase triggered by " . Carbon::now()->toIsoString(), [
            'data' => $var
        ]);




        if ($status == true) {

            Transaction::where('trx_id', $request->ref)->update(['service_type' => "Airtime Purchase", 'service' => "Airtime", 'status' => 2]);

            $message = "Airtime Purchase successful";
            return success($message);

        }

        if ($status == false) {

            $message = $var;
            send_notification($message);

            if ($var->message = "Insufficient Funds, Fund your main wallet") {
                User::where('id', Auth::id())->increment('main_wallet', $request->amount);
                $message = "Airtime Purchase not successful, Try again later";
                $code = 422;
                return error($message, $code);
            }


        }

        $message = $var;
        send_notification($message);

    }


    public function get_data(request $request)
    {


        $token = token();
        $databody = array();

        $body = json_encode($databody);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web.sprintpay.online/api/get-data',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ),
        ));

        $var = curl_exec($curl);

        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;


        if ($status == true) {

            return response()->json([
                'status' => true,
                'mtn_data' => $var->mtn_data,
                'glo_data' => $var->glo_data,
                'airtel_data' => $var->airtel_data,
                '9mobile_data' => $var->ninemobile_data,
                'smile_data' => $var->smile_data,
                'spectranet_data' => $var->spectranet_data
            ]);

        }


        $message = $var;
        send_notification($message);

    }


    public function get_cable_plan(request $request)

    {


        $token = token();
        $databody = array();

        $body = json_encode($databody);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web.sprintpay.online/api/cable-plan',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status ?? null;


        if ($status == true) {
            return response()->json([
                'status' => true,
                'dstv' => $var->dstv,
                'gotv' => $var->gotv,
                'startimes' => $var->startimes,
                'showmax' => $var->showmax,
            ]);

        }

        $message = $var;
        send_notification($message);
    }


        public function validate_cable(request $request)
        {
            $token = token();
            $databody = array(

                'serviceid' => $request->decoder_type,
                'biller_code' => $request->decoder_no

            );

            $body = json_encode($databody);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://web.sprintpay.online/api/validate-cable',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token,
                ),
            ));

            $var = curl_exec($curl);


            curl_close($curl);
            $var = json_decode($var);
            $status = $var->status ?? null;

            if ($status == true) {
                return response()->json([
                    'status' => true,
                    'data' => $var->data,

                ]);

            }

            $message = $var;
            send_notification($message);

        }



    public function buy_cable(request $request)
    {

        $token = token();

        if ($request->quantity != null) {

            $databody = array(

                'billersCode' => $request->decoder_no,
                'variation_code' => $request->variation_code,
                'amount' => $request->amount,
                'phone' => Auth::user()->phone,
                'quantity' => $request->quantity,

            );

        }


        if ($request->subscription_type != null) {

            $databody = array(

                'billersCode' => $request->decoder_no,
                'variation_code' => $request->variation_code,
                'amount' => $request->amount,
                'phone' => Auth::user()->phone,
                'subscription_type' => $request->subscription_type,

            );

        }


        $body = json_encode($databody);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://web.sprintpay.online/api/buy-cable',
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
                'Authorization: Bearer ' . $token,
            ),
        ));

        $var = curl_exec($curl);

        curl_close($curl);
        $var = json_decode($var);


        $status = $var->status ?? null;

        Transaction::where('trx_id', $request->ref)->update(['service_type' => "Cable Purchase", 'service' => "Cable", 'status' => 2]);


        if ($status == true) {
            $message = "Cable Purchase successful";
            return success($message);

        }



        if ($status == false) {

            if ($var->message = "Insufficient Funds, Fund your main wallet") {
                User::where('id', Auth::id())->increment('main_wallet', $request->amount);
                $message = $var;
                send_notification($message);
                $message = "Cable Purchase not successful, Try again later";
                $code = 422;
                return error($message, $code);
            }


        }
    }


    public
        function buy_data(request $request)
        {

            $token = token();
            $databody = array(
                "service_id" => $request->service_id,
                "amount" => $request->amount,
                "phone" => $request->phone,
                "variation_code" => $request->variation_code,
                "variation_amount" => $request->amount,

            );

            $body = json_encode($databody);
            $curl = curl_init();


            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://web.sprintpay.online/api/buy-data',
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
                    'Authorization: Bearer ' . $token,
                ),
            ));

            $var = curl_exec($curl);
            curl_close($curl);
            $var = json_decode($var);
            $status = $var->status ?? null;


            Transaction::where('trx_id', $request->ref)->update(['service_type' => "Data Purchase", 'service' => "Data", 'status' => 2]);


            if ($status == true) {
                $message = "Data Purchase successful";
                return success($message);

            }

            if ($status == false) {

                if ($var->message = "Insufficient Funds, Fund your main wallet") {
                    User::where('id', Auth::id())->increment('main_wallet', $request->amount);
                    $message = "Data Purchase not successful, Try again later";
                    $code = 422;
                    return error($message, $code);
                }


            }

            $message = $var;
            send_notification($message);


        }



}






