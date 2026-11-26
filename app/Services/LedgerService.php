<?php

namespace App\Services;

use App\Constants\EstateMeterVendingType;
use App\Models\Estate;
use App\Models\PostpaidAccumulationPayment;
use App\Models\TokenLedger;
use Carbon\Carbon;
use Exception;

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
        $estate = $transaction->estate;
        $fee_type = $estate->transaction_fee_type;
        $fee = $estate->transaction_fee;

        if ($fee_type == 'flat') {
            return $fee;
        }

        if ($fee > 100) {
            throw new Exception('A Percentage Fee of Greater than 100 is not possible recheck Estate Transaction Fee');
        }

        $uncapped_fee = ($fee / 100) * $amount;
        $momas_max = config('constants.momas_max_transaction_fee') - 2000; // subtract paystack fee from the cap to get momas only cap
        $capped_fee = min($uncapped_fee, $momas_max);

        return $capped_fee;
    }

    public static function recordTransactionLedger(
        $transaction,
        string $meterNo,
        int $creditTokenId,
        ?int $receiverId = null,
        string $tokenType = TokenLedger::TYPE_CREDIT_TOKEN
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
                'token_type'      => $tokenType,
                'receiver_id'     => $receiverId ?? $transaction->user_id,
                'paid_at'         => null,
            ]
        );
    }

    public static function recordPostpaidTokenLedger(
        $transaction,
        string $meterNo,
        int $tokenRecordId,
        string $tokenType,
        ?int $receiverId = null
    ): TokenLedger {
        $amount = (float) $transaction->amount;

        return TokenLedger::firstOrCreate(
            ['trx_id' => $transaction->trx_id],
            [
                'user_id'         => $transaction->user_id,
                'estate_id'       => $transaction->estate_id,
                'meterNo'         => $meterNo,
                'credit_token_id' => $tokenRecordId,
                'trx_amount'      => $amount,
                'expected_fee'    => $amount,
                'token_type'      => $tokenType,
                'receiver_id'     => $receiverId ?? $transaction->user_id,
                'paid_at'         => null,
            ]
        );
    }

    public static function estateCanVendPostpaid(Estate $estate): bool
    {
        $months = $estate->fee_accumulation_period ?? 1;
        $cutoff = Carbon::now()->subMonths($months);

        return TokenLedger::where('estate_id', $estate->id)
            ->whereNull('paid_at')
            ->where('created_at', '<=', $cutoff)
            ->sum('expected_fee') <= 0;
    }

    public static function estateSupportsPostpaid(Estate $estate): bool
    {
        return (int) ($estate->estate_meter_vending_type ?? EstateMeterVendingType::PREPAID) === EstateMeterVendingType::POSTPAID;
    }

    public static function markEstatePostpaidLedgersAsPaid(Estate|int $estate, string $trxRef): int
    {
        $estateId = $estate instanceof Estate ? $estate->id : $estate;

        return TokenLedger::where('estate_id', $estateId)
            ->whereNull('paid_at')
            ->update(['paid_at' => Carbon::now()]);
    }

    public static function clearEstatePostpaidLedgersWithBalance(Estate $estate, float $amount): array
    {
        $available = round((float) $estate->postpaid_clear_balance + $amount, 2);

        $estate->postpaid_clear_balance = 0;

        $ledgers = TokenLedger::where('estate_id', $estate->id)
            ->whereNull('paid_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $paidAt = Carbon::now();
        $clearedCount = 0;
        $amountApplied = 0.0;

        foreach ($ledgers as $ledger) {
            $fee = (float) $ledger->expected_fee;

            if ($available + 0.00001 < $fee) {
                break;
            }

            $ledger->paid_at = $paidAt;
            $ledger->save();

            $available = round($available - $fee, 2);
            $amountApplied = round($amountApplied + $fee, 2);
            $clearedCount++;
        }

        $estate->postpaid_clear_balance = $available;
        $estate->save();

        $remainingDue = (float) TokenLedger::where('estate_id', $estate->id)
            ->whereNull('paid_at')
            ->sum('expected_fee');

        return [
            'cleared_count' => $clearedCount,
            'amount_applied' => $amountApplied,
            'remaining_balance' => $available,
            'remaining_due' => round($remainingDue, 2),
        ];
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
