<?php

namespace App\Http\Controllers;

use App\Constants\ServiceTypeConstants;
use App\Constants\TransactionConstants;
use App\Models\Token;
use App\Models\Transaction;
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

    public function getAnalyticsPage(Request $request)
    {
        $auth_user = Auth::user();
        $now = Carbon::now();

        $current_year = Carbon::now()->year;
        $min_year = $current_year - 5;

        $available_years = range($current_year, $min_year);
        foreach ($available_years as $idx => $year) {
            $available_years[$idx] = (string) $year;
        }


        $year_start = Carbon::now()->startOfYear();
        $month_start = $year_start;
        $transaction_month_start = Carbon::now()->startOfMonth();
        $last_month_start = Carbon::now()->subMonth()->startOfMonth();
        $last_month_end = Carbon::now()->subMonth()->endOfMonth();


        $total_month_sum = Transaction::byStatus(TransactionConstants::TRANSACTION_COMPLETE)
            ->where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$transaction_month_start, $now])
            ->sum('amount');


        $last_month_sum = Transaction::byStatus(TransactionConstants::TRANSACTION_COMPLETE)
            ->where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$last_month_start, $last_month_end])
            ->sum('amount');

        $month_change_percent = $last_month_sum > 0
            ? round((($total_month_sum - $last_month_sum) / $last_month_sum) * 100, 1)
            : 0;


        $trx_by_month = Transaction::byStatus(TransactionConstants::TRANSACTION_COMPLETE)
            ->where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$year_start, $now])
            ->select([
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
            ])
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');


        $byYear = [];
        for ($m = 1; $m <= 12; $m++) {
            $byYear[] = [
                'month'             => $m,
                'total_amount'      => isset($trx_by_month[$m]) ? (float) $trx_by_month[$m]->total_amount : 0,
                'transaction_count' => isset($trx_by_month[$m]) ? (int) $trx_by_month[$m]->transaction_count : 0,
            ];
        }


        $serviceTypes = [
            ServiceTypeConstants::AIRTIME_TOP_UP,
            ServiceTypeConstants::DATA_TOP_UP,
            ServiceTypeConstants::CREDIT_TOKEN,
            ServiceTypeConstants::CABLE_SUBSCRIPTION,
        ];

        $this_month_by_service = Transaction::byStatus(TransactionConstants::TRANSACTION_COMPLETE)
            ->where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$year_start, $now])
            ->whereIn('service_type', $serviceTypes)
            ->select([
                'service_type',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
            ])
            ->groupBy('service_type')
            ->get()
            ->keyBy('service_type');

        $last_month_by_service = Transaction::byStatus(TransactionConstants::TRANSACTION_COMPLETE)
            ->where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$last_month_start, $last_month_end])
            // ->whereIn('service_type', $serviceTypes)
            ->select([
                'service_type',
                DB::raw('SUM(amount) as total_amount'),
            ])
            ->groupBy('service_type')
            ->get()
            ->keyBy('service_type');

        $byServiceType = [];
        foreach ($serviceTypes as $type) {
            $current  = isset($this_month_by_service[$type])  ? (float) $this_month_by_service[$type]->total_amount  : 0;
            $previous = isset($last_month_by_service[$type]) ? (float) $last_month_by_service[$type]->total_amount : 0;

            $change_percent = $previous > 0
                ? round((($current - $previous) / $previous) * 100, 1)
                : 0;

            $byServiceType[] = [
                'service_type'      => $type,
                'total_amount'      => $current,
                'transaction_count' => isset($this_month_by_service[$type]) ? (int) $this_month_by_service[$type]->transaction_count : 0,
                'change_percent'    => $change_percent,
                'trend'             => $change_percent >= 0 ? 'up' : 'down',
            ];
        }


        $tokens = Token::where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$month_start, $now])
            ->select([
                'status',
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $tokenStatuses = [ 0 => 'pending', 1 => 'failed', 2 => 'used' ];
        $total = 0;
        $breakdown = [];

        foreach ($tokenStatuses as $status => $value) {
            $count  = isset($tokens[$status]) ? (int) $tokens[$status]->count : 0;
            $total += $count;
            $breakdown[] = ['status' => $value, 'count' => $count];
        }

        return StandardResponse::success(200, 'Fetched Analytics', [
            'total_month_amount'    => (float) $total_month_sum,
            'month_change_percent'  => $month_change_percent,
            'year'                  => (string) $current_year,
            'month_trend'           => $month_change_percent >= 0 ? 'up' : 'down',
            'months'               => $byYear,
            'services'       => $byServiceType,
            'token_breakdown' => $breakdown,
            'available_years' => $available_years,
            'period' => [
                'start_date' => $year_start->toDateString(),
                'end_date'   => $now->toDateString(),
            ],
        ]);
    }

    public function filterTransactionChartData(Request $request)
    {
        $auth_user = Auth::user();
        $current_year = Carbon::now()->year;
        $min_year = $current_year - 5;

        $validator = Validator::make($request->all(), [
            'year' => ["nullable", "integer", "min:{$min_year}", "max:{$current_year}"],
        ]);

        if ($validator->fails()) {
            return StandardResponse::error(422, 'Validation Error', [ 'validation_error' => $validator->errors() ]);
        }

        $year = (int) $request->input('year', $current_year);
        // dd($year);

        $start = Carbon::create($year)->startOfYear();
        $end   = $year === $current_year
            ? Carbon::now()
            : Carbon::create($year)->endOfYear();

        $trx_by_month = Transaction::byStatus(TransactionConstants::TRANSACTION_COMPLETE)
            ->where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$start, $end])
            ->select([
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
            ])
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Current year = up to current month only | Past year = all 12 months
        $months_to_show = $year === $current_year ? Carbon::now()->month : 12;

        $available_years = range($current_year, $min_year);

        foreach ($available_years as $idx => $t_year) {
            $available_years[$idx] = (string) $t_year;
        }

        $months = [];
        for ($m = 1; $m <= $months_to_show; $m++) {
            $months[] = [
                'month'             => $m,
                'month_label'       => Carbon::create()->month($m)->format('M'),
                'total_amount'      => isset($trx_by_month[$m]) ? (float) $trx_by_month[$m]->total_amount : 0,
                'transaction_count' => isset($trx_by_month[$m]) ? (int)   $trx_by_month[$m]->transaction_count : 0,
            ];
        }

        return StandardResponse::success(200, 'Fetch Transaction Chart Data', [
            'year'            => (string) $year,
            'available_years' => $available_years,
            'months'          => $months,
        ]);
    }

    public function filterUtilityMetrics(Request $request)
    {
        $auth_user = Auth::user();
        $current_year = Carbon::now()->year;
        $min_year = $current_year - 5;

        $validator = Validator::make($request->all(), [
            'year' => ["nullable", "integer", "min:{$min_year}", "max:{$current_year}"],
        ]);

        if ($validator->fails()) {
            return StandardResponse::error(422, $validator->errors());
        }

        $year = (int) $request->input('year', $current_year);

        $start = Carbon::create($year)->startOfYear();
        $end   = $year === $current_year
            ? Carbon::now()
            : Carbon::create($year)->endOfYear();

        // Previous year range for % change comparison
        $prev_start = Carbon::create($year - 1)->startOfYear();
        $prev_end   = Carbon::create($year - 1)->endOfYear();

        $serviceTypes = ['airtime_top_up', 'data_top_up', 'credit_token'];

        $current_by_service = Transaction::byStatus(TransactionConstants::TRANSACTION_COMPLETE)
            ->where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('service_type', $serviceTypes)
            ->select([
                'service_type',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count'),
            ])
            ->groupBy('service_type')
            ->get()
            ->keyBy('service_type');

        $prev_by_service = Transaction::byStatus(TransactionConstants::TRANSACTION_COMPLETE)
            ->where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$prev_start, $prev_end])
            ->whereIn('service_type', $serviceTypes)
            ->select([
                'service_type',
                DB::raw('SUM(amount) as total_amount'),
            ])
            ->groupBy('service_type')
            ->get()
            ->keyBy('service_type');

        $services = [];
        foreach ($serviceTypes as $type) {
            $current  = isset($current_by_service[$type]) ? (float) $current_by_service[$type]->total_amount  : 0;
            $previous = isset($prev_by_service[$type])    ? (float) $prev_by_service[$type]->total_amount     : 0;

            $change_percent = $previous > 0
                ? round((($current - $previous) / $previous) * 100, 1)
                : 0;

            $services[] = [
                'service_type'      => $type,
                'total_amount'      => $current,
                'transaction_count' => isset($current_by_service[$type]) ? (int) $current_by_service[$type]->transaction_count : 0,
                'change_percent'    => $change_percent,
                'trend'             => $change_percent >= 0 ? 'up' : 'down',
            ];
        }

        $available_years = range($current_year, $min_year);
        foreach ($available_years as $idx => $year) {
            $available_years[$idx] = (string) $year;
        }

        return StandardResponse::success(200, 'Fetched utilities metrics successfully', [
            'year'            => (string) $year,
            'available_years' => $available_years,
            'services'        => $services,
        ]);
    }

    public function filterAccessTokens(Request $request)
    {
        $auth_user = Auth::user();
        $current_year = Carbon::now()->year;
        $min_year = $current_year - 5;

        $validator = Validator::make($request->all(), [
            'year' => ["nullable", "integer", "min:{$min_year}", "max:{$current_year}"],
        ]);

        if ($validator->fails()) {
            return StandardResponse::error(422, $validator->errors());
        }

        $year = (int) $request->input('year', $current_year);

        $start = Carbon::create($year)->startOfYear();
        $end   = $year === $current_year
            ? Carbon::now()
            : Carbon::create($year)->endOfYear();

        $tokens = Token::where('user_id', $auth_user->id)
            ->whereBetween('created_at', [$start, $end])
            ->select([
                'status',
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // dd($tokens->toArray());

        $tokenStatuses = [ 0 => 'pending', 1 => 'failed', 2 => 'used' ];
        $total = 0;
        $breakdown = [];

        foreach ($tokenStatuses as $status => $value) {
            $count  = isset($tokens[$status]) ? (int) $tokens[$status]->count : 0;
            $total += $count;
            $breakdown[] = ['status' => $value, 'count' => $count];
        }

        // Inject percentage for donut chart rendering
        foreach ($breakdown as &$entry) {
            $entry['percentage'] = $total > 0
                ? round(($entry['count'] / $total) * 100, 1)
                : 0;
        }
        unset($entry);

        $available_years = range($current_year, $min_year);
        foreach ($available_years as $idx => $year) {
            $available_years[$idx] = (string) $year;
        }

        return StandardResponse::success(200, 'Fetched access tokens', [
            'year'            => (string) $year,
            'available_years' => $available_years,
            'total'           => $total,
            'token_breakdown'       => $breakdown,
        ]);
    }
}
