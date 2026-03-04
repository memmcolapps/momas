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

        $actionables = array_merge($features, [
            'momas_meter_web' => 1
        ]);

        if ($actionables[$action['action']] != 1) {
            throw new Exception ('Sorry The requested feature is not available at the moment');
        }
        dump("got here 41");

        $handler = match ($action['action']) {
            'momas_meter', 'momas_meter_web' => fn() => $init->handleBuyTokenRequest(),
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
}
