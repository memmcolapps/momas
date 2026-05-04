<?php

namespace App\Http\Controllers\Bills;

use App\Constants\ServiceTypeConstants;
use App\Constants\TransactionConstants;
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
        $amount = handle_pay_arrears($trx->trx_id, $auth_user->id, 'utilities', true);


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
            $amount,
            $reference
        );


        Logger::info("Airtime purchase triggered by {$auth_user->id} | " . Carbon::now()->toIsoString(), [
            'data' => $response
        ]);

        // Check if the transaction was successful
        $status = $response['status'] ?? null;

        if ($status === 'successful') {

            Transaction::where('trx_id', $request->ref)->update([
                'service_type' => ServiceTypeConstants::AIRTIME_TOP_UP,
                'service' => "Airtime Purchase {$network}",
                'status' => TransactionConstants::TRANSACTION_COMPLETE,
            ]);

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
                'phone' => $request->phone,
            ]);

            User::where('id', Auth::id())->increment('main_wallet', (int) $amount);
            Transaction::where('trx_id', $request->trx_id)->update([
                'wallet_creditted' => $amount,
                'status' => 1,
            ]);
            $message = "Airtime Purchase not successful, Try again later";
            $code = 422;
            return error($message, $code);
        }

        $message = $response;
        // send_notification($message);
        User::where('id', Auth::id())->increment('main_wallet', (int) $amount);
        Transaction::where('trx_id', $request->trx_id)->update([
            'wallet_creditted' => $amount,
            'status' => 1,
        ]);

        Logger::error("An Error Occurred", [
            'user_id' => $auth_user->id,
            'error' => $response,
        ]);

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
                'data_bundle' => $dataBundles['data']['packages'],
                'service_id' => $request->service_id
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

        $serviceMap = [
            'dstv' => 'dstv',
            'gotv' => 'gotv',
            'startimes' => 'startimes',
            'showmax' => 'showmax',
        ];

        $service_ids = implode(',', array_keys($serviceMap));

        $validator = Validator::make($request->all(), [
            'service_id' => ['required', 'string', "in:$service_ids"],
        ]);

        $allCableBouquets = $this->paybetaService->getAllCableBouquets();

        // Check if we got any data from the service
        if ($validator->fails()) {

            return StandardResponse::error(422, "Validator Errors", [
                "validator_error" => $validator->errors(),
            ]);
        }

        $cableBouquets = $this->paybetaService->getCableBouquets($request->service_id);

        return StandardResponse::success(200, 'Fetch Data Plan', [
            'cable_plans' => $cableBouquets['data']['packages'],
            'service_id' => $request->service_id
        ]);

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
        $auth_user = Auth::user();

        try {
            // Map decoder_type to Paybeta service format
            $serviceMap = [
                'dstv' => 'dstv',
                'gotv' => 'gotv',
                'startimes' => 'startimes',
                'showmax' => 'showmax',
            ];

            $service = $serviceMap[strtolower($request->decoder_type)] ?? $request->decoder_type;
            $reference = $request->ref ?? uniqid('cable_');

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


            handle_pay_arrears($trx->trx_id, $auth_user->id, 'utilities');


            $amount = $trx->vending_amount;

            $package = $this->paybetaService->getCablePackage(
                $request->service_id,
                $request->variation_code,
            );

            if (! $package['search_success']) {

                Logger::error("User $auth_user->id passed an invalid variation code poping stored cache", [
                    'user_id' => $auth_user->id,
                    'variation_code' => $request->variation_code,
                    'service' => $request->service_id,
                    'cable_bouquet' => $this->paybetaService->getCableBouquets($request->service_id),
                ]);

                $this->paybetaService->popCableBouquetCache($request->service_id);

                return StandardResponse::success(200, 'Cable Bouquet You Selected Doesn\'t exist', []);
            }

            $bundle_price = (double) $package['price'];
            $value_left = $amount - $bundle_price;

            if ($value_left < 0) {

                Logger::error("User $auth_user->id tried to failed to buy data bundle due to insufficient_fund", [
                    'user_id' => $auth_user->id,
                    'package' => $package,
                ]);

                return StandardResponse::error(403, "Insufficient funds for the selected data bundle", [
                    'reason' => "After Utilities fees payment you have $amount left for transaction bundle costs $bundle_price",
                ]);
            }

            // Handle showmax separately as it uses different method
            if (strtolower($request->decoder_type) === 'showmax') {
                $response = $this->paybetaService->purchaseShowmax(
                    $request->phone,
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
                'data' => $response,
                'payload' => $request->all(),
            ]);

            $status = $response['status'] ?? null;

            // Update transaction status regardless of outcome (per original logic)
            Transaction::where('trx_id', $request->ref)->update([
                'service_type' => ServiceTypeConstants::CABLE_SUBSCRIPTION,
                'service' => "Cable Purchase {$service}",
                'status' => TransactionConstants::TRANSACTION_COMPLETE,
            ]);

            if ($status === 'successful') {
                $message = "Cable Purchase successful";
                return success($message);
            }

            // Handle failure cases
            $errorMessage = $response['message'] ?? '';
            if (stripos($errorMessage, 'Insufficient') !== false || stripos($errorMessage, 'fund') !== false) {

                Logger::error("Cable Bouquet Purchase fail due to insufficient balance", [
                    'user_id' => Auth::id(),
                    'decoder_no/phone(for_showmax)' => $request->decoder_no ?? $request->phone,
                ]);

                User::where('id', Auth::id())->increment('main_wallet', $request->amount);
                Transaction::where('trx_id', $request->ref)->update([
                    'wallet_creditted' => $request->amount,
                    'status' => 1,
                ]);
                $message = $response;
                send_notification($message);
                $message = "Cable Purchase not successful, Try again later";
                $code = 422;
                return error($message, $code);
            }

            $message = $response;
            // send_notification($message);
        } catch (Exception $e) {

            Logger::error($e->getMessage(), [
                'user_id' => $auth_user->id,
                'trace' => $e->getTrace(),
            ]);

            return StandardResponse::error(500, "An Error Occurred", [], [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }
    }


    public function buy_data(request $request)
    {
        $auth_user = Auth::user();
        $validator = Validator::make($request->all(), [
            'trx_id' => ['required', Rule::exists('transactions', 'trx_id')
                ->where(function ($query) use ($auth_user) {
                    $query->where('user_id', $auth_user->id);
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


        handle_pay_arrears($trx->trx_id, $auth_user->id, 'utilities');


        $amount = $trx->vending_amount;

        $package = $this->paybetaService->getDataPackage(
            $request->service_id,
            $request->variation_code,
        );

        $bundle_price = (double) $package['price'];
        $value_left = $amount - $bundle_price;

        if (! $package['search_success']) {

            Logger::error("User $auth_user->id passed an invalid variation code poping stored cache", [
                'user_id' => $auth_user->id,
                'variation_code' => $request->variation_code,
                'network' => $request->service_id,
                'data_bundles' => $this->paybetaService->getDataBundles($request->service_id),
            ]);

            $value_left > 0 && $auth_user->creditWallet($value_left);

            $this->paybetaService->popDataBundleCache($request->service_id);

            Transaction::where('trx_id', $request->trx_id)->update([
                'wallet_creditted' => $value_left,
                'status' => 1,
            ]);

            return StandardResponse::error(200, 'Data Bundle You Selected Doesn\'t exist', []);
        }


        if ($value_left < 0) {

            Logger::error("User $auth_user->id tried to failed to buy data bundle due to insufficient_fund", [
                'user_id' => $auth_user->id,
                'package' => $package,
            ]);

            return StandardResponse::error(403, "Insufficient funds for the selected data bundle", [
                'reason' => "After Utilities fees payment you have $value_left left for transaction bundle costs $bundle_price",
            ]);
        }


        $value_left > 0 && $auth_user->creditWallet($value_left);

        Transaction::where('trx_id', $request->trx_id)->update([
            'wallet_creditted' => $value_left,
            'status' => 3,
        ]);


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
        Transaction::where('trx_id', $request->ref)->update([
            'service_type' => ServiceTypeConstants::DATA_TOP_UP,
            'service' => "Data Purchase {$network}",
            'status' => TransactionConstants::TRANSACTION_COMPLETE,
        ]);

        $status = $response['status'] ?? null;

        if ($status === 'successful') {
            $message = "Data Purchase successful";
            return success($message);
        }

        // Handle failure cases
        $errorMessage = $response['message'] ?? '';
        if (stripos($errorMessage, 'Insufficient') !== false || stripos($errorMessage, 'fund') !== false) {
            User::where('id', Auth::id())->increment('main_wallet', $request->amount);
            Transaction::where('trx_id', $request->trx_id)->update([
                'wallet_creditted' => $request->amount,
                'status' => 1,
            ]);
            $message = "Data Purchase not successful, Try again later";
            $code = 422;
            return error($message, $code);
        }

        // $message = $response;
        // send_notification($message);
    }



}






