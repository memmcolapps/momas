<?php

namespace App\Services;

use App\DTO\CableBouquetDTO;
use App\DTO\DataPlanDTO;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class VTUService
{
    protected $apiKey;
    protected $baseUrl;
    protected $cable_services;
    protected $networkMap;

    public function __construct()
    {
        $this->apiKey = config('services.vtu.key');
        $this->baseUrl = rtrim(config('services.vtu.base_url', 'https://api.vtu.ng/v1'), '/');

        $this->cable_services = ['dstv', 'gotv', 'startimes'];

        // VTU.ng currently identifies the former 9mobile network as t2mobile.
        $this->networkMap = [
            'mtn' => 'mtn',
            'glo' => 'glo',
            'airtel' => 'airtel',
            '9mobile' => 't2mobile',
            'etisalat' => 't2mobile',
            't2mobile' => 't2mobile',
        ];
    }

    /**
     * Internal helper for API requests.
     *
     * For GET requests, $data is sent as query-string parameters.
     * For write requests, $idempotencyKey is sent using VTU.ng's
     * required Idempotency-Key header.
     */
    protected function makeRequest($method, $endpoint, $data = [], $idempotencyKey = null)
    {
        try {
            $request = Http::withToken($this->apiKey)
                ->acceptJson()
                ->asJson();

            if ($idempotencyKey !== null) {
                $request = $request->withHeaders([
                    'Idempotency-Key' => (string) $idempotencyKey,
                ]);
            }

            $url = "{$this->baseUrl}/" . ltrim($endpoint, '/');

            $response = strtolower($method) === 'get'
                ? $request->get($url, $data)
                : $request->{strtolower($method)}($url, $data);

            if ($response->failed()) {
                Log::error('VTU.ng API Error', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                ]);
            }

            return $response->json() ?? [
                'status' => 'failed',
                'message' => 'The provider returned an invalid response.',
            ];
        } catch (\Throwable $e) {
            Log::error('VTU.ng Connection Error', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
            ]);

            return [
                'status' => 'failed',
                'message' => 'Could not connect to provider',
            ];
        }
    }

    /**
     * VTU.ng requires an Idempotency-Key for every bill purchase.
     * The existing $reference argument is reused as that key so callers
     * can retry/reconcile with the same value. A UUID v4 is recommended.
     */
    protected function purchaseRequest($endpoint, $data, $reference)
    {
        if (!is_string($reference) || trim($reference) === '') {
            throw new InvalidArgumentException('A non-empty transaction reference is required.');
        }

        return $this->makeRequest('post', $endpoint, $data, $reference);
    }

    // --- AIRTIME (MTN, Glo, Airtel, T2mobile) ---

    public function purchaseAirtime($network, $phone, $amount, $reference)
    {
        $network = $this->resolveNetwork($network);

        return $this->purchaseRequest('bills/airtime', [
            'network' => $network,
            'phone' => $phone,
            'amount' => $amount,
        ], $reference);
    }

    // --- DATA ---

    public function purchaseData($network, $phone, $dataCode, $amount, $reference)
    {
        $network = $this->resolveNetwork($network);

        // VTU.ng prices data using the variation code; amount is deliberately
        // not sent because the provider takes the price from its live catalogue.
        return $this->purchaseRequest('bills/data', [
            'network' => $network,
            'phone' => $phone,
            'variation_code' => $dataCode,
        ], $reference);
    }

    // --- CABLE TV (DStv, GOtv, StarTimes) ---

    public function validateCableAccount($service, $smartCardNumber)
    {
        return $this->validateCable($service, $smartCardNumber);
    }

    public function purchaseCable($service, $smartCardNumber, $bouquetCode, $amount, $reference)
    {
        $data = [
            'provider' => $service,
            'smartcard_number' => $smartCardNumber,
            'subscription_type' => 'change',
        ];

        if ($bouquetCode !== null && $bouquetCode !== '') {
            $data['variation_code'] = $bouquetCode;
        }

        // VTU.ng can derive the price from variation_code. Keep the legacy
        // amount argument in the method signature for interface compatibility.
        if ($amount !== null && $amount > 0) {
            $data['amount'] = $amount;
        }

        return $this->purchaseRequest('bills/cable-tv', $data, $reference);
    }

    public function purchaseShowmax($phone, $bouquetCode, $amount, $reference)
    {
        // Showmax is not listed as a supported provider/product in the current
        // VTU.ng Partner API documentation. Do not send this to an invented endpoint.
        Log::warning('Showmax purchase is not supported by the current VTU.ng Partner API.');

        return [
            'status' => 'failed',
            'message' => 'Showmax is not supported by the current VTU.ng Partner API.',
        ];
    }

    public function getWalletBalance()
    {
        // The current public integration guide documents wallet:read access,
        // but does not specify the wallet-balance route. Configure the verified
        // route from your VTU.ng API Reference rather than guessing it here.
        $endpoint = config('services.vtu.wallet_balance_endpoint');

        if (!$endpoint) {
            return [
                'status' => 'failed',
                'message' => 'Set services.vtu.wallet_balance_endpoint to the wallet endpoint shown in your VTU.ng API Reference.',
            ];
        }

        return $this->makeRequest('get', $endpoint);
    }

    // --- DISCOVERY METHODS ---

    /**
     * Fetch providers from the unified VTU.ng bill catalogue.
     * Supported categories include airtime, data, electricity, cable_tv,
     * epin, bet_funding and education.
     */
    public function getProviders($category)
    {
        return $this->makeRequest('get', 'bills/catalog', [
            'type' => $category,
        ]);
    }

    /**
     * Fetch available data plans for a network.
     */
    public function getDataBundles($network, $ttl = 12600)
    {
        $network = $this->resolveNetwork($network);
        $cacheKey = 'vtu_data_bundles-' . $network;

        return Cache::remember($cacheKey, $ttl, function () use ($network) {
            $response = $this->makeRequest('get', 'bills/data-plans', [
                'network' => $network,
            ]);

            if (!$this->isSuccessfulResponse($response)) {
                throw new Exception('Failed to fetch data bundles: ' . ($response['message'] ?? 'Unknown provider error'));
            }

            return $this->transformDataBundles($response);
        });
    }

    /**
     * Fetch available data bundles for all networks.
     *
     * @return array
     */
    public function getAllDataBundles(): array
    {
        $networks = ['mtn', 'glo', 'airtel', 't2mobile'];
        $results = [];

        foreach ($networks as $network) {
            try {
                $results[$network] = $this->getDataBundles($network);
            } catch (Exception $e) {
                Log::warning("Failed to fetch VTU.ng data bundles for {$network}", [
                    'error' => $e->getMessage(),
                ]);

                $results[$network] = null;
            }
        }

        return $results;
    }

    /**
     * Fetch available bouquets for Cable TV.
     */
    public function getCableBouquets($service, $ttl = 12600)
    {
        if (!in_array($service, $this->cable_services, true)) {
            throw new InvalidArgumentException('Invalid Cable Service Passed');
        }

        $cacheKey = 'vtu_service_bouquets-' . $service;

        return Cache::remember($cacheKey, $ttl, function () use ($service) {
            $response = $this->makeRequest('get', 'bills/cable-packages', [
                'provider' => $service,
            ]);

            if (!$this->isSuccessfulResponse($response)) {
                throw new Exception('Failed to fetch service bouquets: ' . ($response['message'] ?? 'Unknown provider error'));
            }

            return $this->transformCableBouquets($response);
        });
    }

    /**
     * Fetch all cable TV bouquets for all providers.
     *
     * @return array
     */
    public function getAllCableBouquets(): array
    {
        $results = [];

        foreach ($this->cable_services as $service) {
            try {
                $results[$service] = $this->getCableBouquets($service);
            } catch (Exception $e) {
                Log::warning("Failed to fetch VTU.ng cable bouquets for {$service}", [
                    'error' => $e->getMessage(),
                ]);

                $results[$service] = null;
            }
        }

        return $results;
    }

    // --- VALIDATION METHODS ---

    /**
     * Validate Cable TV IUC/SmartCard Number.
     */
    public function validateCable($service, $smartCardNumber)
    {
        if (!in_array($service, $this->cable_services, true)) {
            throw new InvalidArgumentException('Invalid Cable Service Passed');
        }

        return $this->makeRequest('post', 'bills/cable-tv/verify-smartcard', [
            'provider' => $service,
            'smartcard_number' => $smartCardNumber,
        ]);
    }

    /**
     * Validate Electricity Meter Number.
     */
    public function validateMeter($service, $meterNumber, $type = 'prepaid')
    {
        return $this->makeRequest('post', 'bills/electricity/verify-meter', [
            'disco_slug' => $service,
            'meter_number' => $meterNumber,
            'meter_type' => $type,
        ]);
    }

    public function getDataPackage($network, $variation_code)
    {
        $allowedNetworks = ['mtn', 'glo', 'airtel', '9mobile', 'etisalat', 't2mobile'];

        if (!in_array($network, $allowedNetworks, true)) {
            throw new InvalidArgumentException('Invalid network passed');
        }

        $packages = $this->getDataBundles($network)['data']['packages'] ?? [];

        foreach ($packages as $package) {
            if (($package['code'] ?? null) == $variation_code) {
                $package['search_success'] = true;
                return $package;
            }
        }

        return ['search_success' => false];
    }

    public function getCablePackage($service, $variation_code)
    {
        if (!in_array($service, $this->cable_services, true)) {
            throw new InvalidArgumentException('Invalid Cable Service Passed');
        }

        $packages = $this->getCableBouquets($service)['data']['packages'] ?? [];

        foreach ($packages as $package) {
            if (($package['code'] ?? null) == $variation_code) {
                $package['search_success'] = true;
                return $package;
            }
        }

        return ['search_success' => false];
    }

    public function popDataBundleCache($network)
    {
        $network = $this->resolveNetwork($network);

        return Cache::forget('vtu_data_bundles-' . $network);
    }

    public function popCableBouquetCache($service)
    {
        return Cache::forget('vtu_service_bouquets-' . $service);
    }

    /**
     * Resolve legacy network aliases to the current VTU.ng provider slug.
     */
    protected function resolveNetwork($network)
    {
        if (!isset($this->networkMap[$network])) {
            throw new InvalidArgumentException('Invalid network type');
        }

        return $this->networkMap[$network];
    }

    /**
     * Treat structured API errors and failed connection responses as failures.
     */
    protected function isSuccessfulResponse($response)
    {
        return is_array($response)
            && !isset($response['error'])
            && ($response['status'] ?? null) !== 'failed'
            && ($response['code'] ?? null) !== 'error';
    }

    /**
     * Transform VTU.ng data plans into the Paybeta-compatible response shape.
     */
    protected function transformDataBundles(array $response): array
    {
        $packages = $response['data']['packages']
            ?? $response['data']['plans']
            ?? $response['packages']
            ?? $response['plans']
            ?? (isset($response['data']) && is_array($response['data']) && array_is_list($response['data']) ? $response['data'] : []);

        $transformed = array_map(
            fn ($package) => DataPlanDTO::fromVtuPackage($package)->toArray(),
            is_array($packages) ? $packages : []
        );

        return ['data' => $transformed];
    }

    /**
     * Transform VTU.ng cable bouquets into the Paybeta-compatible response shape.
     */
    protected function transformCableBouquets(array $response): array
    {
        $packages = $response['data']['packages']
            ?? $response['data']['plans']
            ?? $response['packages']
            ?? $response['plans']
            ?? (isset($response['data']) && is_array($response['data']) && array_is_list($response['data']) ? $response['data'] : []);

        $transformed = array_map(
            fn ($package) => CableBouquetDTO::fromVtuPackage($package)->toArray(),
            is_array($packages) ? $packages : []
        );

        return ['data' => ['packages' => $transformed]];
    }
}
