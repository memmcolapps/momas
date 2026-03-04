<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenGenerationService {
    public static function generateMeterToken($meter, $tariff_index, $unit, $need_kct = false) {
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

        Log::info('Tamper token data body', ['request body' => $databody]);

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
}
