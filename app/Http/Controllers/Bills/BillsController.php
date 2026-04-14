<?php

namespace App\Http\Controllers\Bills;

use App\Contracts\PaymentServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Logger;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PaybetaService;
use App\Services\StandardResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
            'trx_id' => 'required|string|exists:transactions,trx_id',
            'phone' => 'required|numeric',
            'service_id' => 'required|string|in:mtn,glo,airtel,9mobile,etisalat'
        ]);

        if ($validator->fails()) {
            return StandardResponse::error(422, 'Validation Error', [
                'validation_error' => $validator->errors(),
            ]);
        }


        $auth_user = Auth::user();

        $trx = Transaction::where('trx_id', $request->trx_id)
            ->where('user_id', $auth_user->id)
            ->first();

        if (! $trx) {
            return StandardResponse::error(404, 'Invalid transaction Id', []);
        }


        $payment_engine = app()->makeWith(PaymentServiceInterface::class, [ 'provider' => $trx->pay_type]);

        switch ($trx->status) {
            case 0:
                $verifier = $payment_engine->verifyTransaction($trx->trx_id);

                // dd($verifier);
                if (! $verifier['is_successful']) {
                    return StandardResponse::error(403, 'Transaction Failed: Please retry later', []);
                }

                $trx->status = 3;
                $trx->save();
                break;

            case 1:
                return StandardResponse::error(403, 'Transaction Failed: Please retry later', []);
                break;

            case 2:
                return StandardResponse::error(403, 'Transaction Completed, restart a new transaction', []);
                break;
        }

        // Pay utility fees
        // handle_pay_arrears($trx->trx_id, $auth_user->id, 'utilities');


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
            $trx->vending_amount ?? $trx->amount,
            $reference
        );

        $user = Auth::user();

        Logger::info("Airtime purchase triggered by {$user->id} | " . Carbon::now()->toIsoString(), [
            'data' => $response
        ]);

        // Check if the transaction was successful
        $status = $response['status'] ?? null;

        if ($status === 'successful') {

            Transaction::where('trx_id', $request->ref)->update(['service_type' => "{$network}_airtime_purchase", 'service' => "Airtime Purchase", 'status' => 2]);

            $message = "Airtime Purchase successful";
            return success($message);

        }

        // // Handle failure cases
        // $message = $response;
        // send_notification($message);

        // Check for insufficient funds
        $errorMessage = $response['message'] ?? '';
        if (stripos($errorMessage, 'Insufficient') !== false || stripos($errorMessage, 'fund') !== false) {
            Logger::error("Airtime Purchase fail due to insufficient balance", [
                'user_id' => Auth::id(),
            ]);
            User::where('id', Auth::id())->increment('main_wallet', (int) $request->amount);
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
        try {
            $networkMap = [
                'mtn' => 'mtn',
                'glo' => 'glo',
                'airtel' => 'airtel',
                '9mobile' => '9mobile',
                'etisalat' => '9mobile',
            ];

            $service_ids = implode(',', array_keys($networkMap));

            $validator = Validator::make($request->all(), [
                'service_id' => ['required', 'string', "in:$service_ids"],
            ]);

            if ($validator->fails()) {

                return StandardResponse::error(422, "Validator Errors", [
                    "validator_error" => $validator->errors(),
                ]);
            }

            $dataBundles = $this->paybetaService->getDataBundles($request->service_id);

            return StandardResponse::success(200, 'Fetch Data Plan', [
                $request->service_id . '_data_bundle' => $dataBundles,
            ]);

        } catch (Exception $e) {
            Logger::error('Failed to fetch data plan', [
                'user_id' => Auth::user()->id,
                'service_id' => $request->service_id,
                'trace' => $e->getTrace(),
            ]);

            return StandardResponse::error(500, 'An Error, Occurred', [], [
                'trace' => $e->getTrace(),
            ]);
        }
    }


    public function get_cable_plan(request $request)
    {
        $allCableBouquets = $this->paybetaService->getAllCableBouquets();

        // Check if we got any data from the service
        $hasData = !empty($allCableBouquets['dstv']) ||
                   !empty($allCableBouquets['gotv']) ||
                   !empty($allCableBouquets['startimes']);

        if ($hasData) {
            return StandardResponse::success(200, 'Fetched Cable Plans', [
                'dstv' => $allCableBouquets['dstv'] ?? null,
                'gotv' => $allCableBouquets['gotv'] ?? null,
                'startimes' => $allCableBouquets['startimes'] ?? null,
                'showmax' => null
            ]);
        }

        // $message = $allCableBouquets;
        // send_notification($message);
        $auth_user = Auth::user();

        Logger::error("Failed to fetch cable plans for User {$auth_user->id}");
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
        Transaction::where('trx_id', $request->ref)->update(['service_type' => "{$service}_cable_purchase", 'service' => "Cable Purchase", 'status' => 2]);

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
        $auth_user = Auth::user();
        $validator = Validator::make($request->all(), [
            'trx_id' => ['required', Rule::exists('transactions', 'trx_id')
                ->where(function ($query) use ($auth_user) {
                    $query->where('user_id', $auth_user->id())
                        ->where('status', 3);
                }),
            ],
            'phone' => 'required|numeric',
            'service_id' => 'required|string|in:mtn,glo,airtel,9mobile,etisalat',
            'variation_code' => 'required|string',
        ]);

        // dd($request->all());

        if ($validator->fails()) {
            return StandardResponse::error(422, 'Validation Error', [
                'validation_error' => $validator->errors(),
            ]);
        }


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

        // Ownership and status verification already handled inside validator
        $trx = Transaction::where('trx_id', $request->trx_id)->first();


        handle_pay_arrears($trx->id, $auth_user->id, 'utilities');


        $amount = $trx->vending_amount;

        $package = $this->paybetaService->getDataPackage(
            $request->service_id,
            $request->variation_code,
        );

        if (! $package['search_success']) {

            Logger::error("User $auth_user->id passed an invalid variation code poping stored cache", [
                'user_id' => $auth_user->id,
                'variation_code' => $request->variation_code,
                'network' => $request->service_id,
                'data_bundles' => $this->paybetaService->getDataBundles($request->service_id),
            ]);

            $this->paybetaService->popDataBundleCache($request->service_id);

            return StandardResponse::success(200, 'Data Bundle You Selected Doesn\'t exist', []);
        }

        $value_left = $amount - (double) $package['price'];

        if ($value_left < 0) {

            Logger::error("User $auth_user->id tried to failed to buy data bundle due to insufficient_fund", [
                'user_id' => $auth_user->id,
                'package' => $package,
            ]);

            return StandardResponse::error(403, "Insufficient funds for the selected data bundle", [
                'reason' => "After Utilities fees payment you have $amount left for transaction",
            ]);
        }


        $value_left > 0 && $auth_user->creditWallet($value_left);


        $response = $this->paybetaService->purchaseData(
            $network,
            $request->phone,
            $request->variation_code,
            $amount,
            $reference
        );

        $user = Auth::user();

        Logger::info("Data purchase triggered by {$user->id} amount: {$request->amount} code: {$request->variation_code} | " . Carbon::now()->toIsoString(), [
            'data' => $response
        ]);

        // Update transaction status regardless of outcome (per original logic)
        Transaction::where('trx_id', $request->ref)->update(['service_type' => "{$network}_data_purchase", 'service' => "Data Purchase", 'status' => 2]);

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

        // $message = $response;
        // send_notification($message);
    }



}






