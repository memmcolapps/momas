<?php

namespace App\Services;

use App\Contracts\PaymentServiceInterface;
use App\Models\Logger;
use App\Models\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class FlutterwavePaymentService implements PaymentServiceInterface
{
    protected string $flutterwave_public;
    protected string $flutterwave_secret;
    protected string $payment_endpoint;
    protected string $flutterwave_env;

    /**
     * Environments considered non-production (test mode)
     */
    private const TEST_ENVIRONMENTS = ['local', 'staging', 'stg', 'lcl'];

    public function __construct()
    {
        $this->initializeFlutterwaveSettings();
    }

    // -------------------------------------------------------------------------
    // Initialisation
    // -------------------------------------------------------------------------

    /**
     * Load Flutterwave credentials from the database (or env overrides for
     * non-production environments).
     *
     * @throws Exception
     */
    protected function initializeFlutterwaveSettings(): void
    {
        $settings = Setting::where('id', 1)->first();

        if (! $settings) {
            throw new Exception('Cannot find required keys to initialize Flutterwave');
        }

        $this->flutterwave_env = $this->isTestEnvironment() ? 'test' : 'live';

        if (! $this->flutterwave_env === 'test') {
            if (empty($settings->flutterwave_public) || empty($settings->flutterwave_secret)) {
                throw new Exception('Flutterwave API keys are not configured');
            }
        }

        if ($this->flutterwave_env === 'test') {
            $this->flutterwave_public = env('FLUTTERWAVE_TEST_PUBLIC_KEY', $settings->flutterwave_public);
            $this->flutterwave_secret = env('FLUTTERWAVE_TEST_SECRET_KEY', $settings->flutterwave_secret);
        } else {
            $this->flutterwave_public = $settings->flutterwave_public;
            $this->flutterwave_secret = $settings->flutterwave_secret;
        }

        $this->payment_endpoint = config('constants.flutterwave_payment_endpoint');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Return the Flutterwave public key for client-side integration.
     */
    public function getPublicKey(): ?string
    {
        return $this->flutterwave_public;
    }

    /**
     * Return the Flutterwave secret key (server-side use only).
     */
    public function getSecretKey(): ?string
    {
        return $this->flutterwave_secret;
    }

    // -------------------------------------------------------------------------
    // Payment initialisation
    // -------------------------------------------------------------------------

    /**
     * Initialise a Flutterwave Standard payment and return a hosted checkout link.
     *
     * Required $data keys:
     *   - amount        (numeric)
     *   - email         (string)  customer e-mail
     *   - sub_account   (string)  Flutterwave sub-account ID for split payments
     *                             (not required / ignored in test environments)
     *   - metadata      (array)   arbitrary metadata; merged with the tx reference
     *
     * Optional $data keys:
     *   - customer_name        (string)
     *   - customer_phone       (string)
     *   - redirect_url         (string) overrides default redirect
     *   - customizations       (array)  title, description, logo
     *
     * @param  array $data
     * @return array{status: bool, message: string, data: array|null, reference?: string}
     *
     * @throws InvalidArgumentException when required parameters are absent
     */
    public function makePayment(array $data): array
    {
        // BUG FIX #2: Build required-parameter list correctly.
        // The original used array_search + unset which left non-contiguous keys,
        // causing array_diff to behave unpredictably.
        $requiredParameters = ['amount', 'email', 'metadata'];

        if ($this->flutterwave_env === 'live') {
            $requiredParameters[] = 'sub_account';
        }

        $missingParameters = array_diff($requiredParameters, array_keys($data));

        if (! empty($missingParameters)) {
            throw new InvalidArgumentException(
                'Missing required parameters: ' . implode(', ', $missingParameters)
            );
        }

        // Generate a unique transaction reference for idempotency tracking.
        $transactionRef = generate_unique_string('MOMAS');

        $metadata        = $data['metadata'] ?? [];
        $metadata['ref'] = $transactionRef;

        // BUG FIX #3: Removed the non-standard "custom_fields" key.
        // Flutterwave Standard does not support custom_fields inside `meta`.
        // That pattern belongs to Paystack. Any extra display info should be
        // passed as plain key-value pairs inside the meta object.

        // BUG FIX #4: Wrap the customer's email inside a `customer` object.
        // Flutterwave Standard requires customer.email, not a top-level email key.
        $dataBody = [
            'tx_ref'       => $transactionRef,
            'amount'       => (int) $data['amount'],   // Flutterwave expects whole units, not kobo
            'currency'     => 'NGN',
            'redirect_url' => url('') . "/payment-check",
            'customer'     => [
                'email'       => $data['email'],
                'name'        => $data['customer_name']  ?? '',
                'phonenumber' => $data['customer_phone'] ?? '',
            ],
            'meta'         => $metadata,
        ];

        // Optional checkout customisations (title, description, logo).
        if (! empty($data['customizations'])) {
            $dataBody['customizations'] = $data['customizations'];
        }

        // Attach the split-payment sub-account in live mode only.
        if ($this->flutterwave_env === 'live') {
            $validateSubaccount = self::validateSubaccount($data['sub_account']);

            if (! $validateSubaccount['valid']) {
                Logger::warning(sprintf(
                    'User %s provided an invalid sub-account [%s] at %s',
                    $data['email'],
                    $data['sub_account'],
                    Carbon::now()->toISOString()
                ));

                return [
                    'status'  => false,
                    'message' => $validateSubaccount['message'],
                    'data'    => $validateSubaccount['data'] ?? null,
                ];
            }

            $dataBody['subaccounts'] = [
                ['id' => $data['sub_account']],
            ];
        }

        try {
            $response     = Http::withHeaders($this->defaultHeaders())->post($this->payment_endpoint, $dataBody);
            $responseData = $response->json();

            if ($response->failed() || ! ($responseData['status'] === 'success')) {
                return [
                    'status'  => false,
                    'message' => $responseData['message'] ?? 'Payment initialization failed',
                    'data'    => $responseData['data'] ?? null,
                ];
            }

            return [
                'status'    => true,
                'message'   => 'Payment initialized successfully',
                'data'      => $responseData['data'] ?? null,
                'reference' => $transactionRef,
            ];
        } catch (Exception $e) {
            Logger::error('Flutterwave makePayment exception: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Sub-account validation
    // -------------------------------------------------------------------------

    /**
     * Validate that a Flutterwave sub-account ID exists and is active.
     *
     * @param  string $subaccountId
     * @return array{valid: bool, message: string, data: array|null}
     */
    public static function validateSubaccount(string $subaccountId): array
    {
        try {
            // BUG FIX #5: Rather than instantiating a full service object (which
            // reads the DB), resolve only the secret we need for this static call.
            $secret = static::resolveSecretKey();

            if (empty($secret)) {
                return [
                    'valid'   => false,
                    'message' => 'Flutterwave API keys not configured',
                    'data'    => null,
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$secret}",
                'Cache-Control' => 'no-cache',
            ])
            ->timeout(15)
            ->get("https://api.flutterwave.com/v3/subaccounts/{$subaccountId}");

            if ($response->failed()) {
                return [
                    'valid'   => false,
                    'message' => 'Failed to validate subaccount',
                    'data'    => null,
                ];
            }

            $responseData = $response->json();

            if (($responseData['status'] ?? '') === 'success') {
                return [
                    'valid'   => true,
                    'message' => 'Subaccount is valid',
                    'data'    => $responseData['data'] ?? null,
                ];
            }

            return [
                'valid'   => false,
                'message' => $responseData['message'] ?? 'Invalid subaccount',
                'data'    => null,
            ];
        } catch (Exception $e) {
            return [
                'valid'   => false,
                'message' => 'Subaccount validation failed: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Bank listing
    // -------------------------------------------------------------------------

    /**
     * Fetch the list of banks for a given country.
     *
     * Use this to populate a dropdown for users to select their bank before
     * creating a subaccount. The returned code field (e.g. "044") is what
     * you pass as account_bank in createSubaccount.
     *
     * @param  string $country  Country code e.g. "NG", "GH", "US", "KE", "UG", "RW", "TZ"
     * @return array{status: bool, message: string, data?: array|null}
     *
     * @throws RuntimeException when the API call fails
     */
    public function getBanks(string $country): array
    {
        if (empty($country)) {
            throw new InvalidArgumentException('Country code is required');
        }

        try {
            $response     = Http::withHeaders($this->defaultHeaders())
                ->get("https://api.flutterwave.com/v3/banks/{$country}");
            $responseData = $response->json();

            if ($response->failed() || ($responseData['status'] ?? '') !== 'success') {
                return [
                    'status'  => false,
                    'message' => $responseData['message'] ?? 'Failed to fetch banks',
                    'data'    => null,
                ];
            }

            return [
                'status'  => true,
                'message' => 'Banks retrieved successfully',
                'data'    => $responseData['data'] ?? [],
            ];
        } catch (Exception $e) {
            Logger::error('Flutterwave getBanks exception: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Failed to fetch banks: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Bank account verification
    // -------------------------------------------------------------------------

    /**
     * Verify a bank account to confirm it belongs to the expected owner.
     *
     * This is a recommended safety step to call before creating a subaccount.
     * It validates that the account number and bank code match the provided
     * account name, helping prevent errors in subaccount creation.
     *
     * Required $data keys:
     *   - account_bank   (string) Bank code (e.g., "044")
     *   - account_number (string) Bank account number
     *   - account_name   (string) Expected account holder name (for comparison)
     *
     * @param  array $data
     * @return array{status: bool, message: string, data?: array|null, verified?: bool}
     *
     * @throws InvalidArgumentException when required parameters are absent
     */
    public function verifyBankAccount(array $data): array
    {
        $requiredParameters = ['account_bank', 'account_number'];

        $missingParameters = array_diff($requiredParameters, array_keys($data));

        if (! empty($missingParameters)) {
            throw new InvalidArgumentException(
                'Missing required parameters: ' . implode(', ', $missingParameters)
            );
        }

        try {
            $response     = Http::withHeaders($this->defaultHeaders())
                ->post('https://api.flutterwave.com/v3/accounts/resolve', [
                    'account_bank' => $data['account_bank'],
                    'account_number' => $data['account_number'],
                ]);
            $responseData = $response->json();

            if ($response->failed() || ($responseData['status'] ?? '') !== 'success') {
                return [
                    'status'    => false,
                    'message'   => $responseData['message'] ?? 'Account verification failed',
                    'data'      => null,
                    'verified'  => false,
                ];
            }

            // Flutterwave returns the resolved account name in the response
            $resolvedData = $responseData['data'] ?? [];
            $resolvedName = $resolvedData['account_name'] ?? '';

            // If an account_name was provided, compare it with the resolved name
            $verified = true;
            $message  = 'Account verified successfully';

            if (! empty($data['account_name']) && ! empty($resolvedName)) {
                // Case-insensitive comparison with normalized spaces
                $normalizedExpected = strtolower(preg_replace('/\s+/', ' ', trim($data['account_name'])));
                $normalizedResolved = strtolower(preg_replace('/\s+/', ' ', trim($resolvedName)));

                if ($normalizedExpected !== $normalizedResolved) {
                    $verified = false;
                    $message  = 'Account name mismatch: expected "' . $data['account_name'] . '" but got "' . $resolvedName . '"';
                }
            }

            return [
                'status'   => true,
                'message'  => $message,
                'data'     => $resolvedData,
                'verified' => $verified,
            ];
        } catch (Exception $e) {
            Logger::error('Flutterwave verifyBankAccount exception: ' . $e->getMessage());

            return [
                'status'   => false,
                'message'  => 'Account verification failed: ' . $e->getMessage(),
                'data'     => null,
                'verified' => false,
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Sub-account creation
    // -------------------------------------------------------------------------

    /**
     * Create a new Flutterwave sub-account for split payments.
     *
     * Required $data keys:
     *   - account_bank   (string) Bank code (e.g., "044" for Access Bank Nigeria)
     *   - account_number (string) Merchant's bank account number or IBAN
     *   - business_name  (string) Subaccount business name
     *   - business_mobile (string) Business phone number
     *   - country        (string) Country code (e.g., "NG", "GH", "UG", "RW", "TZ", "US")
     *   - split_type     (string) "percentage" or "flat"
     *   - split_value    (float) Commission ratio/amount (e.g., 0.2 for 20%)
     *
     * Optional $data keys:
     *   - email                (string) Business email
     *   - meta                 (array)  Additional metadata (US requires: swiftCode, routingNumber)
     *   - bank_branch          (string) Required for GH, TZ, RW, UG
     *   - swift_code           (string) Required for SEPA countries
     *   - is_f4b_account       (bool)   Set true to split into another Flutterwave merchant
     *                                     (use merchant ID as account_number)
     *
     * @param  array $data
     * @return array{status: bool, message: string, subaccount_id?: string, data?: array|null}
     *
     * @throws InvalidArgumentException when required parameters are absent
     */
    public function createSubaccount(array $data): array
    {
        $requiredParameters = [
            'account_bank',
            'account_number',
            'business_name',
            'business_mobile',
            'country',
            'split_type',
            'split_value',
        ];

        $missingParameters = array_diff($requiredParameters, array_keys($data));

        if (! empty($missingParameters)) {
            throw new InvalidArgumentException(
                'Missing required parameters: ' . implode(', ', $missingParameters)
            );
        }

        // Validate split_type
        if (! in_array($data['split_type'], ['percentage', 'flat'], true)) {
            throw new InvalidArgumentException(
                'split_type must be either "percentage" or "flat"'
            );
        }

        // Build the request body
        $dataBody = [
            'account_bank'   => $data['account_bank'],
            'account_number' => $data['account_number'],
            'business_name'  => $data['business_name'],
            'business_mobile' => $data['business_mobile'],
            'country'        => $data['country'],
            'split_type'     => $data['split_type'],
            'split_value'    => (float) $data['split_value'],
        ];

        // Optional: email
        if (! empty($data['email'])) {
            $dataBody['email'] = $data['email'];
        }

        // Optional: meta (required for US accounts - swiftCode, routingNumber)
        if (! empty($data['meta']) && is_array($data['meta'])) {
            $dataBody['meta'] = $data['meta'];
        }

        // Optional: bank_branch (required for Ghana, Tanzania, Rwanda, Uganda)
        if (! empty($data['bank_branch'])) {
            $dataBody['bank_branch'] = $data['bank_branch'];
        }

        // Optional: swift_code (required for SEPA countries)
        if (! empty($data['swift_code'])) {
            $dataBody['swift_code'] = $data['swift_code'];
        }

        // Optional: is_f4b_account (set true to split into another Flutterwave merchant)
        if (! empty($data['is_f4b_account'])) {
            $dataBody['is_f4b_account'] = (bool) $data['is_f4b_account'];
        }

        try {
            $response     = Http::withHeaders($this->defaultHeaders())
                ->post('https://api.flutterwave.com/v3/subaccounts', $dataBody);
            $responseData = $response->json();

            if ($response->failed() || ($responseData['status'] ?? '') !== 'success') {
                return [
                    'status'  => false,
                    'message' => $responseData['message'] ?? 'Subaccount creation failed',
                    'data'    => $responseData['data'] ?? null,
                ];
            }

            // Extract and return the subaccount_id (formatted like RS_FB312AA6C2C84A13421F3079E714F2CB)
            $subaccountId = $responseData['data']['subaccount_id'] ?? null;

            return [
                'status'        => true,
                'message'       => 'Subaccount created successfully',
                'subaccount_id' => $subaccountId,
                'data'          => $responseData['data'] ?? null,
            ];
        } catch (Exception $e) {
            Logger::error('Flutterwave createSubaccount exception: ' . $e->getMessage());

            return [
                'status'  => false,
                'message' => 'Subaccount creation failed: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Transaction verification
    // -------------------------------------------------------------------------

    /**
     * Verify a transaction using the Flutterwave transaction ID (numeric).
     *
     * Use this after a redirect or inline callback where you have data.id.
     * For tx_ref-based verification use {@see verifyTransactionByReference}.
     *
     * @param  string|int $transactionId  Flutterwave numeric transaction ID
     * @return array{status: bool, message: string, payment_status: string|null, is_successful?: bool, data?: array}
     */
    public function verifyTransaction(string|int $transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->flutterwave_secret}",
                'Cache-Control' => 'no-cache',
            ])
            ->timeout(15)
            ->retry(3, 1000)
            ->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

            if ($response->failed()) {
                return [
                    'status'         => false,
                    'message'        => 'Transaction verification failed',
                    'error'          => $response->body(),
                    'payment_status' => null,
                ];
            }

            $responseData = $response->json();

            // BUG FIX #6: Flutterwave returns "success" (string) not true (bool)
            // for the status field. The original compared ($responseData['status'] ?? false)
            // which would never equal the string "success" truthy-check aside.
            if (($responseData['status'] ?? '') !== 'success') {
                return [
                    'status'         => false,
                    'message'        => $responseData['message'] ?? 'Transaction verification failed',
                    'payment_status' => null,
                ];
            }

            $transactionData = $responseData['data'] ?? [];

            return $this->buildVerificationResponse($transactionData);
        } catch (Exception $e) {
            return [
                'status'         => false,
                'message'        => 'Transaction verification failed: ' . $e->getMessage(),
                'payment_status' => null,
            ];
        }
    }

    /**
     * Verify a transaction using the merchant-generated tx_ref.
     *
     * Handy when you only stored the reference and not the numeric Flutterwave ID.
     *
     * @param  string $txRef  The tx_ref you generated when initialising payment
     * @return array{status: bool, message: string, payment_status: string|null, is_successful?: bool, data?: array}
     */
    public function verifyTransactionByReference(string $txRef): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->flutterwave_secret}",
                'Cache-Control' => 'no-cache',
            ])
            ->timeout(15)
            ->retry(3, 1000)
            ->get("https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref={$txRef}");

            if ($response->failed()) {
                return [
                    'status'         => false,
                    'message'        => 'Transaction verification failed',
                    'error'          => $response->body(),
                    'payment_status' => null,
                ];
            }

            $responseData    = $response->json();
            $transactionData = $responseData['data'] ?? [];

            if (($responseData['status'] ?? '') !== 'success') {
                return [
                    'status'         => false,
                    'message'        => $responseData['message'] ?? 'Transaction verification failed',
                    'payment_status' => null,
                ];
            }

            return $this->buildVerificationResponse($transactionData);
        } catch (Exception $e) {
            return [
                'status'         => false,
                'message'        => 'Transaction verification failed: ' . $e->getMessage(),
                'payment_status' => null,
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Polling
    // -------------------------------------------------------------------------

    /**
     * Poll Flutterwave for a final transaction status (successful / failed).
     *
     * ⚠️  This method uses sleep() and is therefore suitable only for CLI
     * commands and queued jobs — never call it inside a synchronous HTTP request.
     *
     * @param  string $transactionReference  The tx_ref used when creating the payment
     * @param  int    $maxAttempts           Maximum number of polling iterations
     * @param  int    $intervalSeconds       Seconds to wait between attempts
     * @return array{status: bool, message: string, payment_status: string|null, is_successful: bool, data: array|null}
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
                    'Authorization' => "Bearer {$this->flutterwave_secret}",
                    'Cache-Control' => 'no-cache',
                ])
                ->timeout(30)
                ->get("https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref={$transactionReference}");

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
                $paymentStatus = $responseData['data']['status'] ?? 'failed';

                if (in_array($paymentStatus, ['successful', 'failed'], true)) {
                    return $this->formatPollResponse($responseData);
                }

                if ($paymentStatus === 'pending') {
                    sleep($intervalSeconds);
                    continue;
                }

                // Unknown status — stop polling immediately.
                return $this->formatPollResponse($responseData);
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
    // Webhooks
    // -------------------------------------------------------------------------

    /**
     * Validate and dispatch an inbound Flutterwave webhook.
     *
     * BUG FIX #7: Flutterwave does NOT use HMAC-SHA256 for webhook authentication.
     * It sends the plain-text secret hash you configured in the dashboard inside
     * a header called `verif-hash`.  The original code read `flutterwave-webhook-signature`
     * and computed an HMAC — both were wrong.
     *
     * @param  Request $request
     * @return array{status: bool, message: string, reference?: string, amount?: int}
     */
    public static function handleFlutterwaveWebhook(Request $request): array
    {
        // BUG FIX #8: Avoid instantiating the full service (which hits the DB)
        // just to read a secret. Read the webhook hash directly from env/config
        // where it belongs.
        $webhookSecretHash = config('constants.flutterwave_webhook_secret_hash')
            ?? env('FLUTTERWAVE_WEBHOOK_SECRET_HASH', '');

        if (! self::verifyWebhookSignature(
            $request->header('verif-hash'),
            $webhookSecretHash
        )) {
            return [
                'status'  => false,
                'message' => 'Invalid webhook signature',
            ];
        }

        $data        = $request->all();
        $event       = $data['event'] ?? '';
        $paymentData = $data['data']  ?? [];

        try {
            return match ($event) {
                'charge.completed' => self::handleSuccessfulCharge($paymentData),
                'charge.failed'    => self::handleFailedCharge($paymentData),
                'charge.pending'   => self::handlePendingCharge($paymentData),
                'transfer.completed' => self::handleSuccessfulTransfer($paymentData),
                'transfer.failed'    => self::handleFailedTransfer($paymentData),
                default => [
                    'status'  => true,
                    'message' => 'Event received but not processed: ' . $event,
                ],
            };
        } catch (Exception $e) {
            return [
                'status'  => false,
                'message' => 'Webhook processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a Flutterwave webhook using the plain-text verif-hash header.
     *
     * Flutterwave simply echoes back the secret hash you configured in the
     * dashboard. There is NO cryptographic signing — just a direct string match.
     *
     * BUG FIX #7 (continued): The original performed hash_hmac('sha256', ...) which
     * is incorrect and will always fail for legitimate Flutterwave webhooks.
     *
     * @param  string|null $incomingHash  Value of the `verif-hash` header
     * @param  string      $storedHash    Your configured webhook secret hash
     */
    public static function verifyWebhookSignature(?string $incomingHash, string $storedHash): bool
    {
        if (empty($incomingHash) || empty($storedHash)) {
            return false;
        }

        // Constant-time comparison to prevent timing attacks.
        return hash_equals($storedHash, $incomingHash);
    }

    // -------------------------------------------------------------------------
    // Webhook event handlers
    // -------------------------------------------------------------------------

    /**
     * @param  array $paymentData
     * @return array{status: bool, message: string, reference: string, amount: int}
     */
    protected static function handleSuccessfulCharge(array $paymentData): array
    {
        $reference = $paymentData['tx_ref']  ?? '';
        $amount    = $paymentData['amount']  ?? 0;

        // TODO: Update your transaction record here, e.g.:
        // Transaction::where('reference', $reference)->update(['status' => 'completed']);

        return [
            'status'    => true,
            'message'   => 'Successful charge processed',
            'reference' => $reference,
            'amount'    => $amount,
        ];
    }

    /**
     * @param  array $paymentData
     * @return array{status: bool, message: string, reference: string}
     */
    protected static function handleFailedCharge(array $paymentData): array
    {
        $reference = $paymentData['tx_ref'] ?? '';

        // TODO: Update your transaction record here, e.g.:
        // Transaction::where('reference', $reference)->update(['status' => 'failed']);

        return [
            'status'    => true,
            'message'   => 'Failed charge processed',
            'reference' => $reference,
        ];
    }

    /**
     * @param  array $paymentData
     * @return array{status: bool, message: string, reference: string}
     */
    protected static function handlePendingCharge(array $paymentData): array
    {
        $reference = $paymentData['tx_ref'] ?? '';

        // TODO: Update your transaction record here, e.g.:
        // Transaction::where('reference', $reference)->update(['status' => 'pending']);

        return [
            'status'    => true,
            'message'   => 'Pending charge processed',
            'reference' => $reference,
        ];
    }

    /**
     * @param  array $transferData
     * @return array{status: bool, message: string, reference: string}
     */
    protected static function handleSuccessfulTransfer(array $transferData): array
    {
        return [
            'status'    => true,
            'message'   => 'Successful transfer processed',
            'reference' => $transferData['reference'] ?? '',
        ];
    }

    /**
     * @param  array $transferData
     * @return array{status: bool, message: string, reference: string}
     */
    protected static function handleFailedTransfer(array $transferData): array
    {
        return [
            'status'    => true,
            'message'   => 'Failed transfer processed',
            'reference' => $transferData['reference'] ?? '',
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Default HTTP headers for Flutterwave API calls.
     */
    private function defaultHeaders(): array
    {
        return [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->flutterwave_secret,
        ];
    }

    /**
     * Build a standardised verification response from raw transaction data.
     */
    private function buildVerificationResponse(array $transactionData): array
    {
        $paymentStatus = $transactionData['status'] ?? 'failed';
        $isSuccessful  = $paymentStatus === 'successful';

        $message = match ($paymentStatus) {
            'successful' => 'Transaction completed successfully',
            'failed'     => 'Transaction failed',
            'pending'    => 'Transaction is still pending',
            default      => 'Unknown transaction status: ' . $paymentStatus,
        };

        return [
            'status'         => true,
            'message'        => $message,
            'payment_status' => $paymentStatus,
            'is_successful'  => $isSuccessful,
            'data'           => [
                'reference'      => $transactionData['tx_ref']            ?? null,
                'amount'         => $transactionData['amount']            ?? 0,
                'currency'       => $transactionData['currency']          ?? 'NGN',
                'customer_email' => $transactionData['customer']['email'] ?? '',
                'channel'        => $transactionData['payment_type']      ?? '',
                'paid_at'        => $transactionData['created_at']        ?? null,
                'status'         => $paymentStatus,
            ],
        ];
    }

    /**
     * Format a raw Flutterwave poll response into the standard shape.
     */
    private function formatPollResponse(array $responseData): array
    {
        $transactionData = $responseData['data'] ?? [];
        $paymentStatus   = $transactionData['status'] ?? 'failed';

        return [
            'status'         => true,
            'message'        => match ($paymentStatus) {
                'successful' => 'Transaction completed successfully',
                'failed'     => 'Transaction failed',
                'pending'    => 'Transaction is still pending',
                default      => 'Unknown transaction status',
            },
            'payment_status' => $paymentStatus,
            'is_successful'  => $paymentStatus === 'successful',
            'data'           => [
                'reference'      => $transactionData['tx_ref']            ?? null,
                'amount'         => $transactionData['amount']            ?? 0,
                'currency'       => $transactionData['currency']          ?? 'NGN',
                'customer_email' => $transactionData['customer']['email'] ?? '',
                'channel'        => $transactionData['payment_type']      ?? '',
                'paid_at'        => $transactionData['created_at']        ?? null,
                'status'         => $paymentStatus,
            ],
        ];
    }

    /**
     * Resolve just the secret key without booting the full service.
     * Used by static methods that need a secret key.
     */
    private static function resolveSecretKey(): string
    {
        if (static::isTestEnvironment()) {
            return env('FLUTTERWAVE_TEST_SECRET_KEY', '');
        }

        $settings = Setting::where('id', 1)->first();

        return $settings?->flutterwave_secret ?? '';
    }

    /**
     * Determine whether the current APP_ENV is a non-production environment.
     */
    private static function isTestEnvironment(): bool
    {
        return in_array(env('APP_ENV'), self::TEST_ENVIRONMENTS, true);
    }
}
