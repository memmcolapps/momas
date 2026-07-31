<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\TarrifState;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertTariffFixedCharges extends Command
{
    protected $signature = 'tariff:convert-fixed-charges {--dry-run : Preview changes without writing}';

    protected $description = 'Move active tariff-state fixed charges into per-customer service charge utilities';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $estates = Estate::whereNotNull('legacy_buid')->where('legacy_buid', '!=', '')->get();

        if ($estates->isEmpty()) {
            $this->warn('No estates found with a legacy_buid.');
            return self::SUCCESS;
        }

        $this->info(
            ($dryRun ? '[DRY-RUN] ' : '') . "Processing {$estates->count()} estate(s) with a legacy_buid..."
        );

        $totalStates = 0;
        $totalCharges = 0;
        $totalSkipped = 0;

        foreach ($estates as $estate) {
            $states = TarrifState::where('estate_id', $estate->id)
                ->where('status', 2)
                ->where('fixed_charge', '>', 0)
                ->get();

            if ($states->isEmpty()) {
                $this->line("  {$estate->id} [{$estate->legacy_buid}] {$estate->title}: skipped (no active fixed-charge states)");
                continue;
            }

            $estateCharges = 0;
            $estateSkipped = 0;

            DB::transaction(function () use ($states, $estate, $dryRun, &$totalStates, &$totalCharges, &$totalSkipped, &$estateCharges, &$estateSkipped) {
                foreach ($states as $state) {
                    $totalStates++;

                    $customers = User::where('estate_id', $estate->id)
                        ->where('role', 2)
                        ->where('tariffid', $state->tariff_id)
                        ->get();

                    $branch = 'all customers';
                    if ($customers->isNotEmpty()) {
                        $branch = 'matched tariff ' . $state->tariff_id . ' customers';
                    } else {
                        $customers = User::where('estate_id', $estate->id)
                            ->where('role', 2)
                            ->get();
                    }

                    $this->line(
                        "  {$estate->id} [{$estate->legacy_buid}] {$estate->title}: " .
                        "state #{$state->id} fixed_charge {$state->fixed_charge} -> {$customers->count()} customer(s) ({$branch})"
                    );

                    foreach ($customers as $customer) {
                        $existing = Utility::where('estate_id', $estate->id)
                            ->where('type', 'service_charge')
                            ->where('title', 'Fixed Charge')
                            ->where('user_id', $customer->id)
                            ->exists();

                        if ($existing) {
                            $totalSkipped++;
                            $estateSkipped++;
                            continue;
                        }

                        if (!$dryRun) {
                            Utility::create([
                                'user_id' => $customer->id,
                                'estate_id' => $estate->id,
                                'type' => 'service_charge',
                                'title' => 'Fixed Charge',
                                'amount' => $state->fixed_charge,
                                'duration' => 'monthly',
                                'activated' => true,
                            ]);
                        }

                        $totalCharges++;
                        $estateCharges++;
                    }

                    if (!$dryRun) {
                        $state->fixed_charge = 0;
                        $state->save();
                    }
                }
            });

            if (!$dryRun) {
                $utilityAmount = Utility::where('estate_id', $estate->id)
                    ->serviceCharge()
                    ->sum('amount');

                Estate::where('id', $estate->id)->update(['total_utility_amount' => $utilityAmount]);
            }

            $this->info(
                "  {$estate->id} [{$estate->legacy_buid}] {$estate->title}: " .
                "{$estateCharges} service charge(s) created, {$estateSkipped} skipped"
            );
        }

        $this->newLine();
        $this->info("Done: {$totalStates} tariff state(s) processed, {$totalCharges} service charge(s) created, {$totalSkipped} skipped.");

        return self::SUCCESS;
    }
}
