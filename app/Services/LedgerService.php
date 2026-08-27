<?php

namespace App\Services;

use App\Models\Estate;
use App\Models\PostpaidAccumulationPayment;
use App\Models\TokenLedger;
use Carbon\Carbon;

class LedgerService
{
    public static function computeEstateFee(Estate|int $estate): float
    {
        $estateId = $estate instanceof Estate ? $estate->id : $estate;

        return (float) TokenLedger::where('estate_id', $estateId)
            ->whereNull('paid_at')
            ->sum('expected_fee');
    }

    public static function computeTransactionFee($transaction): float
    {
        $amount = $transaction->vending_amount ?? $transaction->amount;

        return round((float) $amount * 0.01, 2);
    }

    public static function recordTransactionLedger(
        $transaction,
        string $meterNo,
        int $creditTokenId,
        ?int $receiverId = null
    ): TokenLedger {
        $fee = self::computeTransactionFee($transaction);
        $amount = (float) ($transaction->vending_amount ?? $transaction->amount);

        return TokenLedger::firstOrCreate(
            ['trx_id' => $transaction->trx_id],
            [
                'user_id'         => $transaction->user_id,
                'estate_id'       => $transaction->estate_id,
                'meterNo'         => $meterNo,
                'credit_token_id' => $creditTokenId,
                'trx_amount'      => $amount,
                'expected_fee'    => $fee,
                'receiver_id'     => $receiverId ?? $transaction->user_id,
                'paid_at'         => null,
            ]
        );
    }

    public static function estateCanVendPostpaid(Estate $estate): bool
    {
        $months = $estate->fee_accumulation_period ?? 1;
        $cutoff = Carbon::now()->subMonths($months);

        return !TokenLedger::where('estate_id', $estate->id)
            ->whereNull('paid_at')
            ->where('created_at', '<=', $cutoff)
            ->exists();
    }

    public static function markEstatePostpaidLedgersAsPaid(Estate|int $estate, string $trxRef): int
    {
        $estateId = $estate instanceof Estate ? $estate->id : $estate;

        return TokenLedger::where('estate_id', $estateId)
            ->whereNull('paid_at')
            ->update(['paid_at' => Carbon::now()]);
    }

    public static function getAccumulationPaymentHistory(Estate|int $estate): \Illuminate\Database\Eloquent\Collection
    {
        $estateId = $estate instanceof Estate ? $estate->id : $estate;

        return PostpaidAccumulationPayment::where('estate_id', $estateId)
            ->where('status', 1)
            ->latest()
            ->get();
    }
}
