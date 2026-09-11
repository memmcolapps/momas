<?php

namespace App\Services;

use App\Models\Bank;

class BankService
{
    /**
     * Find a bank by its code, or create it if it doesn't exist.
     * The code field matches Paystack bank codes (e.g. "044" for Access Bank).
     *
     * @param  string  $code  The bank code
     * @param  array  $extraData  Optional extra data (name, slug, paystack_code, gateway, etc.)
     */
    public function findOrCreateFromCode(string $code, array $extraData = []): ?Bank
    {
        if (empty($code)) {
            return null;
        }

        $bank = Bank::where('code', $code)->first();

        if ($bank) {
            return $bank;
        }

        $bankData = array_merge([
            'code' => $code,
            'bankName' => $extraData['name'] ?? $code,
            'name' => $extraData['name'] ?? null,
            'slug' => $extraData['slug'] ?? null,
            'paystack_code' => $extraData['paystack_code'] ?? $code,
            'gateway' => $extraData['gateway'] ?? null,
            'supports_transfer' => $extraData['supports_transfer'] ?? true,
            'active' => $extraData['active'] ?? true,
            'country' => $extraData['country'] ?? 'Nigeria',
            'currency' => $extraData['currency'] ?? 'NGN',
            'type' => $extraData['type'] ?? 'nuban',
        ], $extraData);

        return Bank::create($bankData);
    }
}
