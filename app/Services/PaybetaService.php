<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaybetaService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.paybeta.key');
        $this->baseUrl = config('services.paybeta.base_url');
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
    public function getDataBundles($network)
    {
        // $network: mtn_data, glo_data, airtel_data, 9mobile_data
        return $this->makeRequest('post', 'data-bundle/list', [
            'service' => $network
        ]);
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
            } catch (\Exception $e) {
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
    public function getCableBouquets($service)
    {
        // $service: dstv, gotv, startimes
        return $this->makeRequest('post', 'cable/bouquets', [
            'service' => $service
        ]);
    }

    /**
     * Fetch all cable TV bouquets for all providers.
     *
     * @return array  Array containing bouquets for all cable providers
     */
    public function getAllCableBouquets(): array
    {
        $services = ['dstv', 'gotv', 'startimes'];
        $results = [];

        foreach ($services as $service) {
            try {
                $results[$service] = $this->getCableBouquets($service);
            } catch (\Exception $e) {
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
}
