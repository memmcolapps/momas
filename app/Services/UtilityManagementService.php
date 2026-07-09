<?php

namespace App\Services;

use App\Exceptions\InsufficientPaymentException;
use App\Models\Logger;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Utility;
use App\Models\UtilityPaymentRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class UtilityManagementService
{
    public function calculateUserOwedUtility(int $userId, int $estateId): array
    {
        $utilities = Utility::where('estate_id', $estateId)
            ->where('activated', true)
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id');
            })
            ->get();

        $paymentRecords = UtilityPaymentRecord::where('user_id', $userId)
            ->where('estate_id', $estateId)
            ->get()
            ->keyBy('utility_id');

        $items = [];
        $totalOwed = 0;

        foreach ($utilities as $utility) {
            $record = $paymentRecords->get($utility->id);

            if ($record) {
                $owed = $utility->amount - $record->amount_paid;
                if ($owed <= 0) {
                    continue;
                }
            } else {
                $owed = $utility->amount;
            }

            $items[] = [
                'utility' => $utility,
                'payment_record' => $record,
                'owed' => $owed,
            ];

            $totalOwed += $owed;
        }

        return [
            'items' => $items,
            'total_owed' => $totalOwed,
        ];
    }

    public function processPayment(
        int $userId,
        int $estateId,
        float $amount,
        ?string $transactionId = null
    ): array {
        try {
            return DB::transaction(function () use ($userId, $estateId, $amount, $transactionId) {
                $utilities = Utility::where('estate_id', $estateId)
                    ->where('activated', true)
                    ->where(function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                          ->orWhereNull('user_id');
                    })
                    ->lockForUpdate()
                    ->get();

                $paymentRecords = UtilityPaymentRecord::where('user_id', $userId)
                    ->where('estate_id', $estateId)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('utility_id');

                $items = [];
                $totalOwed = 0;

                foreach ($utilities as $utility) {
                    $record = $paymentRecords->get($utility->id);

                    if ($record) {
                        $owed = $utility->amount - $record->amount_paid;
                        if ($owed <= 0) {
                            continue;
                        }
                    } else {
                        $owed = $utility->amount;
                    }

                    $items[] = [
                        'utility' => $utility,
                        'payment_record' => $record,
                        'owed' => $owed,
                    ];

                    $totalOwed += $owed;
                }

                if ($amount < $totalOwed) {
                    throw new InsufficientPaymentException($amount, $totalOwed);
                }

                $remaining = $amount;

                foreach ($items as $item) {
                    $utility = $item['utility'];
                    $record = $item['payment_record'];
                    $owed = $item['owed'];
                    $newAmountPaid = ($record ? $record->amount_paid : 0) + $owed;
                    $fullyPaid = $newAmountPaid >= $utility->amount;

                    if (!$record) {
                        UtilityPaymentRecord::create([
                            'utility_id' => $utility->id,
                            'user_id' => $userId,
                            'estate_id' => $estateId,
                            'amount' => $utility->amount,
                            'amount_paid' => $owed,
                            'activated' => !$fullyPaid,
                            'status' => $fullyPaid ? 2 : 1,
                        ]);
                    } else {
                        $record->amount_paid = $newAmountPaid;
                        $record->activated = !$fullyPaid;
                        $record->status = $fullyPaid ? 2 : 1;
                        $record->save();
                    }

                    $remaining -= $owed;
                }

                if ($transactionId) {
                    Transaction::where('trx_id', $transactionId)
                        ->where('user_id', $userId)
                        ->update(['vending_amount' => $remaining]);
                }

                Logger::info('Utility payment processed successfully', [
                    'user_id' => $userId,
                    'estate_id' => $estateId,
                    'amount' => $amount,
                    'total_owed' => $totalOwed,
                    'vending_amount' => $remaining,
                    'transaction_id' => $transactionId,
                    'items_count' => count($items),
                ]);

                return [
                    'status' => true,
                    'message' => 'Utility payment processed successfully',
                    'data' => [
                        'total_owed' => $totalOwed,
                        'amount_paid' => $amount,
                        'vending_amount' => $remaining,
                        'items_settled' => count($items),
                    ],
                ];
            });
        } catch (InsufficientPaymentException $e) {
            throw $e;
        } catch (Throwable $e) {
            Logger::error('Utility payment processing failed', [
                'user_id' => $userId,
                'estate_id' => $estateId,
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Utility payment processing failed: ' . $e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function settleUtilitiesByTransaction(string $transactionId): array
    {
        $trx = Transaction::where('trx_id', $transactionId)->first();

        if (!$trx) {
            return [
                'status' => false,
                'message' => "Transaction {$transactionId} not found",
                'data' => [],
            ];
        }

        if (!$trx->user_id || !$trx->estate_id) {
            return [
                'status' => false,
                'message' => 'Transaction is missing user_id or estate_id',
                'data' => [],
            ];
        }

        return $this->processPayment(
            $trx->user_id,
            $trx->estate_id,
            (float) $trx->amount,
            $transactionId
        );
    }
}
