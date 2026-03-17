<?php

namespace App\Services;

use App\Models\Meter;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Models\Logger;

class TokenGenerationService {
    public static function generateMeterToken($meter, $tariff_index, $unit, $need_kct = false) {
        // throw new Exception('Test Failure');
        // return ['success' => false];
        $databody = [
            'meterType' => $meter->KRN2,
            'meterNo' => $meter->meterNo,
            'sgc' => (int)$meter->NewSGC,
            'ti' => $tariff_index, //TRARRRIF INDEX
            'amount' => $unit,
        ];

        $response = Http::withOptions([
            'verify' => false,
            'timeout' => 10,
        ])->post('http://169.239.189.91:19071/tokenGen', $databody);

        $token = null;
        $status = null;

        if (! $response->successful()) {
            return [
                'success' => false,
                'data' => [],
            ];
        }

        $gdata = $response->json();
        $data = json_decode($gdata, true);
        $status = $data['code'] ?? null;
        $token = $data['tokens'][0];

        if ($status !== "SUCCESS") {
            return [
                'success' => false,
                'data' => []
            ];
        }

        if (! $need_kct) {
            return [
                'success' => true,
                'data' => [
                    'token' => $token
                ],
            ];
        }

        $kctdatabody = [
            'meterType' => $meter->KRN1,
            'tometerType' => $meter->KRN1,
            'meterNo' => $meter->meterNo,
            'sgc' => (int)$meter->OldSGC,
            'tosgc' => (int)$meter->NewSGC,
            'ti' => $tariff_index,
            'toti' => 1,
            'allow' => false,
            'allowkrn' => true,
        ];

        $kct_response = Http::withOptions([
            'verify' => false,
            'timeout' => 10,
        ])->post('http://169.239.189.91:19071/kcttokenGen', $kctdatabody);

        if (! $kct_response->successful()) {
            return [
                'success' => false,
                'data' => []
            ];
        }

        $kct = $kct_response->json();
        $kct_data = json_decode($kct, true);
        $status = $kct_data['code'] ?? null;

        if ($status !== "SUCCESS") {
            return [
                'success' => false,
                'data' => []
            ];
        }

        $kct_token = $kct_data['tokens'];
        $meter->NeedKCT = 0;
        $meter->save();

        return [
            'success' => true,
            'data' => [
                'token' => $token,
                'kct_token' => $kct_token,
            ],
        ];
    }

    /**
     * Generate a tamper token for the given meter
     *
     * @param \App\Models\Meter $meter The meter instance
     * @param int $tariff_index The tariff index
     * @return array
     */
    public static function generateTamperToken($meter, $tariff_index)
    {
        $databody = [
            'meterType' => $meter->KRN2,
            'meterNo' => $meter->meterNo,
            'sgc' => (int)$meter->NewSGC,
            'ti' => $tariff_index,
            'sbc' => 5,
            'amount' => 10,
        ];

        Logger::info('Tamper token data body', ['request body' => $databody]);

        $response = Http::withOptions([
            'verify' => false,
            'timeout' => 10,
        ])->post('http://169.239.189.91:19071/msetokenGen', $databody);

        if (!$response->successful()) {
            return [
                'success' => false,
                'data' => [],
                'error' => 'HTTP request failed',
            ];
        }

        $responseData = $response->json();
        $data = json_decode($responseData, true);
        $status = $data['code'] ?? null;

        if ($status !== 'SUCCESS') {
            return [
                'success' => false,
                'data' => [],
                'error' => $data['message'] ?? 'Token generation failed',
            ];
        }

        $token = $data['tokens'][0] ?? null;

        if (!$token) {
            return [
                'success' => false,
                'data' => [],
                'error' => 'No token returned',
            ];
        }

        return [
            'success' => true,
            'data' => [
                'token' => $token
            ],
        ];
    }

    /**
     * Generate a KCT (Key Change Token) for the given meter
     *
     * This method generates a KCT token which is used when migrating between meters.
     * After successful generation, it sets the meter NeedKCT to 0.
     *
     * @param Meter $meter The meter instance
     * @param string $meterNo The meter number
     * @param int $sgc The source SGC (Standard Generic Classification)
     * @param int $tosgc The target SGC
     * @param int $ti The source tariff index
     * @param int $toti The target tariff index
     * @return array
     */
    public static function generateKctToken(Meter $meter, string $meterNo, int $sgc, int $tosgc, int $ti, int $toti)
    {
        $kctdatabody = [
            'meterType' => $meter->KRN1,
            'tometerType' => $meter->KRN2,
            'meterNo' => $meterNo,
            'sgc' => $sgc,
            'tosgc' => $tosgc,
            'ti' => $ti,
            'toti' => $toti,
            'allow' => false,
            'allowkrn' => true,
        ];

        Logger::info('KCT Token data body', ['request body' => $kctdatabody]);

        $kct_response = Http::withOptions([
            'verify' => false,
            'timeout' => 10,
        ])->post('http://169.239.189.91:19071/kcttokenGen', $kctdatabody);

        if (!$kct_response->successful()) {
            return [
                'success' => false,
                'data' => [],
                'error' => 'HTTP request failed',
            ];
        }

        $kct = $kct_response->json();
        $kct_data = json_decode($kct, true);
        $status_code = $kct_data['code'] ?? null;

        if ($status_code !== 'SUCCESS') {
            return [
                'success' => false,
                'data' => [],
                'error' => $kct_data['message'] ?? 'KCT token generation failed',
            ];
        }

        // Set meter NeedKCT to 0 and save
        $meter->NeedKCT = 0;
        $meter->save();

        return [
            'success' => true,
            'data' => [
                'kct_token1' => $kct_data['tokens'][0],
                'kct_token2' => $kct_data['tokens'][1],
            ],
        ];
    }

    /**
     * Generate a clear credit token for the given meter
     *
     * This method generates a clear credit token to clear existing credit on a meter.
     * Note: KCT generation is handled separately in the Meter model using generateKctToken when needed.
     *
     * @param Meter $meter The meter instance
     * @param int $tariff_index The tariff index
     * @return array
     */
    public static function generateClearCreditToken(Meter $meter, int $tariff_index)
    {
        $databody = [
            'meterType' => $meter->KRN2,
            'meterNo' => $meter->meterNo,
            'sgc' => (int)$meter->NewSGC,
            'ti' => $tariff_index,
            'sbc' => 1,
            'amount' => 10,
        ];

        Logger::info('Clear Credit Token data body', ['request body' => $databody]);

        $response = Http::withOptions([
            'verify' => false,
            'timeout' => 10,
        ])->post('http://169.239.189.91:19071/msetokenGen', $databody);

        if (!$response->successful()) {
            return [
                'success' => false,
                'data' => [],
                'error' => 'HTTP request failed',
            ];
        }

        $responseData = $response->json();
        $data = json_decode($responseData, true);
        $status = $data['code'] ?? null;

        if ($status !== 'SUCCESS') {
            return [
                'success' => false,
                'data' => [],
                'error' => $data['message'] ?? 'Clear credit token generation failed',
            ];
        }

        $token = $data['tokens'][0] ?? null;

        if (!$token) {
            return [
                'success' => false,
                'data' => [],
                'error' => 'No token returned',
            ];
        }

        return [
            'success' => true,
            'data' => [
                'token' => $token
            ],
        ];
    }
}
