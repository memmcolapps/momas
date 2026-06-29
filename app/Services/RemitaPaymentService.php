<?php

namespace App\Services;

use App\Contracts\PaymentServiceInterface;
use App\Models\Logger;
use App\Models\Setting;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class RemitaPaymentService implements PaymentServiceInterface
{
    protected string $merchantId;
    protected string $apiKey;
    protected string $serviceTypeId;
    protected string $baseUrl;
    protected string $environment;

    public function __construct()
    {
        $this->initializeRemitaSettings();
    }

    /**
     * Initialize Remita settings from the database
     */
    protected function initializeRemitaSettings(): void
    {
        $settings = Setting::where('id', 1)->first();

        if (! $settings) {
            throw new Exception('Cannot find required keys to initialize Remita');
        }

        if (empty($settings->remita_merchant_id) || empty($settings->remita_api_key)) {
            throw new Exception('Remita API keys are not configured');
        }

        $this->merchantId    = $settings->remita_merchant_id;
        $this->apiKey        = $settings->remita_api_key;
        $this->serviceTypeId = $settings->remita_service_type_id ?? '';
        $this->environment   = 'live';

        if (in_array(env('APP_ENV'), ['local', 'staging', 'stg', 'lcl'])) {
            $this->merchantId    = env('REMITA_TEST_MERCHANT_ID');
            $this->apiKey        = env('REMITA_TEST_API_KEY');
            $this->serviceTypeId = env('REMITA_TEST_SERVICE_TYPE_ID', '');
            $this->environment   = 'test';
        }

        $this->baseUrl = $this->environment === 'live'
            ? 'https://login.remita.net/remita'
            : 'https://remitademo.net/remita';
    }

    // -------------------------------------------------------------------------
    // Interface methods
    // -------------------------------------------------------------------------

    /**
     * Returns the merchant ID (analogous to a public key for client context)
     */
    public function getPublicKey(): ?string
    {
        return $this->merchantId;
    }

    /**
     * Returns the API key (secret)
     */
    public function getSecretKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Generate a Remita Retrieval Reference (RRR) to initiate a payment.
     *
     * Required $data keys:
     *   - amount        (numeric, in Naira — NOT kobo)
     *   - email         (payer email)
     *   - payerName     (payer full name)
     *   - payerPhone    (payer phone number)
     *   - description   (narration / payment description)
     *
     * Optional:
     *   - serviceTypeId (overrides the configured default)
     *   - orderId       (your internal order reference; auto-generated if absent)
     *   - customFields  (array of ['name'=>…,'value'=>…,'type'=>…] maps)
     *
     * @throws InvalidArgumentException
     */
    public function makePayment(array $data): array
    {
        $required = ['amount', 'email', 'payerName', 'payerPhone', 'description'];
        $missing  = array_diff($required, array_keys($data));

        if (! empty($missing)) {
            throw new InvalidArgumentException(
                'Missing required parameters: ' . implode(', ', $missing)
            );
        }

        $orderId       = $data['orderId']       ?? generate_unique_string('RMT');
        $serviceTypeId = $data['serviceTypeId'] ?? $this->serviceTypeId;
        $amount        = (string) $data['amount']; // Remita expects a string

        // Remita auth hash: SHA512(merchantId + serviceTypeId + orderId + amount + apiKey)
        $hash = hash('sha512', $this->merchantId . $serviceTypeId . $orderId . $amount . $this->apiKey);

        $body = [
            'serviceTypeId' => $serviceTypeId,
            'amount'        => $amount,
            'orderId'       => $orderId,
            'payerName'     => $data['payerName'],
            'payerEmail'    => $data['email'],
            'payerPhone'    => $data['payerPhone'],
            'description'   => $data['description'],
        ];

        if (! empty($data['customFields'])) {
            $body['customFields'] = $data['customFields'];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => "remitaConsumerKey={$this->merchantId},remitaConsumerToken={$hash}",
            ])->post("{$this->baseUrl}/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit", $body);

            $responseData = $response->json();

            // Remita wraps its real payload in a stringified JSON field called `statuscode`/`RRR`
            // On success: statuscode === "025" and RRR is populated
            $statusCode = $responseData['statuscode'] ?? null;
            $rrr        = $responseData['RRR']        ?? null;

            if ($response->failed() || $statusCode !== '025' || empty($rrr)) {
                Logger::warning('Remita payment initialization failed', [
                    'response' => $responseData,
                    'order_id' => $orderId,
                ]);

                return [
                    'status'  => false,
                    'message' => $responseData['status'] ?? 'Payment initialization failed',
                    'data'    => $responseData,
                ];
            }

            return [
                'status'    => true,
                'message'   => 'Payment initialized successfully',
                'reference' => $orderId,
                'data'      => [
                    'rrr'           => $rrr,
                    'orderId'       => $orderId,
                    'amount'        => $amount,
                    'paymentUrl'    => $this->buildPaymentUrl($rrr),
                ],
            ];
        } catch (Exception $e) {
            Logger::warning('Remita makePayment exception', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ]);

            return [
                'status'  => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * Verify a Remita transaction by RRR or your internal orderId.
     *
     * @param string|int $transactionId  The RRR issued by Remita
     */
    public function verifyTransaction(string|int $transactionId): array
    {
        $rrr  = (string) $transactionId;
        $hash = hash('sha512', $rrr . $this->apiKey . $this->merchantId);

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => "remitaConsumerKey={$this->merchantId},remitaConsumerToken={$hash}",
            ])
            ->timeout(15)
            ->retry(3, 1000)
            ->get("{$this->baseUrl}/exapp/api/v1/send/api/echannelsvc/{$this->merchantId}/{$rrr}/{$hash}/status.reg");

            if ($response->failed()) {
                return [
                    'status'         => false,
                    'message'        => 'Transaction verification failed',
                    'payment_status' => null,
                    'error'          => $response->body(),
                ];
            }

            $responseData  = $response->json();

            // Remita status codes:  01 = successful, 021 = pending, 062 = failed
            $statusCode    = $responseData['status']      ?? null;
            $paymentStatus = $this->normalizeStatus($statusCode);
            $isSuccessful  = $paymentStatus === 'success';

            return [
                'status'         => true,
                'message'        => $this->statusMessage($paymentStatus),
                'payment_status' => $paymentStatus,
                'is_successful'  => $isSuccessful,
                'data'           => [
                    'rrr'            => $rrr,
                    'reference'      => $responseData['orderId']      ?? null,
                    'amount'         => $responseData['amount']       ?? null,
                    'currency'       => 'NGN',
                    'customer_email' => $responseData['payerEmail']   ?? '',
                    'channel'        => $responseData['channel']      ?? '',
                    'paid_at'        => $responseData['transactiontime'] ?? null,
                    'status'         => $paymentStatus,
                    'raw_status'     => $statusCode,
                ],
            ];
        } catch (Exception $e) {
            return [
                'status'         => false,
                'message'        => 'Transaction verification failed: ' . $e->getMessage(),
                'payment_status' => null,
            ];
        }
    }

    /**
     * Poll Remita until the transaction leaves a pending state.
     */
    public function pollTransactionStatus(
        string $reference,
        int $maxAttempts = 10,
        int $intervalSeconds = 5
    ): array {
        $attempt = 0;
        $hash    = hash('sha512', $reference . $this->apiKey . $this->merchantId);

        try {
            while ($attempt < $maxAttempts) {
                $attempt++;

                $response = Http::withHeaders([
                    'Accept'        => 'application/json',
                    'Authorization' => "remitaConsumerKey={$this->merchantId},remitaConsumerToken={$hash}",
                ])
                ->timeout(30)
                ->get("{$this->baseUrl}/exapp/api/v1/send/api/echannelsvc/{$this->merchantId}/{$reference}/{$hash}/status.reg");

                if ($response->failed()) {
                    return [
                        'status'         => false,
                        'message'        => 'Failed to poll transaction status',
                        'error'          => $response->body(),
                        'payment_status' => null,
                        'is_successful'  => false,
                        'data'           => null,
                    ];
                }

                $responseData  = $response->json();
                $statusCode    = $responseData['status'] ?? null;
                $paymentStatus = $this->normalizeStatus($statusCode);

                if (in_array($paymentStatus, ['success', 'failed', 'abandoned'])) {
                    return $this->formatPollResponse($responseData, $reference);
                }

                if ($paymentStatus === 'pending') {
                    sleep($intervalSeconds);
                    continue;
                }

                // Unexpected status — bail out
                return $this->formatPollResponse($responseData, $reference);
            }

            return [
                'status'         => false,
                'message'        => 'Polling timeout reached',
                'payment_status' => 'pending',
                'is_successful'  => false,
                'data'           => null,
            ];
        } catch (Exception $e) {
            return [
                'status'         => false,
                'message'        => 'Failed to poll transaction status: ' . $e->getMessage(),
                'payment_status' => null,
                'is_successful'  => false,
                'data'           => null,
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Webhook
    // -------------------------------------------------------------------------

    /**
     * Handle inbound Remita payment notification (IPN).
     *
     * Remita sends a POST with a JSON body; authentication is verified via
     * an HMAC signature in the Authorization header.
     */
    public static function handleRemitaWebhook(Request $request): array
    {
        $signature = $request->header('Authorization');
        $secret    = (new self())->apiKey;

        if (! self::verifyWebhookSignature($signature, $request, $secret)) {
            return [
                'status'  => false,
                'message' => 'Invalid webhook signature',
            ];
        }

        $data   = $request->all();
        $event  = $data['status']         ?? '';    // e.g. "01", "021", "062"
        $rrr    = $data['rrr']            ?? '';
        $amount = ($data['amount']        ?? 0);

        try {
            $paymentStatus = (new self())->normalizeStatus($event);

            switch ($paymentStatus) {
                case 'success':
                    return self::handleSuccessfulCharge($data);

                case 'failed':
                    return self::handleFailedCharge($data);

                case 'pending':
                    return self::handlePendingCharge($data);

                default:
                    return [
                        'status'  => true,
                        'message' => "Event received but not processed: {$event}",
                    ];
            }
        } catch (Exception $e) {
            return [
                'status'  => false,
                'message' => 'Webhook processing failed: ' . $e->getMessage(),
            ];
        }
    }

    protected static function handleSuccessfulCharge(array $paymentData): array
    {
        $rrr    = $paymentData['rrr']    ?? '';
        $amount = $paymentData['amount'] ?? 0;

        $transaction = Transaction::where('trx_id', $rrr)->first();

        if ($transaction) {
            $transaction->status = 3; // same convention as Paystack service
            $transaction->save();
            // Dispatch further processing job here if needed
        }

        return [
            'status'  => true,
            'message' => 'Successful charge processed',
            'rrr'     => $rrr,
            'amount'  => $amount,
        ];
    }

    protected static function handleFailedCharge(array $paymentData): array
    {
        $rrr = $paymentData['rrr'] ?? '';

        $transaction = Transaction::where('trx_id', $rrr)->first();

        if ($transaction) {
            $transaction->status = 1;
            $transaction->save();
        }

        return [
            'status'  => true,
            'message' => 'Failed charge processed',
            'rrr'     => $rrr,
        ];
    }

    protected static function handlePendingCharge(array $paymentData): array
    {
        $rrr = $paymentData['rrr'] ?? '';

        $transaction = Transaction::where('trx_id', $rrr)->first();

        if ($transaction) {
            $transaction->status = 0;
            $transaction->save();
        }

        return [
            'status'  => true,
            'message' => 'Pending charge processed',
            'rrr'     => $rrr,
        ];
    }

    // -------------------------------------------------------------------------
    // Signature verification
    // -------------------------------------------------------------------------

    /**
     * Verify the inbound Remita IPN signature.
     *
     * Remita computes: SHA512(rrr + apiKey + merchantId) and sends it
     * in the Authorization header as a plain hex string.
     */
    public static function verifyWebhookSignature(?string $signature, $payload, string $secret): bool
    {
        if (! $signature) {
            return false;
        }

        // For Remita IPN the body contains the rrr we can re-hash against
        $data = is_string($payload) ? json_decode($payload, true) : $payload->all();
        $rrr  = $data['rrr'] ?? '';

        $instance          = new self();
        $computedSignature = hash('sha512', $rrr . $instance->apiKey . $instance->merchantId);

        return hash_equals($computedSignature, $signature);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Map Remita numeric status codes to the same plain-English statuses
     * used throughout the Paystack service so the rest of the app stays uniform.
     *
     * Remita docs:
     *   01  → Successful
     *   021 → Pending / awaiting payment
     *   062 → Failed / reversed
     *   023 → Cancelled by user (treated as abandoned)
     */
    protected function normalizeStatus(?string $code): string
    {
        return match ($code) {
            '01'    => 'success',
            '021'   => 'pending',
            '023'   => 'abandoned',
            '062'   => 'failed',
            default => 'unknown',
        };
    }

    protected function statusMessage(string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'success'   => 'Transaction completed successfully',
            'failed'    => 'Transaction failed',
            'pending'   => 'Transaction is still pending',
            'abandoned' => 'Transaction was abandoned by customer',
            default     => 'Unknown transaction status',
        };
    }

    private function formatPollResponse(array $responseData, string $rrr): array
    {
        $statusCode    = $responseData['status'] ?? null;
        $paymentStatus = $this->normalizeStatus($statusCode);

        return [
            'status'         => true,
            'message'        => $this->statusMessage($paymentStatus),
            'payment_status' => $paymentStatus,
            'is_successful'  => $paymentStatus === 'success',
            'data'           => [
                'rrr'            => $rrr,
                'reference'      => $responseData['orderId']         ?? null,
                'amount'         => $responseData['amount']          ?? null,
                'currency'       => 'NGN',
                'customer_email' => $responseData['payerEmail']      ?? '',
                'channel'        => $responseData['channel']         ?? '',
                'paid_at'        => $responseData['transactiontime'] ?? null,
                'status'         => $paymentStatus,
            ],
        ];
    }

    /**
     * Build the hosted-page URL a client can redirect to for payment.
     */
    private function buildPaymentUrl(string $rrr): string
    {
        $hash = hash('sha512', $this->merchantId . $this->apiKey . $rrr);

        return "{$this->baseUrl}/ecomm/finalize.reg"
            . "?merchantId={$this->merchantId}"
            . "&rrr={$rrr}"
            . "&hash={$hash}";
    }
}
