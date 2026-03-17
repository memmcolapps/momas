<?php

namespace App\Http\Controllers;

use App\Models\CreditToken;
use App\Services\StandardResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnalyticController extends Controller
{
    public function getTokenPurchaseByMonth(Request $request) {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {

            return StandardResponse::error(422, 'Validation Error', [
                'validation_error' => $validator->errors(),
            ]);
        }

        $start_date = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfYear();

        $end_date = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        $user_id = Auth::user()->id;

        // Join transactions with credit_tokens on trx_id
        $query = DB::table('transactions')
            ->join('credit_tokens', 'credit_tokens.trx_id', '=', 'transactions.trx_id')
            ->where('transactions.user_id', $user_id)
            ->whereBetween('transactions.created_at', [$start_date, $end_date]);

        // Get data grouped by month and status
        $tokenPurchasesByStatus = $query
            ->select(
                DB::raw('MONTH(transactions.created_at) as month'),
                DB::raw('YEAR(transactions.created_at) as year'),
                'transactions.status',
                'transactions.service',
                'transactions.service_type',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(transactions.amount) as total_amount'),
                DB::raw('SUM(credit_tokens.unitkwh) as total_units'),
                DB::raw('SUM(credit_tokens.vatAmount) as total_vat')
            )
            ->groupBy(
                DB::raw('MONTH(transactions.created_at)'),
                DB::raw('YEAR(transactions.created_at)'),
                'transactions.status',
                'transactions.service',
                'transactions.service_type'
            )
            ->orderBy(DB::raw('YEAR(transactions.created_at)'))
            ->orderBy(DB::raw('MONTH(transactions.created_at)'))
            ->orderBy('transactions.status')
            ->get();

        // Group by month with status breakdown
        $groupedData = [];
        $summary = [
            'total_transactions' => 0,
            'total_amount' => 0,
            'total_units' => 0,
            'total_vat' => 0,
        ];

        foreach ($tokenPurchasesByStatus as $item) {
            $monthKey = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);

            if (!isset($groupedData[$monthKey])) {
                $groupedData[$monthKey] = [
                    'month' => $item->month,
                    'year' => $item->year,
                    'month_name' => Carbon::createFromDate($item->year, $item->month)->format('F'),
                    'statuses' => [],
                    'total_transactions' => 0,
                    'total_amount' => 0,
                    'total_units' => 0,
                    'total_vat' => 0,
                ];
            }

            $statusLabel = match($item->status) {
                0 => 'pending',
                1 => 'failed',
                2 => 'successful',
                3 => 'processing',
                default => 'unknown',
            };

            $groupedData[$monthKey]['statuses'][] = [
                'status' => $item->status,
                'status_label' => $statusLabel,
                'service' => $item->service,
                'service_type' => $item->service_type,
                'total_transactions' => $item->total_transactions,
                'total_amount' => (float) $item->total_amount,
                'total_units' => (float) $item->total_units,
                'total_vat' => (float) $item->total_vat,
            ];

            $groupedData[$monthKey]['total_transactions'] += $item->total_transactions;
            $groupedData[$monthKey]['total_amount'] += (float) $item->total_amount;
            $groupedData[$monthKey]['total_units'] += (float) $item->total_units;
            $groupedData[$monthKey]['total_vat'] += (float) $item->total_vat;

            $summary['total_transactions'] += $item->total_transactions;
            $summary['total_amount'] += (float) $item->total_amount;
            $summary['total_units'] += (float) $item->total_units;
            $summary['total_vat'] += (float) $item->total_vat;
        }

        return StandardResponse::success(200, [
            'token_purchases' => array_values($groupedData),
            'summary' => $summary,
            'period' => [
                'start_date' => $start_date->toDateString(),
                'end_date' => $end_date->toDateString(),
            ]
        ]);
    }
}
