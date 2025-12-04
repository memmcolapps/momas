<?php

namespace App\Services;

use App\Models\CustomerArrear;
use App\Models\Estate;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class ArrearsService
{
    /**
     * Get customer's total outstanding arrears
     */
    public static function getTotalArrears($userId)
    {
        return CustomerArrear::where('user_id', $userId)
            ->whereIn('status', [0, 1]) // Unpaid or partially paid
            ->where('balance', '>', 0)
            ->sum('balance');
    }

    /**
     * Calculate minimum arrears payment based on estate percentage
     */
    public static function calculateMinimumPayment($userId, $estateId)
    {
        $totalArrears = self::getTotalArrears($userId);

        if ($totalArrears == 0) {
            return 0;
        }

        $estate = Estate::find($estateId);
        $percentage = $estate->arrears_payment_percentage ?? 100;

        return ($totalArrears * $percentage) / 100;
    }

    /**
     * Process arrears payment (FIFO - oldest first)
     */
    public static function processArrearsPayment($userId, $paymentAmount, $transactionId = null)
    {
        $remainingPayment = $paymentAmount;
        $totalPaid = 0;

        $arrears = CustomerArrear::where('user_id', $userId)
            ->where('balance', '>', 0)
            ->whereIn('status', [0, 1])
            ->orderBy('created_at', 'asc') // Oldest first
            ->get();

        foreach ($arrears as $arrear) {
            if ($remainingPayment <= 0) break;

            $paidAmount = $arrear->applyPayment($remainingPayment, $transactionId);
            $remainingPayment -= $paidAmount;
            $totalPaid += $paidAmount;
        }

        return $totalPaid;
    }

    /**
     * Check if customer had transaction this month
     */
    public static function hadTransactionThisMonth($userId)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return Transaction::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', 2)
            ->exists();
    }

    /**
     * Get customer arrears summary
     */
    public static function getArrearsSummary($userId)
    {
        $arrears = CustomerArrear::where('user_id', $userId)
            ->whereIn('status', [0, 1])
            ->where('balance', '>', 0)
            ->get();

        return [
            'total_arrears' => $arrears->sum('balance'),
            'fixed_charge_arrears' => $arrears->where('arrear_type', 'fixed_charge')->sum('balance'),
            'utility_arrears' => $arrears->whereIn('arrear_type', ['utility_admin_fee', 'utility_service'])->sum('balance'),
            'service_charge_arrears' => $arrears->where('arrear_type', 'customer_service_charge')->sum('balance'),
            'oldest_arrear_date' => $arrears->min('generated_date'),
            'count' => $arrears->count(),
        ];
    }

    /**
     * Validate if customer can afford purchase with arrears
     */
    public static function canAffordPurchase($userId, $estateId, $purchaseAmount)
    {
        $estate = Estate::find($estateId);
        $minPayment = self::calculateMinimumPayment($userId, $estateId);
        $minPurchase = $estate->min_pur ?? 0;

        $requiredAmount = $minPayment + $minPurchase;

        return [
            'can_afford' => $purchaseAmount >= $requiredAmount,
            'required_amount' => $requiredAmount,
            'arrears_payment' => $minPayment,
            'min_purchase' => $minPurchase,
            'remaining_for_electricity' => max(0, $purchaseAmount - $minPayment),
        ];
    }
}
