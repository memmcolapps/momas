<?php

namespace App\Services;

use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class PaystackPaymentService {
    protected $paystack_public;
    protected $paystack_secret;
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
            throw new Exception('Cannot find required keys to initialize Paystack');
        }

        if (empty($settings->paystack_public) || empty($settings->paystack_secret)) {
            throw new Exception('Paystack API keys are not configured');
        }

        $this->paystack_public = $settings->paystack_public;
        $this->paystack_secret = $settings->paystack_secret;
        $this->payment_endpoint = config('constants.paystack_payment_url');
    }

    /**
     * Get Paystack public key for client-side integration
     *
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->paystack_public;
    }

    /**
     * Make a payment using Paystack
     *
     * @param array $data Payment data containing 'amount' and 'email'
     * @return array Payment initialization response
     * @throws InvalidArgumentException
     */
    public function makePayment(array $data): array
    {
        $requiredParameters = ['amount', 'email', 'sub_account', 'metadata'];
        $missingParameters = array_diff($requiredParameters, array_keys($data));

        if (!empty($missingParameters)) {
            throw new InvalidArgumentException("Missing required parameters: " . implode(', ', $missingParameters));
        }

        // Generate unique transaction reference for this payment
        $transactionRef = generate_unique_string('MOMAS');
        $metadata = $data['metadata'] ?? [];
        $metadata['ref'] = $transactionRef;
        $metadata['custom_fields'] = $metadata['custom_fields'] ?? [
            [
                "display_name" => "Payment Reference",
                "variable_name" => "payment_ref",
                "value" => $transactionRef
            ]
        ];

        $dataBody = [
            "amount" => (int) ($data['amount'] * 100), // Paystack expects amount in kobo
            "email" => $data['email'],
            "ref" => $transactionRef,
            "callback_url" => url('') . "/paystack-check",
            "subaccount" => $data['sub_account'],
            "metadata" => $metadata,
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->paystack_secret,
            ])->post($this->payment_endpoint, $dataBody);

            $responseData = $response->json();

            if ($response->failed() || !($responseData['status'] ?? false)) {
                return [
                    'status' => false,
                    'message' => $responseData['message'] ?? 'Payment initialization failed',
                    'data' => $responseData['data'] ?? null,
                ];
            }

            return [
                'status' => true,
                'message' => 'Payment initialized successfully',
                'data' => $responseData['data'] ?? null,
                'reference' => $transactionRef,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Verify a Paystack transaction
     *
     * @param string $transactionId The transaction reference to verify
     * @return array Verification result
     */
    public function verifyTransaction(string $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->paystack_secret}",
                'Cache-Control' => 'no-cache',
            ])
            ->timeout(15)
            ->retry(3, 1000)
            ->get("https://api.paystack.co/transaction/verify/{$transactionId}");

            if ($response->failed()) {
                return [
                    'status' => false,
                    'message' => 'Transaction verification failed',
                    'error' => $response->body()
                ];
            }

            $responseData = $response->json();
            $status = $responseData['status'] ?? false;
            $paymentStatus = $responseData['data']['status'] ?? 'failed';

            if (!$status) {
                return [
                    'status' => false,
                    'message' => $responseData['message'] ?? 'Transaction verification failed',
                    'payment_status' => false,
                ];
            }

            $message = '';
            switch ($paymentStatus) {
                case 'success':
                    $message = 'Transaction Successful, Payment received';
                    break;
                case 'failed':
                    $message = 'Transaction Failed, Ask customer to retry';
                    break;
                case 'pending':
                    $message = 'Transaction Incomplete, check again later';
                    break;
                default:
                    $message = 'Unknown transaction status';
            }

            return [
                'status' => true,
                'payment_status' => $paymentStatus === 'success',
                'message' => $message,
                'data' => $responseData['data'] ?? null,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Transaction verification failed: ' . $e->getMessage(),
                'payment_status' => false,
            ];
        }
    }


    /**
     * Handle Paystack webhook events
     *
     * @param array $data The webhook payload data
     * @return array Response indicating success or failure
     */
    public static function handlePaystackWebhook(array $data): array
    {
        $event = $data['event'] ?? '';
        $paymentData = $data['data'] ?? [];

        // Verify the webhook signature to ensure it's from Paystack
        $headers = getallheaders();
        $secret = (new self())->paystack_secret;
        if (!self::verifyWebhookSignature($headers, json_encode($data), $secret)) {
            return [
                'status' => false,
                'message' => 'Invalid webhook signature',
            ];
        }

        try {
            switch ($event) {
                case 'charge.success':
                    return self::handleSuccessfulCharge($paymentData);

                case 'charge.failed':
                    return self::handleFailedCharge($paymentData);

                case 'charge.pending':
                    return self::handlePendingCharge($paymentData);

                case 'transfer.success':
                    return self::handleSuccessfulTransfer($paymentData);

                case 'transfer.failed':
                    return self::handleFailedTransfer($paymentData);

                default:
                    return [
                        'status' => true,
                        'message' => 'Event received but not processed: ' . $event,
                    ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Webhook processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle successful charge event
     *
     * @param array $paymentData Payment data from webhook
     * @return array Response
     */
    protected static function handleSuccessfulCharge(array $paymentData): array
    {
        $reference = $paymentData['reference'] ?? '';
        $amount = $paymentData['amount'] ?? 0;
        $customerEmail = $paymentData['customer']['email'] ?? '';

        // Process the successful payment - update your transaction records
        // Example: Transaction::where('reference', $reference)->update(['status' => 'completed', ...]);

        return [
            'status' => true,
            'message' => 'Successful charge processed',
            'reference' => $reference,
            'amount' => $amount,
        ];
    }

    /**
     * Handle failed charge event
     *
     * @param array $paymentData Payment data from webhook
     * @return array Response
     */
    protected static function handleFailedCharge(array $paymentData): array
    {
        $reference = $paymentData['reference'] ?? '';

        // Process the failed payment - update your transaction records here
        // Example: Transaction::where('reference', $reference)->update(['status' => 'failed', ...]);

        return [
            'status' => true,
            'message' => 'Failed charge processed',
            'reference' => $reference,
        ];
    }

    /**
     * Handle pending charge event
     *
     * @param array $paymentData Payment data from webhook
     * @return array Response
     */
    protected static function handlePendingCharge(array $paymentData): array
    {
        $reference = $paymentData['reference'] ?? '';

        // Process the pending payment - update your transaction records here
        // Example: Transaction::where('reference', $reference)->update(['status' => 'pending', ...]);

        return [
            'status' => true,
            'message' => 'Pending charge processed',
            'reference' => $reference,
        ];
    }

    /**
     * Handle successful transfer event
     *
     * @param array $transferData Transfer data from webhook
     * @return array Response
     */
    protected static function handleSuccessfulTransfer(array $transferData): array
    {
        return [
            'status' => true,
            'message' => 'Successful transfer processed',
            'reference' => $transferData['reference'] ?? '',
        ];
    }

    /**
     * Handle failed transfer event
     *
     * @param array $transferData Transfer data from webhook
     * @return array Response
     */
    protected static function handleFailedTransfer(array $transferData): array
    {
        return [
            'status' => true,
            'message' => 'Failed transfer processed',
            'reference' => $transferData['reference'] ?? '',
        ];
    }

    protected static function verifyWebhookSignature(array $headers, string $payload, string $secret): bool
    {
        $signature = $headers['x-paystack-signature'] ?? '';
        if (empty($signature)) {
            return false;
        }

        $computedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($computedSignature, $signature);
    }
}
