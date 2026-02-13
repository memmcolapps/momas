<?php

namespace App\Services;

use App\Models\Setting;
use Exception;
use InvalidArgumentException;

class PaystackPaymentService {
    protected $paystack_public;
    protected $paystack_secret;
    protected $trx_id;
    protected $payment_endpoint;

    public function __construct()
    {
        $this->initializePaystackSettings();
    }

    /**
     * Initialize Paystack settings from the database
     */
    protected function initializePaystackSettings()
    {
        $settings = Setting::where('id', 1)->first();

        if (! $settings) {
            throw new Exception('cannot find required keys to initialize paystack');
        }

        $this->paystack_public = $settings->paystack_public;
        $this->paystack_secret = $settings->paystack_secret;
        $this->payment_endpoint = config('constants.paystack_payment_url');
        $this->trx_id = generate_unique_string('MOMAS');
    }

    public function makePayment($data) {
        $parameters = [
            'amount',
            'email'
        ];

        if ( array_diff(
            array_keys($data),
            $parameters
        ) ) {
            throw new InvalidArgumentException("arguments to make payment must contain" . implode(',', $parameters));
        }


        $databody = array(
            "amount" => $data->amount * 100,
            "email" => $data->email,
            "ref" => $this->trx_id,
            'callback_url' => url('') . "/paystack-check",
            'metadata' => ["ref" => $this->trx_id],

        );

        $body = json_encode($databody);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->payment_endpoint,
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
                'Authorization: Bearer ' . $this->paystack_secret,
            ),
        ));

        $var = curl_exec($curl);
        curl_close($curl);
        $var = json_decode($var);
        $status = $var->status;

    }

    public static function handlePaystackWebhook($data) {

    }
}
