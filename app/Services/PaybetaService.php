<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class PaybetaService
{
    protected $apiKey;
    protected $baseUrl;
    protected $cable_services;
    protected $networkMap;

    public function __construct()
    {
        $this->apiKey = config('services.paybeta.key');
        $this->baseUrl = config('services.paybeta.base_url');
        $this->cable_services = ['dstv', 'gotv', 'startimes'];
    }

    /**
     * Internal helper for API requests
     */
    protected function makeRequest($method, $endpoint, $data = [])
    {
        try {
            $response = Http::withHeaders([
                'P-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->$method("{$this->baseUrl}/{$endpoint}", $data);

            if ($response->failed()) {
                Log::error("Paybeta API Error: " . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Paybeta Connection Error: " . $e->getMessage());
            return ['status' => 'failed', 'message' => 'Could not connect to provider'];
        }
    }

    // --- AIRTIME (MTN, Glo, Airtel, 9mobile) ---
    public function purchaseAirtime($network, $phone, $amount, $reference)
    {
        // Service slugs: mtn_vtu, glo_vtu, airtel_vtu, 9mobile_vtu
        return $this->makeRequest('post', 'airtime/purchase', [
            'service' => "{$network}_vtu",
            'phoneNumber' => $phone,
            'amount' => $amount,
            'reference' => $reference
        ]);
    }

    // --- DATA ---
    public function purchaseData($network, $phone, $dataCode, $amount, $reference)
    {
        // Service slugs: mtn_data, glo_data, etc.
        return $this->makeRequest('post', 'data-bundle/purchase', [
            'service' => "{$network}_data",
            'phoneNumber' => $phone,
            'amount' => $amount,
            'code' => $dataCode, // The plan code from their 'Get Data Bundles' endpoint
            'reference' => $reference
        ]);
    }

    // --- CABLE TV (DStv, GOtv, StarTimes, Showmax) ---
    public function validateCableAccount($service, $smartCardNumber)
    {
        // Slugs: dstv, gotv, startimes
        return $this->makeRequest('post', 'cable/validate', [
            'service' => $service,
            'smartCardNumber' => $smartCardNumber
        ]);
    }

    public function purchaseCable($service, $smartCardNumber, $bouquetCode, $amount, $reference)
    {
        return $this->makeRequest('post', 'cable/purchase', [
            'service' => $service,
            'smartCardNumber' => $smartCardNumber,
            'bouquetCode' => $bouquetCode,
            'amount' => $amount,
            'reference' => $reference
        ]);
    }

    public function purchaseShowmax($phone, $bouquetCode, $amount, $reference)
    {
        return $this->makeRequest('post', 'showmax/purchase', [
            'phoneNumber' => $phone,
            'bouquetCode' => $bouquetCode,
            'amount' => $amount,
            'reference' => $reference
        ]);
    }

    public function getWalletBalance()
    {
        return $this->makeRequest('get', 'wallet/balance');
    }

    // --- DISCOVERY METHODS ---

    /**
     * Fetch all available service providers (MTN, Airtel, DStv, etc.)
     * Useful for building dynamic category lists.
     */
    public function getProviders($category)
    {
        // Categories: airtime, data, cable, electricity
        return $this->makeRequest('get', "{$category}/providers");
    }

    /**
     * Fetch available data plans for a specific network.
     * Use this to populate your "Select Plan" dropdown.
     */
    public function getDataBundles($network, $ttl = 12600)
    {
        // $network: mtn_data, glo_data, airtel_data, 9mobile_data

        $networkMap = [
            'mtn' => 'mtn_data',
            'glo' => 'glo_data',
            'airtel' => 'airtel_data',
            '9mobile' => '9mobile_data',
            'etisalat' => '9mobile_data',
        ];

        if (!in_array($network, array_keys($networkMap))) {
            throw new InvalidArgumentException("Invalid network type");
        }

        $cache_key = 'paybeta_data_bundles-' . $networkMap[$network];

        return Cache::remember($cache_key, $ttl, function () use ($network, $networkMap) {

            $response =  $this->makeRequest('post', 'data-bundle/list', [
                'service' => $networkMap[$network]
            ]);

            if (!$response || isset($response['error']) || $response['status'] == 'failed') {
                throw new Exception('Failed to fetch data bundles');
            }

            return $response;
        });
    }

    /**
     * Fetch available data bundles for all networks.
     *
     * @return array  Array containing data bundles for all networks
     */
    public function getAllDataBundles(): array
    {
        $networks = ['mtn_data', 'glo_data', 'airtel_data', '9mobile_data'];
        $results = [];

        foreach ($networks as $network) {
            try {
                $key = str_replace('_data', '', $network);
                $results[$key] = $this->getDataBundles($network);
            } catch (Exception $e) {
                // Log error but continue with other networks
                Log::warning("Failed to fetch data bundles for {$network}", [
                    'error' => $e->getMessage()
                ]);
                $results[$key] = null;
            }
        }

        return $results;
    }

    /**
     * Fetch available bouquets for Cable TV (DStv, GOtv, etc.)
     */
    public function getCableBouquets($service, $ttl = 12600)
    {
        // $service: dstv, gotv, startimes
        $services = $this->cable_services;

        $cache_key = 'paybeta_service_bouquets-' . $service;

        return Cache::remember($cache_key, $ttl, function () use ($service) {
            $response =  $this->makeRequest('post', 'cable/bouquet', [
                'service' => $service
            ]);

            if (!$response || isset($response['error'])) {
                throw new Exception('Failed to fetch service bouquets');
            }

            return $response;
        });
    }

    /**
     * Fetch all cable TV bouquets for all providers.
     *
     * @return array  Array containing bouquets for all cable providers
     */
    public function getAllCableBouquets(): array
    {
        $services = $this->cable_services;
        $results = [];

        foreach ($services as $service) {
            try {
                $results[$service] = $this->getCableBouquets($service);
            } catch (Exception $e) {
                Log::warning("Failed to fetch cable bouquets for {$service}", [
                    'error' => $e->getMessage()
                ]);
                $results[$service] = null;
            }
        }

        return $results;
    }

    // --- VALIDATION METHODS ---

    /**
     * Validate Cable TV IUC/SmartCard Number.
     * Returns the customer's name to show on your frontend.
     */
    public function validateCable($service, $smartCardNumber)
    {
        return $this->makeRequest('post', 'cable/validate', [
            'service' => $service,
            'smartCardNumber' => $smartCardNumber
        ]);
    }

    /**
     * Validate Electricity Meter Number.
     */
    public function validateMeter($service, $meterNumber, $type = 'prepaid')
    {
        return $this->makeRequest('post', 'electricity/validate', [
            'service' => $service,
            'meterNumber' => $meterNumber,
            'type' => $type // prepaid or postpaid
        ]);
    }

    public function getDataPackage($network, $variation_code) {
        $allowed_networks = [
            'mtn',
            'glo',
            'airtel',
            '9mobile',
            'etisalat',
        ];

        if (!in_array($network, $allowed_networks)) {
            throw new InvalidArgumentException('Invalid network passed');
        }

        $packages = $this->getDataBundles($network)['data']['packages'] ?? [];

        // dd($packages, $this->popDataBundleCache($network));

        foreach ($packages as $package) {
            if (isset($package['code']) && $package['code'] == $variation_code) {
                $package['search_success'] = true;
                return $package;
            }
        }

        return [
            'search_success' => false
        ];
    }

    public function getCablePackage($service, $variation_code) {

        $services = $this->cable_services;
        if (!in_array($service, $services)) {
            throw new InvalidArgumentException('Invalid Cable Service Passed');
        }

        $packages = $this->getCableBouquets($service)['data']['packages'] ?? [];

        // dd($packages, $this->popDataBundleCache($network));

        foreach ($packages as $package) {
            if (isset($package['code']) && $package['code'] == $variation_code) {
                $package['search_success'] = true;
                return $package;
            }
        }

        return [
            'search_success' => false
        ];
    }

    public function popDataBundleCache($network) {
        $cache_key = 'paybeta_data_bundles-' . $network;

        return Cache::forget($cache_key);
    }

    public function popCableBouquetCache($service) {
        $cache_key = 'paybeta_service_bouquets-' . $service;

        return Cache::forget($cache_key);
    }
}
