<?php

namespace App\Services;

use App\Constants\UserUtilityStatus;
use App\Exceptions\InsufficientPaymentException;
use App\Models\Logger;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Utility;
use App\Models\UserUtility;
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
        $userUtilities = $this->fetchUserUtilities($userId, $estateId);
        $this->activateOverdueRecords($userUtilities, $utilities);
        return $this->computeOwed($utilities, $userUtilities);
    }

    private function fetchUtilities(int $userId, int $estateId, bool $lock = false): Collection
    {
        $query = Utility::where('estate_id', $estateId)
            ->where('type', 'debt')
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

        foreach ($utilities as $utility) {
            if (!$utility->activated) {
                $utility->activated = true;
                $utility->save();
            }
        }

        return $utilities;
    }

    private function ensureRecordsExist(Collection $utilities, int $userId, int $estateId): void
    {
        foreach ($utilities as $utility) {
            UserUtility::firstOrCreate(
                [
                    'utility_id' => $utility->id,
                    'user_id' => $userId,
                    'estate_id' => $estateId,
                ],
                [
                    'amount' => $utility->amount,
                    'amount_paid' => $utility->amount - ($utility->balance ?? 0),
                    'activated' => $utility->activated,
                    'status' => UserUtilityStatus::INACTIVE,
                ]
            );
        }
    }

    private function fetchUserUtilities(int $userId, int $estateId, bool $lock = false): Collection
    {
        $query = UserUtility::where('user_id', $userId)
            ->where('estate_id', $estateId);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get()->keyBy('utility_id');
    }

    private function activateOverdueRecords(Collection $userUtilities, Collection $utilities): void
    {
        // dd($utilities->toArray(), $userUtilities->toArray());
        foreach ($userUtilities as $userUtility) {
            if (!$userUtility->activated && $userUtility->status == UserUtilityStatus::INACTIVE) {
                $utility = $utilities->firstWhere('id', $userUtility->utility_id);
                if (
                    $utility &&
                    $utility->start_date &&
                    $utility->start_date <= now() &&
                    (float) $userUtility->amount_paid !== (float) $userUtility->amount
                ) {
                    $userUtility->activated = true;
                    $userUtility->save();
                }
            }
        }
    }

    private function computeOwed(Collection $utilities, Collection $userUtilities): array
    {
        $items = [];
        $totalOwed = 0;

        // dd($utilities->toArray());
        foreach ($utilities as $utility) {
            $userUtility = $userUtilities->get($utility->id);

            if (!$userUtility || !$userUtility->activated) {
                continue;
            }

            $remaining = max(0, (float) $utility->amount - (float) $userUtility->amount_paid);

            if ($remaining <= 0) {
                continue;
            }

            $installment = $this->getPeriodicAmount($utility, $userUtility->user_id);
            $owed = min($installment, $remaining);
            dump($remaining, $userUtility->toArray(), $owed);

            $items[] = [
                'utility' => $utility,
                'user_utility' => $userUtility,
                'owed' => $owed,
            ];

            $totalOwed += $owed;
        }
        dd('');

        return [
            'items' => $items,
            'total_owed' => $totalOwed,
        ];
    }

    private function getPeriodicAmount(Utility $utility, ?int $userId = null): float
    {
        $mode = strtolower(trim($utility->mode_of_payment ?? ''));

        if (str_contains($mode, 'monthly')) {
            if ($userId) {
                $hasPaidThisMonth = UtilityPaymentRecord::where('utility_id', $utility->id)
                    ->where('user_id', $userId)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->exists();
                if ($hasPaidThisMonth) {
                    return 0.0;
                }
            }
            if (($utility->payment_months ?? 0) > 0) {
                return (float) ($utility->amount / $utility->payment_months);
            }
        }

        if (str_contains($mode, 'percent')) {
            if ((float) ($utility->percent_payment ?? 0) > 0) {
                return (float) ($utility->amount * $utility->percent_payment / 100);
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
                $userUtilities = $this->fetchUserUtilities($userId, $estateId, true);
                $this->activateOverdueRecords($userUtilities, $utilities);

                $result = $this->computeOwed($utilities, $userUtilities);
                $items = $result['items'];
                $totalOwed = $result['total_owed'];

                if ($amount < $totalOwed) {
                    throw new InsufficientPaymentException($amount, $totalOwed);
                }

                $remaining = $amount;

                foreach ($items as $item) {
                    $utility = $item['utility'];
                    $userUtility = $item['user_utility'];
                    $owed = $item['owed'];
                    $newAmountPaid = ($userUtility->amount_paid ?? 0) + $owed;
                    $fullyPaid = $newAmountPaid >= $utility->amount;

                    $userUtility->amount_paid = $newAmountPaid;
                    $userUtility->activated = !$fullyPaid;
                    $userUtility->status = $fullyPaid ? UserUtilityStatus::PAID : UserUtilityStatus::ACTIVE;
                    $userUtility->save();

                    UtilityPaymentRecord::create([
                        'user_utility_id' => $userUtility->id,
                        'utility_id' => $utility->id,
                        'user_id' => $userId,
                        'estate_id' => $estateId,
                        'utility_amount' => $utility->amount,
                        'amount_paid' => $owed,
                        'trx_id' => $transactionId,
                        'status' => $fullyPaid ? 2 : 1,
                    ]);

                    $remaining -= $owed;
                }

                if ($transactionId) {
                    Transaction::where('trx_id', $transactionId)
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
