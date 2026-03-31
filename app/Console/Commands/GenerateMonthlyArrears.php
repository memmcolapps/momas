<?php

namespace App\Console\Commands;

use App\Models\CustomerArrear;
use App\Models\Meter;
use App\Models\TarrifState;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UtilitiesPayment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Logger;

class GenerateMonthlyArrears extends Command
{
    protected $signature = 'arrears:generate';
    protected $description = 'Generate monthly arrears for fixed charges and utilities (runs daily with 5-day grace period)';

    public function handle()
    {
        $this->info('Starting arrears generation...');

        $lastMonth = Carbon::now()->subMonth();
        $billingPeriod = $lastMonth->format('Y-m');
        $graceDate = Carbon::now()->startOfMonth()->addDays(5);

        // Only run if we're past grace period
        if (Carbon::now()->lt($graceDate)) {
            $this->info('Still in grace period. Skipping.');
            return 0;
        }

        // Check if already generated
        $exists = CustomerArrear::where('billing_period', $billingPeriod)
            ->where('arrear_type', 'fixed_charge')->count();

        if ($exists > 0) {
            $this->info("Already generated for {$billingPeriod}");
            return 0;
        }

        DB::beginTransaction();
        try {
            $fixedCount = $this->generateFixedChargeArrears($lastMonth, $billingPeriod);
            $utilCount = $this->generateUtilitiesArrears($lastMonth, $billingPeriod);

            DB::commit();
            $this->info("✓ {$fixedCount} fixed charges, {$utilCount} utilities");
            Logger::info("Arrears generated: {$fixedCount} fixed, {$utilCount} utilities for {$billingPeriod}");
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            Logger::error('Arrears generation failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function generateFixedChargeArrears($lastMonth, $billingPeriod)
    {
        $count = 0;
        $start = $lastMonth->copy()->startOfMonth();
        $end = $lastMonth->copy()->endOfMonth();

        $customers = User::where('status', 2)
            ->whereNotNull('meterNo')->where('meterNo', '!=', '')->get();

        foreach ($customers as $customer) {
            $hadTransaction = Transaction::where('user_id', $customer->id)
                ->whereBetween('created_at', [$start, $end])
                ->where('status', 2)->exists();

            if (!$hadTransaction) {
                $meter = Meter::where('user_id', $customer->id)->first();
                if (!$meter) continue;

                $tariff = TarrifState::where('estate_id', $customer->estate_id)
                    ->where('status', 2)->where('fixed_charge', '>', 0)->first();

                if ($tariff && $tariff->fixed_charge > 0) {
                    CustomerArrear::create([
                        'user_id' => $customer->id,
                        'meter_no' => $customer->meterNo,
                        'estate_id' => $customer->estate_id,
                        'arrear_type' => 'fixed_charge',
                        'description' => "Fixed charge for {$lastMonth->format('F Y')} (no transaction)",
                        'amount_due' => $tariff->fixed_charge,
                        'amount_paid' => 0.00,
                        'balance' => $tariff->fixed_charge,
                        'billing_period' => $billingPeriod,
                        'due_date' => $end->toDateString(),
                        'generated_date' => now()->toDateString(),
                        'status' => 0,
                        'reference_table' => 'tarrif_states',
                        'reference_id' => $tariff->id,
                    ]);
                    $count++;
                }
            }
        }
        return $count;
    }

    private function generateUtilitiesArrears($lastMonth, $billingPeriod)
    {
        $count = 0;
        $overdue = UtilitiesPayment::whereIn('status', [0, 2])
            ->where('next_due_date', '<', Carbon::now()->subDays(5))->get();

        foreach ($overdue as $util) {
            $exists = CustomerArrear::where('reference_table', 'utilities_payments')
                ->where('reference_id', $util->id)->exists();

            if (!$exists) {
                $balance = $util->total_amount - $util->amount;
                if ($balance > 0) {
                    $user = User::find($util->user_id);
                    if (!$user) continue;

                    CustomerArrear::create([
                        'user_id' => $util->user_id,
                        'meter_no' => $user->meterNo ?? 'N/A',
                        'estate_id' => $util->estate_id,
                        'arrear_type' => $util->type === 'admin_fee' ? 'utility_admin_fee' : 'utility_service',
                        'description' => "Overdue {$util->type}",
                        'amount_due' => $balance,
                        'amount_paid' => 0.00,
                        'balance' => $balance,
                        'billing_period' => $billingPeriod,
                        'due_date' => $util->next_due_date,
                        'generated_date' => now()->toDateString(),
                        'status' => 0,
                        'reference_table' => 'utilities_payments',
                        'reference_id' => $util->id,
                    ]);
                    $count++;
                }
            }
        }
        return $count;
    }
}
