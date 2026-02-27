<?php

namespace App\Services;

use App\Models\CreditToken;
use App\Models\Feature;
use App\Models\Meter;
use App\Models\MeterToken;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UtilitiesPayment;
use App\Services\TokenGenerationService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestActionHandler {
    protected $reference;

    protected function __construct($reference)
    {
        $this->reference = $reference;
    }



    public static function handleRequestAction($reference) {
        $init = new self($reference);

        $trx = Transaction::where('trx_id', $reference)
            ->firstOrFail();

        $features =  Feature::where('id', 1)->first()->makeHidden(['created_at', 'updated_at', 'id']);
        $features = $features->toArray();

        $action = json_decode($trx->action_payload, true);

        if (! $action['action'] && ! in_array($action['action'], array_keys($features))) {
            throw new Exception('Invalid action passed to paystack: Action not recognized');
        }

        if ($features[$action['action']] != 1) {
            throw new Exception ('Sorry The requested feature is not available at the moment');
        }

        $handler = match ($action['action']) {
            'momas_meter' => fn() => $init->handleBuyTokenRequest(),
            'others_meter' => fn() => $init->handleBuyTokenRequest($others=true),
        };

        return $handler();
    }



    protected function handleBuyTokenRequest($others=false) {
        $trx = Transaction::where('trx_id', $this->reference)
            ->lockForUpdate()
            ->firstOrFail();

        // dd($trx);
        // Only process when service not rendered
        if ($trx->status != 3) {
            Log::warning("Job triggered on invalid status for {$this->reference}");
            return;
        }

        $action_payload = json_decode($trx->action_payload, true);

        $user = User::findOrFail($trx->user_id);
        $meter = Meter::where('meterNo', $user->meterNo)->firstOrFail();

        $tariffId = $action_payload['tariff_id'];
        $unit = $action_payload['vend_amount_kw_per_naira'];
        $needKct = $meter->NeedKCT;

        $tokenGen = TokenGenerationService::generateMeterToken(
            $meter,
            $tariffId,
            $unit,
            $needKct
        );

        if (!$tokenGen['success']) {

            DB::transaction(function () use ($trx, $user, $action_payload) {

                $utility_amount = $action_payload['utility_amount'];
                $total_paid = $action_payload['total_paid_amount'];

                $user->creditWallet($total_paid - $utility_amount);

                $trx->update([
                    'note' => 'Token generation failed',
                    'status' => 3 // Still service yet to render
                ]);
            });
        }

        // Now DB mutation section
        DB::transaction(function () use (
            $trx,
            $user,
            $meter,
            $action_payload,
            $tokenGen
        ) {

            $utility_amount = $action_payload['utility_amount'];
            $total_paid = $action_payload['total_paid_amount'];
            $vat_amount = $action_payload['vat_amount'];
            $token = $tokenGen['data']['token'];

            if ($utility_amount > 0) {
                UtilitiesPayment::where('user_id', $user->id)
                    ->where('estate_id', $user->estate_id)
                    ->update(['status' => 2]);

                $trx->miscellaneous = "utility";
                $trx->miscellaneous_trx_amount = $utility_amount;
            }

            // Prevent duplicate CreditToken
            CreditToken::updateOrCreate(
                ['trx_id' => $trx->trx_id],
                [
                    'user_id' => $user->id,
                    'meterNo' => $meter->meterNo,
                    'amount' => $total_paid,
                    'vat' => $vat_amount,
                    'estate_id' => $user->estate_id,
                    'token' => $token
                ]
            );

            if ($meter->NeedKCT) {

                $kct_tokens = $token['data']['kct_token'];

                MeterToken::updateOrCreate(
                    ['trx_id' => $trx->trx_id],
                    [
                        'user_id' => $user->id,
                        'meterNo' => $meter->meterNo,
                        'token' => $token,
                        'kct_tokens' => $kct_tokens[0] . "," . $kct_tokens[1],
                        'amount' => $total_paid,
                        'vat' => $vat_amount,
                        'estate_id' => $user->estate_id,
                        'status' => 2
                    ]
                );

                send_kct_email_token(
                    $user->email,
                    $token,
                    $action_payload['amount'],
                    $kct_tokens[0],
                    $kct_tokens[1]
                );
            }

            $meter->update(['NeedKCT' => 0]);

            $trx->update([
                'status' => 2, // service rendered
                'unit_amount' => $action_payload['vending_amount'],
                'tariff_id' => $action_payload['tariff_id']
            ]);
        });

        return true;
    }
}
