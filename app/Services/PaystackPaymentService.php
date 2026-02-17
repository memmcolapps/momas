<?php

namespace App\Services;

use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Http;
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

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->paystack_secret,
        ])->post($this->payment_endpoint, $databody);

        $var = $response->json();
        $status = $var['status'] ?? false;
    }

    public  function verifyTransaction($transactionId)
    {

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->paystack_secret}",
            'Cache-Control' => 'no-cache',
        ])
        ->timeout(15)
        ->retry(3, 1000) // retry 3 times, 1s delay
        ->get("https://api.paystack.co/transaction/verify/{$transactionId}")
        ->throw();

        // Optional: check if request failed
        if ($response->failed()) {
            return [
                'status' => false,
                'message' => 'Transaction verification failed',
                'error' => $response->body()
            ];
        }

        $response_data = $response->json();
        $message = '';
        $status = $response_data['status'];
        $payment_success = $response_data['data']['status'] === 'success';

        // check if ref was found
        if ($status === 'success') {
            // change message based on payment status
            switch ($response_data['data']['status']) {
                case 'success':
                    $message = 'Transaction Successful, Payment recieved';
                    break;
                case 'failed':
                    $message = 'Transaction Failed, Ask customer to retry';
                    break;
                case 'pending':
                    $message = 'Transaction Incomplete, check again later'; // preferably poll but try return message for now
                    break;
            }
        }

        return [
            'payment_status' => $payment_success,
            'message' => $message,
        ];

        return json_decode($response, true);
    }


    public static function handlePaystackWebhook($data) {

    }
}
