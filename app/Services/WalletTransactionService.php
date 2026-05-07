<?php

namespace App\Services;

use App\Constants\TransactionConstants;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletTransactionService
{
    public static function creditWalletOnPaymentVerified(Transaction $trx): void
    {
        DB::transaction(function () use ($trx) {
            $user = User::where('id', $trx->user_id)->lockForUpdate()->first();

            if (!$user) {
                throw new Exception('User not found');
            }

            $user->main_wallet += $trx->amount;
            $user->save();

            $trx->status = TransactionConstants::PAYMENT_COMPLETED;
            $trx->save();
        });
    }

    public static function checkWalletBalance(string $trx_id): array
    {
        $trx = Transaction::where('trx_id', $trx_id)->first();

        if (!$trx) {
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        $user = User::where('id', $trx->user_id)->first();

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        if ($user->main_wallet < $trx->amount) {
            return [
                'success' => false,
                'message' => 'Insufficient wallet balance',
                'balance' => $user->main_wallet,
                'required' => $trx->amount
            ];
        }

        return ['success' => true, 'balance' => $user->main_wallet];
    }

    public static function debitWalletOnServiceDelivery(string $trx_id): void
    {
        DB::transaction(function () use ($trx_id) {
            $trx = Transaction::where('trx_id', $trx_id)->lockForUpdate()->firstOrFail();

            $user = User::where('id', $trx->user_id)->lockForUpdate()->firstOrFail();

            if ($user->main_wallet < $trx->amount) {
                throw new Exception('Insufficient wallet balance');
            }

            $user->main_wallet -= $trx->amount;
            $user->save();

            $trx->status = TransactionConstants::TRANSACTION_COMPLETE;
            $trx->save();
        });
    }

    public static function processPaymentV2(string $trx_id): array
    {
        $trx = Transaction::where('trx_id', $trx_id)->first();

        if (!$trx) {
            return ['success' => false, 'message' => 'Transaction not found'];
        }

        if ($trx->status !== TransactionConstants::PAYMENT_PENDING) {
            return ['success' => false, 'message' => 'Invalid transaction status'];
        }

        $user = User::where('id', $trx->user_id)->lockForUpdate()->first();

        $user->main_wallet += $trx->amount;
        $user->save();

        $trx->status = TransactionConstants::PAYMENT_COMPLETED;
        $trx->save();

        return ['success' => true, 'message' => 'Wallet credited successfully'];
    }

    public static function processServiceDelivery(string $trx_id, callable $serviceCallback): array
    {
        return DB::transaction(function () use ($trx_id, $serviceCallback) {
            $trx = Transaction::where('trx_id', $trx_id)->lockForUpdate()->firstOrFail();

            if ($trx->status !== TransactionConstants::PAYMENT_COMPLETED) {
                throw new Exception('Transaction not ready for service delivery');
            }

            $user = User::where('id', $trx->user_id)->lockForUpdate()->firstOrFail();

            if ($user->main_wallet < $trx->amount) {
                throw new Exception('Insufficient wallet balance for service delivery');
            }

            $serviceResult = $serviceCallback($trx);

            if (!$serviceResult['success'] ?? true) {
                $trx->status = TransactionConstants::PENDING_REVIEW;
                $trx->save();
                throw new Exception('Service delivery failed - transaction flagged for review');
            }

            $user->main_wallet -= $trx->amount;
            $user->save();

            $trx->status = TransactionConstants::TRANSACTION_COMPLETE;
            $trx->save();

            return ['success' => true, 'data' => $serviceResult['data'] ?? null];
        });
    }
}