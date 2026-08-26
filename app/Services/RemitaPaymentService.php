<?php

namespace App\Services;

use App\Contracts\PaymentServiceInterface;
use App\Jobs\ProcessRemitaWebhook;
use App\Models\Logger;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class RemitaPaymentService implements PaymentServiceInterface
{
    protected $remita_merchant_id;
    protected $remita_api_key;
    protected $remita_service_type_id;
    protected $payment_endpoint;
    protected $status_endpoint;
    protected $rrr_generate_url;
    protected $remita_env;

    public function __construct()
    {
        $this->initializeRemitaSettings();
    }

    /**
     * Initialize Remita settings from the database
     */
    protected function initializeRemitaSettings()
    {
        $isTest = in_array(env('APP_ENV'), ['local', 'staging', 'stg', 'lcl']);

        if ($isTest) {
            $this->remita_merchant_id = config('services.remita.test_merchant_id');
            $this->remita_api_key = config('services.remita.test_api_key');
            $this->remita_service_type_id = config('services.remita.test_service_type_id');
        } else {
            $this->remita_merchant_id = config('services.remita.merchant_id');
            $this->remita_api_key = config('services.remita.api_key');
            $this->remita_service_type_id = config('services.remita.service_type_id');
        }

        if (empty($this->remita_merchant_id) || empty($this->remita_api_key) || empty($this->remita_service_type_id)) {
            throw new Exception('Remita API keys are not configured');
        }

        $this->remita_env = $isTest ? 'test' : 'live';
        $this->payment_endpoint = config('constants.remita_payment_endpoint');
        $this->status_endpoint = config('constants.remita_status_endpoint');
        $this->rrr_generate_url = config('constants.remita_rrr_generate_endpoint');
    }

    /**
     * Get Remita merchant id for client-side integration (analogous to Paystack's public key)
     *
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->remita_merchant_id;
    }

    /**
     * Get Remita API key (analogous to Paystack's secret key)
     *
     * @return string|null
     */
    public function getSecretKey(): ?string
    {
        return $this->remita_api_key;
    }

    /**
     * Get the Remita hosted payment page URL (environment-specific:
     * demo.remita.net for test, configured production host for live).
     *
     * @return string
     */
    public function hostedPaymentPageUrl(): string
    {
        return rtrim((string) config('services.remita.payment_page_url'), '/');
    }

    /**
     * Build the hidden-field payload Remita's hosted payment page
     * (onepage/api/v1/so.spa) expects for an RRR-initiated payment.
     *
     * The hash is SHA512 of rrr + api_key + merchant_id per Remita's
     * hosted checkout spec.
     *
     * @param string|int $rrr The Remita Retrieval Reference
     * @return array Hidden form fields (rrr, merchantId, hash)
     */
    public function hostedPaymentPayload(string | int $rrr): array
    {
        $rrr = (string) $rrr;

        return [
            'rrr' => $rrr,
            'merchantId' => $this->remita_merchant_id,
            'hash' => hash('sha512', $rrr . $this->remita_api_key . $this->remita_merchant_id),
        ];
    }

    /**
     * Generate a payment RRR using Remita's standard invoice flow
     *
     * @param array $data Payment data containing 'amount', 'email', 'name', 'phone', 'metadata'
     * @return array Payment initialization response (includes RRR + hosted checkout url)
     * @throws InvalidArgumentException
     */
    public function makePayment(array $data): array
    {
        $requiredParameters = ['amount', 'email', 'name', 'phone'];

        $missingParameters = array_diff($requiredParameters, array_keys($data));

        if (!empty($missingParameters)) {
            throw new InvalidArgumentException("Missing required parameters: " . implode(', ', $missingParameters));
        }

        // Generate unique order id for this payment
        $orderId = generate_unique_string('MOMAS');

        $amount = (string) $data['amount']; // Remita expects amount as a naira string, e.g. "1000.00" — NOT kobo

        $hash = hash('sha512', $this->remita_merchant_id . $this->remita_service_type_id . $orderId . $amount . $this->remita_api_key);

        // Standard invoice payload only — no "meta"/custom fields here. Custom
        // fields are a different Remita endpoint ("Generate Invoice with
        // Custom Field") with its own request shape.
        $dataBody = [
            "serviceTypeId" => $this->remita_service_type_id,
            "amount" => $amount,
            "orderId" => $orderId,
            "payerName" => $data['name'],
            "payerEmail" => $data['email'],
            "payerPhone" => $data['phone'],
            "description" => $data['description'] ?? 'Payment',
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => "remitaConsumerKey={$this->remita_merchant_id},remitaConsumerToken={$hash}",
            ])
            ->timeout(30)
            ->post($this->payment_endpoint, $dataBody);

            $responseData = $response->json();

            $statusCode = $responseData['statuscode'] ?? null;

            // '025' / '01' are Remita's "RRR generated" success codes depending on API version
            if ($response->failed() || !in_array((string) $statusCode, ['00', '025'], true)) {
                Logger::warning('Remita RRR generation failed for unknown reasons', [
                    'remita_response' => $responseData,
                ]);

                return [
                    'status' => false,
                    'message' => $responseData['status'] ?? 'Payment initialization failed',
                    'data' => $responseData ?? null,
                ];
            }

            $rrr = $responseData['RRR']
                ?? $responseData['rrr']
                ?? null;

            if (!$rrr) {
                Logger::warning('Remita RRR generation returned no RRR', [
                    'status_code' => $statusCode,
                    'response' => $responseData,
                ]);

                return [
                    'status' => false,
                    'message' => 'Remita did not return an RRR',
                    'data' => $responseData,
                ];
            }

            // There is no plain "redirect to this URL" checkout for the inline
            // flow — Remita's inline checkout is a JS widget
            // (remita-pay-inline.bundle.js -> RmPaymentEngine.init({...})) that
            // your frontend loads and feeds this RRR into, along with a
            // separate "inline public key" Remita issues you (NOT this
            // service's merchant id/api key — that pair is only for
            // server-to-server hashed calls). So the backend's job stops at
            // handing back the RRR; wire the widget up in your frontend per
            // https://www.remita.net/developers/#/payment/inline
            return [
                'status' => true,
                'message' => 'Payment initialized successfully',
                'data' => $responseData,
                'reference' => $orderId,
                'rrr' => $rrr,
            ];
        } catch (Exception $e) {
            Logger::warning('An Error Occurred', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return [
                'status' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Verify a Remita transaction by RRR
     *
     * @param string $transactionId The RRR to verify
     * @return array Verification result
     */
    public function verifyTransaction(string | int $transactionId): array
    {
        try {
            $hash = hash('sha512', $transactionId . $this->remita_api_key . $this->remita_merchant_id);

            $url = sprintf(
                '%s/%s/%s/%s/status.reg',
                rtrim($this->status_endpoint, '/'),
                $this->remita_merchant_id,
                $transactionId,
                $hash
            );

            $response = Http::withHeaders([
                'Authorization' => "remitaConsumerKey={$this->remita_merchant_id},remitaConsumerToken={$hash}",
                'Cache-Control' => 'no-cache',
            ])
            ->timeout(15)
            ->retry(3, 1000)
            ->get($url);

            if ($response->failed()) {
                return [
                    'status' => false,
                    'message' => 'Transaction verification failed',
                    'error' => $response->body(),
                    'payment_status' => null,
                ];
            }

            $responseData = $response->json();
            $statusCode = $responseData['status'] ?? null;
            $paymentStatus = $this->normalizeStatusCode($statusCode);

            $message = match ($paymentStatus) {
                'success' => 'Transaction completed successfully',
                'failed' => 'Transaction failed',
                'pending' => 'Transaction is still pending',
                default => 'Unknown transaction status: ' . $statusCode,
            };

            return [
                'status' => true,
                'message' => $message,
                'payment_status' => $paymentStatus,
                'is_successful' => $paymentStatus === 'success',
                'data' => [
                    'reference' => $responseData['orderId'] ?? $transactionId,
                    'rrr' => $transactionId,
                    'amount' => $responseData['amount'] ?? null,
                    'currency' => 'NGN',
                    'customer_email' => $responseData['payerEmail'] ?? '',
                    'channel' => $responseData['channel'] ?? '',
                    'paid_at' => $responseData['transactiondate'] ?? null,
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
     * Poll RRR status of a Remita transaction
     *
     * @param string $transactionReference The RRR to poll
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

                $hash = hash('sha512', $transactionReference . $this->remita_api_key . $this->remita_merchant_id);

                $url = sprintf(
                    '%s/%s/%s/%s/status.reg',
                    rtrim($this->status_endpoint, '/'),
                    $this->remita_merchant_id,
                    $transactionReference,
                    $hash
                );

                $response = Http::withHeaders([
                    'Authorization' => "remitaConsumerKey={$this->remita_merchant_id},remitaConsumerToken={$hash}",
                    'Cache-Control' => 'no-cache',
                ])
                ->timeout(15)
                ->retry(3, 1000)
                ->get($url);

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
                $paymentStatus = $this->normalizeStatusCode($responseData['status'] ?? null);

                // Stop polling if transaction is no longer pending
                if (in_array($paymentStatus, ['success', 'failed'])) {
                    return $this->formatPollResponse($responseData, $transactionReference);
                }

                // If still pending, wait before next attempt
                sleep($intervalSeconds);
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

    private function formatPollResponse(array $responseData, string $rrr): array
    {
        $paymentStatus = $this->normalizeStatusCode($responseData['status'] ?? null);

        return [
            'status' => true,
            'message' => match ($paymentStatus) {
                'success' => 'Transaction completed successfully',
                'failed' => 'Transaction failed',
                'pending' => 'Transaction is still pending',
                default => 'Unknown transaction status',
            },
            'payment_status' => $paymentStatus,
            'is_successful' => $paymentStatus === 'success',
            'data' => [
                'reference' => $responseData['orderId'] ?? null,
                'rrr' => $rrr,
                'amount' => $responseData['amount'] ?? null,
                'currency' => 'NGN',
                'customer_email' => $responseData['payerEmail'] ?? '',
                'channel' => $responseData['channel'] ?? '',
                'paid_at' => $responseData['transactiondate'] ?? null,
                'status' => $paymentStatus,
            ],
        ];
    }

    /**
     * Normalize Remita's numeric status codes into success/pending/failed
     */
    protected function normalizeStatusCode(?string $code): string
    {
        return match ((string) $code) {
            // Successful
            '00', '01' => 'success',

            // Still processing / awaiting confirmation
            '020',
            '021',
            '045' => 'pending',

            // Everything else is not a successful payment
            default => 'failed',
        };
    }

    /**
     * Handle a Remita IPN webhook call.
     *
     * IMPORTANT — two things differ from Paystack here:
     *
     * 1. Payload shape: Remita posts a JSON ARRAY of transaction objects with
     *    lowercase keys, e.g.
     *      [{ "rrr":"...", "orderRef":"...", "amount":7500.00,
     *         "payerEmail":"...", "transactiondate":"...", ... }]
     *    not a flat object with "RRR"/"orderId" like Paystack's webhook body.
     *
     * 2. Response contract: Remita's IPN listener expects your endpoint to
     *    respond with the plain text "OK" or "not ok" (per their docs) — NOT
     *    a JSON body. This method still returns a normal array (to match the
     *    rest of this class's interface); it's on the controller calling this
     *    to render `response_text` as the raw response body, e.g.
     *    `return response($result['response_text']);` — returning
     *    `response()->json($result)` here will make Remita think the IPN
     *    failed and it will keep retrying.
     *
     * The payload itself is also not cryptographically trustworthy on its
     * own, so this still requeries the RRR status directly rather than
     * trusting the posted amount/status.
     *
     * @param Request $request The webhook request
     * @return array Response indicating success or failure, including 'response_text'
     */
    public static function handleRemitaWebhook(Request $request): array
    {
        $payload = $request->all();

        // Remita posts a list of transactions; take the first entry.
        $entry = is_array($payload) && array_is_list($payload) ? ($payload[0] ?? []) : $payload;

        $rrr = $entry['rrr'] ?? $entry['RRR'] ?? null;

        if (! $rrr) {
            Logger::warning('Remita webhook received with no RRR', ['payload' => $payload]);

            return [
                'status' => false,
                'message' => 'Missing RRR in webhook payload',
                'response_text' => 'not ok',
            ];
        }

        $service = new self();

        try {
            $verification = $service->verifyTransaction($rrr);

            if (!$verification['status'] || !in_array($verification['payment_status'], ['success', 'pending'])) {
                return [
                    'status' => false,
                    'message' => 'Webhook processing failed: could not verify RRR',
                    'response_text' => 'not ok',
                ];
            }

            $orderId = $verification['data']['reference'] ?? ($entry['orderRef'] ?? null);
            $transaction = $orderId ? Transaction::where('trx_id', $orderId)->first() : null;

            if ($transaction) {
                DB::transaction(function () use ($transaction, $verification, $orderId) {
                    $paymentStatus = $verification['payment_status'];

                    if ($paymentStatus === 'success') {

                        // Only transition to "paid" once.
                        $updated = Transaction::whereKey($transaction->id)
                            ->where('status', '!=', 3)
                            ->update([
                                'status' => 3,
                                'updated_at' => now(),
                            ]);

                        // Only dispatch if this request actually performed
                        // the transition to successful.
                        if ($updated === 1) {
                            ProcessRemitaWebhook::dispatch($orderId);
                        }

                    } elseif ($paymentStatus === 'pending') {

                        Transaction::whereKey($transaction->id)
                            ->where('status', '!=', 3) // Never downgrade a paid transaction
                            ->update([
                                'status' => 0,
                                'updated_at' => now(),
                            ]);
                    }
                });
            }

            return [
                'status' => true,
                'message' => 'Webhook processed',
                'reference' => $orderId,
                'rrr' => $rrr,
                'payment_status' => $verification['payment_status'],
                // Per Remita docs: status code 00 or 01 -> "OK", else "not ok"
                'response_text' => $verification['payment_status'] === 'success' ? 'OK' : 'not ok',
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Webhook processing failed: ' . $e->getMessage(),
                'response_text' => 'not ok',
            ];
        }
    }
}
