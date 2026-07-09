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
        $utilities = $this->fetchUtilities($userId, $estateId);
        $this->ensureRecordsExist($utilities, $userId, $estateId);
        $paymentRecords = $this->fetchPaymentRecords($userId, $estateId);
        $this->activateOverdueRecords($paymentRecords, $utilities);
        return $this->computeOwed($utilities, $paymentRecords);
    }

    /**
     * Fetch utilities that are active or should become active (start_date reached).
     */
    private function fetchUtilities(int $userId, int $estateId, bool $lock = false): Collection
    {
        $query = Utility::where('estate_id', $estateId)
            ->where(function ($q) {
                $q->where('activated', true)
                  ->orWhere(function ($q2) {
                      $q2->where('activated', false)
                         ->whereNotNull('start_date')
                         ->where('start_date', '<=', now());
                  });
            })
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereNull('user_id');
            });

        if ($lock) {
            $query->lockForUpdate();
        }

        $utilities = $query->get();

        // Activate utilities whose start_date has arrived
        foreach ($utilities as $utility) {
            if (!$utility->activated) {
                $utility->activated = true;
                $utility->save();
            }
        }

        return $utilities;
    }

    /**
     * Phase A: Clone uncloned utilities into UtilityPaymentRecord (once).
     */
    private function ensureRecordsExist(Collection $utilities, int $userId, int $estateId): void
    {
        foreach ($utilities as $utility) {
            UtilityPaymentRecord::firstOrCreate(
                [
                    'utility_id' => $utility->id,
                    'user_id' => $userId,
                    'estate_id' => $estateId,
                ],
                [
                    'amount' => $utility->amount,
                    'amount_paid' => 0,
                    'activated' => false,
                    'status' => 0,
                ]
            );
        }
    }

    /**
     * Fetch payment records for a user/estate.
     */
    private function fetchPaymentRecords(int $userId, int $estateId, bool $lock = false): Collection
    {
        $query = UtilityPaymentRecord::where('user_id', $userId)
            ->where('estate_id', $estateId);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get()->keyBy('utility_id');
    }

    /**
     * Phase B: Activate UtilityPaymentRecords whose start_date has passed.
     */
    private function activateOverdueRecords(Collection $paymentRecords, Collection $utilities): void
    {
        foreach ($paymentRecords as $record) {
            if (!$record->activated && $record->status === 0) {
                $utility = $utilities->firstWhere('id', $record->utility_id);
                if (
                    $utility &&
                    $utility->start_date &&
                    $utility->start_date <= now() &&
                    (float) $record->amount_paid !== (float) $record->amount
                ) {
                    $record->activated = true;
                    $record->save();
                }
            }
        }
    }

    /**
     * Phase C: Calculate owed amounts per utility.
     */
    private function computeOwed(Collection $utilities, Collection $paymentRecords): array
    {
        $items = [];
        $totalOwed = 0;

        foreach ($utilities as $utility) {
            $record = $paymentRecords->get($utility->id);

            if (!$record || !$record->activated) {
                continue;
            }

            $remaining = max(0, (float) $utility->amount - (float) $record->amount_paid);
            if ($remaining <= 0) {
                continue;
            }

            $installment = $this->getPeriodicAmount($utility);
            $owed = min($installment, $remaining);

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

    /**
     * Determine the periodic installment amount for a utility based on its mode_of_payment.
     */
    private function getPeriodicAmount(Utility $utility): float
    {
        $mode = strtolower(trim($utility->mode_of_payment ?? ''));

        if (str_contains($mode, 'monthly')) {
            if ((float) ($utility->payment_amount ?? 0) > 0) {
                return (float) $utility->payment_amount;
            }
            if (($utility->payment_months ?? 0) > 0) {
                return (float) ($utility->amount / $utility->payment_months);
            }
        }

        if (str_contains($mode, 'percent')) {
            if ((float) ($utility->percent_payment ?? 0) > 0) {
                return (float) ($utility->amount * $utility->percent_payment / 100);
            }
            if ((float) ($utility->payment_amount ?? 0) > 0) {
                return (float) $utility->payment_amount;
            }
        }

        return (float) $utility->amount;
    }

    public function processPayment(
        int $userId,
        int $estateId,
        float $amount,
        ?string $transactionId = null
    ): array {
        try {
            return DB::transaction(function () use ($userId, $estateId, $amount, $transactionId) {
                $utilities = $this->fetchUtilities($userId, $estateId, true);
                $this->ensureRecordsExist($utilities, $userId, $estateId);
                $paymentRecords = $this->fetchPaymentRecords($userId, $estateId, true);
                $this->activateOverdueRecords($paymentRecords, $utilities);

                $result = $this->computeOwed($utilities, $paymentRecords);
                $items = $result['items'];
                $totalOwed = $result['total_owed'];

                if ($amount < $totalOwed) {
                    throw new InsufficientPaymentException($amount, $totalOwed);
                }

                $remaining = $amount;

                foreach ($items as $item) {
                    $utility = $item['utility'];
                    $record = $item['payment_record'];
                    $owed = $item['owed'];
                    $newAmountPaid = ($record->amount_paid ?? 0) + $owed;
                    $fullyPaid = $newAmountPaid >= $utility->amount;

                    $record->amount_paid = $newAmountPaid;
                    $record->activated = !$fullyPaid;
                    $record->status = $fullyPaid ? 2 : 1;
                    $record->save();

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
