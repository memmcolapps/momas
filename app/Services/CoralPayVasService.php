<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;
use Exception;

class CoralPayVasService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected string $terminalId;
    protected string $channel;

    // Token is cached to avoid re-authenticating on every request
    protected string $cacheTokenKey = 'coralpay_vas_token';
    protected int $tokenTtlMinutes = 55; // Slightly less than typical 60-min JWT expiry

    public function __construct()
    {
        $this->baseUrl    = rtrim(config('coralpay.vas_base_url'), '/');
        $this->username   = config('coralpay.vas_username');
        $this->password   = config('coralpay.vas_password');
        $this->terminalId = config('coralpay.vas_terminal_id');
        $this->channel    = config('coralpay.vas_channel', 'WEB');
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  AUTHENTICATION
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Authenticate with CoralPay VAS API and return a Bearer token.
     * Token is cached so it is re-used across requests within its TTL.
     */
    public function authenticate(): string
    {
        if (Cache::has($this->cacheTokenKey)) {
            return Cache::get($this->cacheTokenKey);
        }

        $response = Http::withoutVerifying() // Remove in production; add proper cert
            ->timeout(30)
            ->post("{$this->baseUrl}/authenticate", [
                'username' => $this->username,
                'password' => $this->password,
            ]);

        $this->assertSuccess($response, 'Authentication');

        $token = $response->json('token')
            ?? $response->json('responseBody.token')
            ?? throw new Exception('CoralPay VAS: token missing in auth response.');

        Cache::put($this->cacheTokenKey, $token, now()->addMinutes($this->tokenTtlMinutes));

        return $token;
    }

    /**
     * Force token refresh (call this if a request returns 401).
     */
    public function refreshToken(): string
    {
        Cache::forget($this->cacheTokenKey);
        return $this->authenticate();
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  AIRTIME TOP-UP
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Purchase airtime for a phone number.
     *
     * @param  string $phone        Beneficiary phone (e.g. 08012345678)
     * @param  int    $amount       Amount in Naira (min ₦50)
     * @param  string $network      MTN | Airtel | Glo | 9mobile
     * @param  string $requestId    Your unique transaction reference
     * @return array                Parsed API response
     */
    public function buyAirtime(string $phone, int $amount, string $network, string $requestId): array
    {
        $payload = [
            'requestId'               => $requestId,
            'uniqueCode'              => $this->resolveNetworkCode($network),
            'beneficiaryPhoneNumber'  => $this->formatPhone($phone),
            'amount'                  => $amount,
            'channel'                 => $this->channel,
            'terminalId'              => $this->terminalId,
        ];

        return $this->post('/topup', $payload);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  DATA BUNDLE
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Fetch available data bundles for a given network.
     *
     * @param  string $network  MTN | Airtel | Glo | 9mobile
     * @return array
     */
    public function getDataBundles(string $network): array
    {
        $code = $this->resolveNetworkCode($network);
        return $this->get("/getdatabundle/{$code}");
    }

    /**
     * Fetch available data bundles for all networks.
     *
     * @return array  Array containing data bundles for all networks
     */
    public function getAllDataBundles(): array
    {
        $networks = ['MTN', 'GLO', 'AIRTEL', '9MOBILE', 'SMILE', 'SPECTRANET'];
        $results = [];

        foreach ($networks as $network) {
            try {
                $results[strtolower($network)] = $this->getDataBundles($network);
            } catch (\Exception $e) {
                // Log error but continue with other networks
                Log::warning("Failed to fetch data bundles for {$network}", [
                    'error' => $e->getMessage()
                ]);
                $results[strtolower($network)] = null;
            }
        }

        return $results;
    }

    /**
     * Purchase a data bundle for a phone number.
     *
     * @param  string $phone      Beneficiary phone
     * @param  string $bundleCode Bundle code returned by getDataBundles()
     * @param  string $network    MTN | Airtel | Glo | 9mobile
     * @param  string $requestId  Your unique transaction reference
     * @return array
     */
    public function buyData(string $phone, string $bundleCode, string $network, string $requestId): array
    {
        $payload = [
            'requestId'              => $requestId,
            'uniqueCode'             => $this->resolveNetworkCode($network),
            'beneficiaryPhoneNumber' => $this->formatPhone($phone),
            'bundleCode'             => $bundleCode,
            'channel'                => $this->channel,
            'terminalId'             => $this->terminalId,
        ];

        return $this->post('/databundle', $payload);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  WALLET BALANCE
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Check wallet / account balance.
     *
     * @return array  e.g. ['balance' => 50000.00, 'currency' => 'NGN']
     */
    public function checkBalance(): array
    {
        return $this->get('/balance');
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  TRANSACTION QUERY
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Query the status of a previous transaction by your request ID.
     *
     * @param  string $requestId  The requestId you sent during purchase
     * @return array
     */
    public function queryTransaction(string $requestId): array
    {
        return $this->get("/transactionquery/{$requestId}");
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  INTERNAL HTTP HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    protected function post(string $endpoint, array $payload): array
    {
        return $this->request('POST', $endpoint, $payload);
    }

    protected function get(string $endpoint): array
    {
        return $this->request('GET', $endpoint);
    }

    protected function request(string $method, string $endpoint, array $payload = []): array
    {
        $token = $this->authenticate();

        $http = Http::withoutVerifying()
            ->timeout(30)
            ->withToken($token)
            ->acceptJson()
            ->contentType('application/json');

        $url = $this->baseUrl . $endpoint;

        try {
            $response = match (strtoupper($method)) {
                'POST' => $http->post($url, $payload),
                'GET'  => $http->get($url),
                default => throw new Exception("Unsupported HTTP method: {$method}"),
            };

            // Handle 401: try once with a fresh token
            if ($response->status() === 401) {
                $token    = $this->refreshToken();
                $response = match (strtoupper($method)) {
                    'POST' => Http::withoutVerifying()->withToken($token)->post($url, $payload),
                    'GET'  => Http::withoutVerifying()->withToken($token)->get($url),
                };
            }

            $this->assertSuccess($response, $endpoint);

            return $response->json();

        } catch (RequestException $e) {
            Log::error('CoralPay VAS HTTP error', [
                'endpoint' => $endpoint,
                'status'   => $e->response?->status(),
                'body'     => $e->response?->body(),
            ]);
            throw new Exception("CoralPay VAS request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function assertSuccess($response, string $context): void
    {
        if ($response->failed()) {
            $status = $response->status();
            $body   = $response->body();
            Log::error("CoralPay VAS [{$context}] failed", ['status' => $status, 'body' => $body]);
            throw new Exception("CoralPay VAS [{$context}] HTTP {$status}: {$body}");
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  UTILITIES
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Resolve human-friendly network name to CoralPay unique code.
     */
    protected function resolveNetworkCode(string $network): string
    {
        return match (strtolower($network)) {
            'mtn'     => 'MTN',
            'airtel'  => 'AIRTEL',
            'glo'     => 'GLO',
            '9mobile', 'etisalat' => '9MOBILE',
            default   => strtoupper($network), // pass through if already a code
        };
    }

    /**
     * Normalize phone to 11-digit local format (08XXXXXXXXX).
     * Also accepts +234XXXXXXXXX or 234XXXXXXXXX.
     */
    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone); // strip non-digits

        if (str_starts_with($phone, '234') && strlen($phone) === 13) {
            return '0' . substr($phone, 3);
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 11) {
            return $phone;
        }

        throw new Exception("Invalid phone number format: {$phone}");
    }

    /**
     * Generate a unique request ID for transactions.
     * Format: CP + timestamp (ms) + random 6-char hex
     */
    public static function generateRequestId(): string
    {
        return 'CP' . now()->valueOf() . strtoupper(bin2hex(random_bytes(3)));
    }
}
