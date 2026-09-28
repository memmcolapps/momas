<?php

namespace App\Services;

use App\Models\CreditToken;
use App\Models\Logger;
use App\Models\Meter;
use App\Models\MeterToken;
use App\Models\Tariff;
use App\Models\User;
use App\Models\Utility;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class EmergencyTokenService
{
    public const DEBT_NAME = 'Emergency Token Debt';

    /**
     * Generate an emergency token for a customer and place an equal amount of
     * debt on the customer.
     *
     * The generation is blocked when the customer already has any unpaid
     * debt-type utility owed. On success, a critical audit log is written. On
     * any failure, the error is logged with the reason so the outcome is always
     * recorded.
     *
     * @param \App\Models\Meter $meter The meter (and owner) to tokenize
     * @param int $tariff_id The ID of the tariff to use
     * @param int $amount The amount in Naira to convert to units and place as debt
     * @return array The generated token details
     * @throws \Exception When the meter has no owner, the tariff is missing,
     *                    the customer has unpaid debt, the amount is too small,
     *                    or token generation fails
     */
    public static function generate(Meter $meter, int $tariff_id, int $amount): array
    {
        $debtCheck = null;

        try {
            $debtCheck = (new UtilityManagementService())
                ->calculateUserOwedUtility($meter->user_id, $meter->estate_id);

            $maxAmount = (int) (app(ConfigManagementService::class)
                ->getConfig('momas-max-emergency-token') ?? config('constants.momas_max_emergency_token'));

            if ($amount > $maxAmount) {
                throw new Exception('Amount cannot exceed NGN ' . number_format($maxAmount, 2));
            }

            $result = DB::transaction(function () use ($meter, $tariff_id, $amount) {
                $owner = User::find($meter->user_id);

                if (! $owner) {
                    throw new Exception('Meter is not attached to any customer');
                }

                $tariff = Tariff::find($tariff_id);

                if (! $tariff) {
                    throw new Exception('Tariff not found');
                }

                $owed = (new UtilityManagementService())
                    ->calculateUserOwedUtility($meter->user_id, $meter->estate_id)['total_owed'];

                if ($owed > 0) {
                    throw new Exception(
                        'Customer has outstanding debt of NGN ' . number_format($owed, 2) .
                        '. Emergency token cannot be generated.'
                    );
                }

                $calculated = $meter->calculateTokenValuesByAmount($tariff_id, $amount);

                $unit = $calculated['unit'];
                $vat = $calculated['vat'];
                $vatAmount = $calculated['vatAmount'];
                $vendingAmount = $calculated['vendingAmount'];

                $token_gen = TokenGenerationService::generateMeterToken(
                    $meter,
                    $tariff->tariff_index,
                    $unit,
                    $meter->NeedKCT
                );

                if (! $token_gen['success']) {
                    throw new Exception('Emergency token generation failed, please try again');
                }

                $token = $token_gen['data']['token'];
                $kct_tokens = $token_gen['data']['kct_token'] ?? null;
                $kct_string = $kct_tokens ? implode(',', $kct_tokens) : null;

                $emergency_ref = 'emg_ref' . Str::upper(Str::random(7));

                CreditToken::create([
                    'trx_id' => $emergency_ref,
                    'user_id' => $meter->user_id,
                    'meterNo' => $meter->meterNo,
                    'amount' => $vendingAmount,
                    'amount_charged' => $amount,
                    'customer_email' => $owner->email,
                    'unitkwh' => $unit,
                    'vat' => $vat,
                    'estate_id' => $meter->estate_id,
                    'estate_name' => $owner->estate_name,
                    'token' => $token,
                    'status' => 2,
                    'vatAmount' => $vatAmount,
                    'tariff_amount' => $calculated['tariffAmount'],
                    'tariff_id' => $tariff_id,
                    'kct_tokens' => $kct_string,
                ]);

                $meterToken = new MeterToken();
                $meterToken->user_id = $meter->user_id;
                $meterToken->trx_id = $emergency_ref;
                $meterToken->meterNo = $meter->meterNo;
                $meterToken->token = $token;
                $meterToken->amount = $amount;
                $meterToken->unit = $unit;
                $meterToken->vat = $vat;
                $meterToken->kct_tokens = $kct_string;
                $meterToken->estate_id = $meter->estate_id;
                $meterToken->status = 2;
                $meterToken->save();

                Utility::create([
                    'user_id' => $meter->user_id,
                    'estate_id' => $meter->estate_id,
                    'type' => 'debt',
                    'title' => self::DEBT_NAME,
                    'amount' => $amount,
                    'start_date' => now()->toDateString(),
                    'mode_of_payment' => 'full_payment',
                    'activated' => true,
                    'operator_id' => auth()->id() ?? 1,
                ]);

                return [
                    'emergency_ref' => $emergency_ref,
                    'token' => $token,
                    'kct_tokens' => $kct_tokens,
                    'unit' => $unit,
                    'calc' => $calculated,
                    'owner' => $owner,
                    'tariff' => $tariff,
                ];
            });
        } catch (Throwable $e) {
            self::logFailure($meter, $tariff_id, $amount, $e, $debtCheck);
            throw $e;
        }

        self::logSuccess(
            $meter,
            $tariff_id,
            $amount,
            $result['emergency_ref'],
            $result['owner'],
            $result['token'],
            $result['kct_tokens'],
            $result['unit']
        );

        return [
            'emergency_ref' => $result['emergency_ref'],
            'token' => $result['token'],
            'kct_tokens' => $result['kct_tokens'],
            'meterNo' => $meter->meterNo,
            'amount' => $amount,
            'unit' => $result['unit'],
            'tariff_id' => $tariff_id,
        ];
    }

    private static function logSuccess(
        Meter $meter,
        int $tariff_id,
        int $amount,
        string $emergency_ref,
        User $owner,
        string $token,
        $kct_tokens,
        float $unit
    ): void {
        $creator = auth()->user();

        Logger::critical('Emergency token generated', [
            'emergency_ref' => $emergency_ref,
            'token' => $token,
            'meter' => [
                'id' => $meter->id,
                'meterNo' => $meter->meterNo,
                'estate_id' => $meter->estate_id,
            ],
            'tariff_id' => $tariff_id,
            'amount' => $amount,
            'unit' => $unit,
            'kct_tokens' => $kct_tokens,
            'creator' => [
                'id' => $creator->id ?? null,
                'name' => ($creator->first_name ?? '') . ' ' . ($creator->last_name ?? ''),
                'email' => $creator->email ?? null,
                'role' => $creator->role ?? null,
            ],
            'owner' => [
                'id' => $owner->id,
                'name' => ($owner->first_name ?? '') . ' ' . ($owner->last_name ?? ''),
                'email' => $owner->email,
            ],
        ]);
    }

    private static function logFailure(
        Meter $meter,
        int $tariff_id,
        int $amount,
        Throwable $e,
        ?array $debtCheck
    ): void {
        $message = $e->getMessage();
        $reason = $message;

        if (str_contains($message, 'has outstanding debt')) {
            $context = 'unpaid_debt_blocked';
        } elseif (str_contains($message, 'Amount cannot exceed')) {
            $context = 'amount_above_cap';
        } elseif (str_contains($message, 'Amount too small')) {
            $context = 'amount_too_small';
        } elseif (str_contains($message, 'Kwh purchase cannot be less')) {
            $context = 'unit_too_small';
        } elseif (str_contains($message, 'Emergency token generation failed')) {
            $context = 'token_generation_failed';
        } elseif (str_contains($message, 'Meter is not attached to any customer')) {
            $context = 'no_owner';
        } elseif (str_contains($message, 'Tariff not found')) {
            $context = 'tariff_not_found';
        } else {
            $context = 'unexpected';
        }

        Logger::error('Emergency token generation failed', [
            'meterNo' => $meter->meterNo,
            'tariff_id' => $tariff_id,
            'amount' => $amount,
            'owner_id' => $meter->user_id,
            'reason' => $reason,
            'unpaid_debt' => $debtCheck['total_owed'] ?? null,
            'context' => $context,
        ]);
    }
}