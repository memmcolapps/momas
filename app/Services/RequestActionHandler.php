<?php

namespace App\Services;

use App\Models\ClearcreditToken;
use App\Models\Feature;
use App\Models\KctToken;
use App\Models\Meter;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class RequestActionHandler {
    protected $reference;

    protected function __construct($reference)
    {
        $this->reference = $reference;
    }



    public static function handleRequestAction($reference) {
        dump("handleRequestAction");
        $init = new self($reference);

        $trx = Transaction::where('trx_id', $reference)
            ->firstOrFail();

        $features =  Feature::where('id', 1)->first()->makeHidden(['created_at', 'updated_at', 'id']);
        $features = $features->toArray();

        $action = json_decode($trx->action_payload, true);

        if (! $action['action'] && ! in_array($action['action'], array_keys($features))) {
            throw new Exception('Invalid action passed to paystack: Action not recognized');
        }

        $actionables = array_merge($features, [
            'momas_meter_web' => 1,
            'momas_tamper_token' => 1,
            'momas_kct_token' => 1,
            'momas_clear_credit_token' => 1
        ]);

        if ($actionables[$action['action']] != 1) {
            throw new Exception ('Sorry The requested feature is not available at the moment');
        }
        dump("got here 41");

        $handler = match ($action['action']) {
            'momas_meter', 'momas_meter_web' => fn() => $init->handleBuyTokenRequest(),
            'momas_tamper_token' => fn() => $init->handleBuyTamperTokenRequest(),
            'momas_kct_token' => fn() => $init->handleBuyKctTokenRequest(),
            'momas_clear_credit_token' => fn() => $init->handleBuyClearCreditTokenRequest(),
            'others_meter' => fn() => $init->handleBuyTokenRequest($others=true),
        };

        return $handler();
    }



    protected function handleBuyTokenRequest($others=false) {
        dump('handleBuyTokenRequest');
        throw new Exception("Test Failure");
        $trx = Transaction::where('trx_id', $this->reference)
            ->firstOrFail();

        // dd($trx);
        // Only process when service not rendered
        if ($trx->status != 3) {
            Log::warning("Job triggered on invalid status for {$this->reference}");
            return;
        }

        $action_payload = json_decode($trx->action_payload, true);
        $user_id = $action_payload['user_id'];

        dump($user_id, $action_payload);
        $user = User::findOrFail($user_id);
        dump("user->", $user->id);

        $meter = Meter::where('user_id', $user->id)->firstOrFail();
        dump("meter_with_uid->", $meter->id);
        // dump($meter);

        $tariffId = $action_payload['tariff_id'];
        $unit = $action_payload['vend_amount_kw_per_naira'];
        $vat = $action_payload['vat_amount'];
        $needKct = $meter->NeedKCT;
        $vending_amount = $action_payload['vending_amount'];

        $meter->getNewToken($tariffId, $unit, $this->reference, $vat, $vending_amount, $verify='null');

        return true;
    }

    /**
     * Handle tamper token purchase request
     *
     * @param bool $others Whether this is for other meters
     * @return bool
     * @throws \Exception
     */
    protected function handleBuyTamperTokenRequest($others = false)
    {
        Log::info('handleBuyTamperTokenRequest started', ['reference' => $this->reference]);
        // throw new Exception("Test Failure handleBuyTamperTokenRequest");

        $trx = Transaction::where('trx_id', $this->reference)
            ->firstOrFail();

        // Only process when service not rendered
        if ($trx->status != 3) {
            Log::warning("Job triggered on invalid status for {$this->reference}");
            return;
        }

        $action_payload = json_decode($trx->action_payload, true);
        $user_id = $action_payload['user_id'];

        Log::info('Tamper token request payload', ['payload' => $action_payload]);

        $user = User::findOrFail($user_id);
        $meter = Meter::where('user_id', $user->id)->firstOrFail();

        $tariffId = $action_payload['tariff_id'];
        $vending_amount = $action_payload['vending_amount'];
        $email = $action_payload['email'] ?? null;

        // Call the tamper token generation method on the meter
        $meter->getNewTamperToken($tariffId, $this->reference, $vending_amount, $email, $verify = 'null');

        Log::info('handleBuyTamperTokenRequest completed', ['reference' => $this->reference]);

        return true;
    }

    /**
     * Handle KCT token purchase request
     *
     * @return bool
     * @throws \Exception
     */
    protected function handleBuyKctTokenRequest()
    {
        Log::info('handleBuyKctTokenRequest started', ['reference' => $this->reference]);
        // throw new Exception("Test Failure handleBuyKctTokenRequest");

        $trx = Transaction::where('trx_id', $this->reference)
            ->firstOrFail();

        // Only process when service not rendered
        if ($trx->status != 3) {
            Log::warning("Job triggered on invalid status for {$this->reference}");
            return;
        }

        $action_payload = json_decode($trx->action_payload, true);
        $user_id = $action_payload['user_id'];

        Log::info('KCT token request payload', ['payload' => $action_payload]);

        $user = User::findOrFail($user_id);
        $meter = Meter::where('user_id', $user->id)->firstOrFail();

        $meterNo = $action_payload['meterNo'];
        $sgc = $action_payload['sgc'];
        $tosgc = $action_payload['tosgc'];
        $ti = $action_payload['ti'];
        $toti = $action_payload['toti'] ?? 1;

        // Call the KCT token generation method on the meter
        $meter->getNewKctToken(
            $this->reference,
            $meterNo,
            $sgc,
            $tosgc,
            $ti,
            $toti
        );

        Log::info('handleBuyKctTokenRequest completed', ['reference' => $this->reference]);

        return true;
    }

    /**
     * Handle clear credit token purchase request
     *
     * @return bool
     * @throws \Exception
     */
    protected function handleBuyClearCreditTokenRequest()
    {
        Log::info('handleBuyClearCreditTokenRequest started', ['reference' => $this->reference]);

        $trx = Transaction::where('trx_id', $this->reference)
            ->firstOrFail();

        // Only process when service not rendered
        if ($trx->status != 3) {
            Log::warning("Job triggered on invalid status for {$this->reference}");
            return;
        }

        $action_payload = json_decode($trx->action_payload, true);
        $user_id = $action_payload['user_id'];

        Log::info('Clear credit token request payload', ['payload' => $action_payload]);

        $user = User::findOrFail($user_id);
        $meter = Meter::where('user_id', $user->id)->firstOrFail();

        $tariff_id = $action_payload['tariff_id'];
        $tariff_index = $action_payload['tariff_index'] ?? null;

        // Get tariff index if not provided in payload
        if (!$tariff_index && $tariff_id) {
            $tariff = \App\Models\Tariff::where('id', $tariff_id)->first();
            $tariff_index = $tariff->tariff_index ?? null;
        }

        // Call the clear credit token generation method
        $result = TokenGenerationService::generateClearCreditToken($meter, $tariff_index);

        if (!$result['success']) {
            Log::error('Clear credit token generation failed', ['error' => $result['error'] ?? 'Unknown error']);
            Transaction::where('trx_id', $this->reference)->update([
                'note' => 'Clear credit token generation failed: ' . ($result['error'] ?? 'Unknown error'),
                'status' => 3,
            ]);
            return false;
        }

        // Update ClearcreditToken record with the generated token
        ClearcreditToken::where('trx_id', $this->reference)->update([
            'token' => $result['data']['token'],
            'status' => 2
        ]);

        // Update transaction status
        Transaction::where('trx_id', $this->reference)->update([
            'status' => 2,
            'service' => 'CLEAR CREDIT TOKEN PURCHASE',
        ]);

        Log::info('handleBuyClearCreditTokenRequest completed', ['reference' => $this->reference]);

        return true;
    }
}
