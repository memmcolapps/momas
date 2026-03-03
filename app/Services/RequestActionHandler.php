<?php

namespace App\Services;

use App\Models\Feature;
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
        dump('handleBuyTokenRequest');
        $trx = Transaction::where('trx_id', $this->reference)
            ->firstOrFail();

        // dd($trx);
        // Only process when service not rendered
        if ($trx->status != 3) {
            Log::warning("Job triggered on invalid status for {$this->reference}");
            return;
        }

        $action_payload = json_decode($trx->action_payload, true);

        $user = User::findOrFail($trx->user_id);
        dump("user->", $user->id);

        $meter1 = Meter::where('user_id', $user->user_id)->firstOrFail();
        dump("meter_with_uid->", $meter1->id);

        $meter = Meter::where('meterNo', $user->meterNo)->first();
        // dump($meter);
        dump([
            'user' => $user?->toArray(),
            'meter_user_id' => $meter1?->toArray(),
            '$meter_meter_no' => $meter?->toArray(),
            'meter_no' => $user?->meterNo,
            'user_id' => $user?->user_id,
            'meter_no_equal_strong' => $user?->meterNo === $meter1?->meterNo,
            'meter_no_equal_weak' => $user?->meterNo == $meter1?->meterNo

        ]);

        $tariffId = $action_payload['tariff_id'];
        $unit = $action_payload['vend_amount_kw_per_naira'];
        $vat = $action_payload['vat_amount'];
        $needKct = $meter->NeedKCT;
        $vending_amount = $action_payload['vending_amount'];

        $meter->getNewToken($tariffId, $unit, $this->reference, $vat, $vending_amount, $verify='null');

        return true;
    }
}
