<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
}
