<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Logger;
use InvalidArgumentException;

class PaystackPaymentService {
    protected $paystack_public;
    protected $paystack_secret;
    protected $payment_endpoint;
    protected $paystack_env;

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
        $this->paystack_env = 'live';
        if (in_array(env('APP_ENV'), ['local', 'staging', 'stg', 'lcl'])) {
            $this->paystack_public = env('PAYSTACK_TEST_PUBLIC_KEY');
            $this->paystack_secret = env('PAYSTACK_TEST_SECRET_KEY');
            $this->paystack_env = 'test';
        }
        $this->payment_endpoint = config('constants.paystack_payment_endpoint');
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

    public function getSecretKey(): ?string
    {
        return $this->paystack_secret;
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
        if ($this->paystack_env === 'test') {
            unset($requiredParameters[array_search('sub_account', $requiredParameters)], $data['sub_account']);
        }

        // dd()
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
        // dd($transactionRef);

        $dataBody = [
            "amount" => (int) ($data['amount']), // Paystack expects amount in kobo
            "email" => $data['email'],
            "reference" => $transactionRef,
            "callback_url" => url('') . "/paystack-check",
            "metadata" => $metadata,
        ];

        if ($this->paystack_env === 'live') {
            $dataBody["subaccount"] = $data['sub_account'];

            $validate_subaccount = self::validateSubaccount($data['sub_account']);

            if (! $validate_subaccount['valid']) {
                Logger::warning("User with email: {$data['email']} passed has invalid estate id on db ---->>>> ". Carbon::now()->toIsoString());

                return [
                    'status' => false,
                    'message' => $validate_subaccount['message'],
                    'data' => $validate_subaccount['data'] ?? null,
                ];
            }
        }

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
                    'error' => $response->body(),
                    'payment_status' => null,
                ];
            }

            $responseData = $response->json();
            $status = $responseData['status'] ?? false;
            $paymentStatus = $responseData['data']['status'] ?? 'failed';

            if (!$status) {
                return [
                    'status' => false,
                    'message' => $responseData['message'] ?? 'Transaction verification failed',
                    'payment_status' => null,
                ];
            }

            // Extract detailed transaction information
            $transactionData = $responseData['data'] ?? [];
            $amount = $transactionData['amount'] ?? 0;
            $currency = $transactionData['currency'] ?? 'NGN';
            $customerEmail = $transactionData['customer']['email'] ?? '';
            $channel = $transactionData['channel'] ?? '';
            $reference = $transactionData['reference'] ?? $transactionId;
            $paidAt = $transactionData['paid_at'] ?? null;

            $message = '';
            $isSuccessful = false;

            switch ($paymentStatus) {
                case 'success':
                    $message = 'Transaction completed successfully';
                    $isSuccessful = true;
                    break;
                case 'failed':
                    $message = 'Transaction failed';
                    break;
                case 'pending':
                    $message = 'Transaction is still pending';
                    break;
                case 'abandoned':
                    $message = 'Transaction was abandoned by customer';
                    break;
                default:
                    $message = 'Unknown transaction status: ' . $paymentStatus;
            }

            return [
                'status' => true,
                'message' => $message,
                'payment_status' => $paymentStatus,
                'is_successful' => $isSuccessful,
                'data' => [
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => $currency,
                    'customer_email' => $customerEmail,
                    'channel' => $channel,
                    'paid_at' => $paidAt,
                    'status' => $paymentStatus,
                ],
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Transaction verification failed: ' . $e->getMessage(),
                'payment_status' => null,
            ];
        }
    }

    /**
     * Poll transaction status of a Paystack transaction
     *
     * @param string $transactionReference
     * @param int $maxAttempts
     * @param int $intervalSeconds
     * @return array
     */
    public function pollTransactionStatus(
        string $transactionReference,
        int $maxAttempts = 10,
        int $intervalSeconds = 5
    ): array {
        $attempt = 0;

        try {
            while ($attempt < $maxAttempts) {
                $attempt++;

                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->paystack_secret}",
                    'Cache-Control' => 'no-cache',
                ])
                ->timeout(30)
                ->get("https://api.paystack.co/transaction/verify/{$transactionReference}");

                if ($response->failed()) {
                    return [
                        'status' => false,
                        'message' => 'Failed to poll transaction status',
                        'error' => $response->body(),
                        'payment_status' => null,
                        'is_successful' => false,
                        'data' => null,
                    ];
                }

                $responseData = $response->json();
                $paymentStatus = $responseData['data']['status'] ?? 'failed';

                // Stop polling if transaction is no longer pending
                if (in_array($paymentStatus, ['success', 'failed', 'abandoned'])) {
                    return $this->formatPollResponse($responseData);
                }

                // If still pending, wait before next attempt
                if ($paymentStatus === 'pending') {
                    sleep($intervalSeconds);
                    continue;
                }

                // Unknown status — stop polling
                return $this->formatPollResponse($responseData);
            }

            // Max attempts reached
            return [
                'status' => false,
                'message' => 'Polling timeout reached',
                'payment_status' => 'pending',
                'is_successful' => false,
                'data' => null,
            ];

        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to poll transaction status: ' . $e->getMessage(),
                'payment_status' => null,
                'is_successful' => false,
                'data' => null,
            ];
        }
    }


    private function formatPollResponse(array $responseData): array
    {
        $transactionData = $responseData['data'] ?? [];
        $paymentStatus = $transactionData['status'] ?? 'failed';

        return [
            'status' => true,
            'message' => match ($paymentStatus) {
                'success' => 'Transaction completed successfully',
                'failed' => 'Transaction failed',
                'pending' => 'Transaction is still pending',
                'abandoned' => 'Transaction was abandoned',
                default => 'Unknown transaction status',
            },
            'payment_status' => $paymentStatus,
            'is_successful' => $paymentStatus === 'success',
            'data' => [
                'reference' => $transactionData['reference'] ?? null,
                'amount' => $transactionData['amount'] ?? 0,
                'currency' => $transactionData['currency'] ?? 'NGN',
                'customer_email' => $transactionData['customer']['email'] ?? '',
                'channel' => $transactionData['channel'] ?? '',
                'paid_at' => $transactionData['paid_at'] ?? null,
                'status' => $paymentStatus,
            ],
        ];
    }



    /**
     * Handle Paystack webhook events
     *
     * @param request $ The webhook request
     * @return array Response indicating success or failure
     */
    public static function handlePaystackWebhook(Request $request): array
    {
        $data = $request->all();
        $event = $data['event'] ?? '';
        $paymentData = $data['data'] ?? [];

        // Verify the webhook signature to ensure it's from Paystack
        $signature = $request->header('x-paystack-signature');
        $secret = (new self())->paystack_secret;
        if (!self::verifyWebhookSignature($signature, $request, $secret)) {
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

    public static function verifyWebhookSignature(?string $signature, $payload, string $secret): bool
    {
        // 1. Handle null or empty signatures gracefully
        if (!$signature) {
            return false;
        }

        // 2. Ensure $payload is a string (the raw body)
        $content = is_string($payload) ? $payload : $payload->getContent();

        $computedSignature = hash_hmac('sha512', $content, $secret);

        // 3. Timing-attack safe comparison
        return hash_equals($computedSignature, $signature);
    }

    public static function validateSubaccount(string $subaccountCode): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . (new self())->paystack_secret,
            'Accept' => 'application/json',
        ])
        ->timeout(0.1)
        ->get("https://api.paystack.co/subaccount/{$subaccountCode}");

        $data = $response->json();

        if ($response->failed() || !($data['status'] ?? false)) {
            return [
                'valid' => false,
                'message' => $data['message'] ?? 'Invalid subaccount',
                'data' => null,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Subaccount is valid',
            'data' => $data['data'],
        ];
    }
}
