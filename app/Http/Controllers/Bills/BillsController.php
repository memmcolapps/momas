<?php

namespace App\Http\Controllers\Bills;

use App\Http\Controllers\Controller;
use App\Models\Logger;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaybetaService;
use App\Services\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BillsController extends Controller
{
    protected PaybetaService $paybetaService;

    public function __construct(PaybetaService $paybetaService)
    {
        $this->paybetaService = $paybetaService;
    }

    public function buy_airtime(request $request)
    {

        $validator = Validator::make($request->all(), [
            'trx_id' => 'nullable|integer|exists:transactions,id',
            'amount' => 'nullable|numeric',
        ]);
        // Map service_id to Paybeta network format
        $networkMap = [
            'mtn' => 'mtn',
            'glo' => 'glo',
            'airtel' => 'airtel',
            '9mobile' => '9mobile',
            'etisalat' => '9mobile',
        ];

        $network = $networkMap[strtolower($request->service_id)] ?? $request->service_id;
        $reference = $request->ref ?? uniqid('airtime_');

        $response = $this->paybetaService->purchaseAirtime(
            $network,
            $request->phone,
            $request->amount,
            $reference
        );

        $user = Auth::user();

        Logger::info("Airtime purchase triggered by {$user->id} | " . Carbon::now()->toIsoString(), [
            'data' => $response
        ]);

        // Check if the transaction was successful
        $status = $response['status'] ?? null;

        if ($status === 'successful') {

            Transaction::where('trx_id', $request->ref)->update(['service_type' => "Airtime Purchase", 'service' => "Airtime", 'status' => 2]);

            $message = "Airtime Purchase successful";
            return success($message);

        }

        // Handle failure cases
        $message = $response;
        send_notification($message);

        // Check for insufficient funds
        $errorMessage = $response['message'] ?? '';
        if (stripos($errorMessage, 'Insufficient') !== false || stripos($errorMessage, 'fund') !== false) {
            User::where('id', Auth::id())->increment('main_wallet', $request->amount);
            $message = "Airtime Purchase not successful, Try again later";
            $code = 422;
            return error($message, $code);
        }

        $message = $response;
        // send_notification($message);

        return StandardResponse::error(500, 'An Error Occurred', $response);
    }


    public function get_data(request $request)
    {
        $allDataBundles = $this->paybetaService->getAllDataBundles();

        // Check if we got any data from the service
        $hasData = !empty($allDataBundles['mtn']) ||
                   !empty($allDataBundles['glo']) ||
                   !empty($allDataBundles['airtel']) ||
                   !empty($allDataBundles['9mobile']);

        if ($hasData) {
            return response()->json([
                'status' => true,
                'mtn_data' => $allDataBundles['mtn'] ?? null,
                'glo_data' => $allDataBundles['glo'] ?? null,
                'airtel_data' => $allDataBundles['airtel'] ?? null,
                '9mobile_data' => $allDataBundles['9mobile'] ?? null,
                'smile_data' => null,
                'spectranet_data' => null
            ]);
        }

        $message = $allDataBundles;
        // send_notification($message);
    }


    public function get_cable_plan(request $request)
    {
        $allCableBouquets = $this->paybetaService->getAllCableBouquets();

        // Check if we got any data from the service
        $hasData = !empty($allCableBouquets['dstv']) ||
                   !empty($allCableBouquets['gotv']) ||
                   !empty($allCableBouquets['startimes']);

        if ($hasData) {
            return response()->json([
                'status' => true,
                'dstv' => $allCableBouquets['dstv'] ?? null,
                'gotv' => $allCableBouquets['gotv'] ?? null,
                'startimes' => $allCableBouquets['startimes'] ?? null,
                'showmax' => null
            ]);
        }

        $message = $allCableBouquets;
        send_notification($message);
    }


        public function validate_cable(request $request)
        {
            // Map decoder_type to Paybeta service format
            $serviceMap = [
                'dstv' => 'dstv',
                'gotv' => 'gotv',
                'startimes' => 'startimes',
                'showmax' => 'showmax',
            ];

            $service = $serviceMap[strtolower($request->decoder_type)] ?? $request->decoder_type;

            $response = $this->paybetaService->validateCable(
                $service,
                $request->decoder_no
            );

            $status = $response['status'] ?? null;

            if ($status === 'successful' || $status === true) {
                return response()->json([
                    'status' => true,
                    'data' => $response['data'] ?? $response,
                ]);
            }

            $message = $response;
            send_notification($message);
        }



    public function buy_cable(request $request)
    {
        // Map decoder_type to Paybeta service format
        $serviceMap = [
            'dstv' => 'dstv',
            'gotv' => 'gotv',
            'startimes' => 'startimes',
            'showmax' => 'showmax',
        ];

        $service = $serviceMap[strtolower($request->decoder_type)] ?? $request->decoder_type;
        $reference = $request->ref ?? uniqid('cable_');

        // Handle showmax separately as it uses different method
        if (strtolower($request->decoder_type) === 'showmax') {
            $response = $this->paybetaService->purchaseShowmax(
                Auth::user()->phone,
                $request->variation_code,
                $request->amount,
                $reference
            );
        } else {
            $response = $this->paybetaService->purchaseCable(
                $service,
                $request->decoder_no,
                $request->variation_code,
                $request->amount,
                $reference
            );
        }

        $user = Auth::user();

        Logger::info("Cable purchase triggered by {$user->id} | " . Carbon::now()->toIsoString(), [
            'data' => $response
        ]);

        $status = $response['status'] ?? null;

        // Update transaction status regardless of outcome (per original logic)
        Transaction::where('trx_id', $request->ref)->update(['service_type' => "Cable Purchase", 'service' => "Cable", 'status' => 2]);

        if ($status === 'successful') {
            $message = "Cable Purchase successful";
            return success($message);
        }

        // Handle failure cases
        $errorMessage = $response['message'] ?? '';
        if (stripos($errorMessage, 'Insufficient') !== false || stripos($errorMessage, 'fund') !== false) {
            User::where('id', Auth::id())->increment('main_wallet', $request->amount);
            $message = $response;
            send_notification($message);
            $message = "Cable Purchase not successful, Try again later";
            $code = 422;
            return error($message, $code);
        }

        $message = $response;
        send_notification($message);
    }


    public function buy_data(request $request)
    {

        $validator = Validator::make($request->all(), []);
        // Map service_id to Paybeta network format
        $networkMap = [
            'mtn' => 'mtn',
            'glo' => 'glo',
            'airtel' => 'airtel',
            '9mobile' => '9mobile',
            'etisalat' => '9mobile',
        ];

        $network = $networkMap[strtolower($request->service_id)] ?? $request->service_id;
        $reference = $request->ref ?? uniqid('data_');

        $response = $this->paybetaService->purchaseData(
            $network,
            $request->phone,
            $request->variation_code,
            $request->amount,
            $reference
        );

        $user = Auth::user();

        Logger::info("Data purchase triggered by {$user->id} amount: {$request->amount} code: {$request->variation_code} | " . Carbon::now()->toIsoString(), [
            'data' => $response
        ]);

        // Update transaction status regardless of outcome (per original logic)
        Transaction::where('trx_id', $request->ref)->update(['service_type' => "Data Purchase", 'service' => "Data", 'status' => 2]);

        $status = $response['status'] ?? null;

        if ($status === 'successful') {
            $message = "Data Purchase successful";
            return success($message);
        }

        // Handle failure cases
        $errorMessage = $response['message'] ?? '';
        if (stripos($errorMessage, 'Insufficient') !== false || stripos($errorMessage, 'fund') !== false) {
            User::where('id', Auth::id())->increment('main_wallet', $request->amount);
            $message = "Data Purchase not successful, Try again later";
            $code = 422;
            return error($message, $code);
        }

        $message = $response;
        send_notification($message);
    }



}






