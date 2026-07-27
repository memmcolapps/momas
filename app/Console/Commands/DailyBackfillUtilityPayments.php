<?php

namespace App\Console\Commands;

use App\Models\Logger;
use App\Models\User;
use Illuminate\Console\Command;

class DailyBackfillUtilityPayments extends Command
{
    protected $signature = 'app:backfill-utility-payments';

    protected $description = 'Daily backfill of utility and admin fee payments for all active customers';

    public function handle(): int
    {
        $this->info('Starting daily backfill of utility payments...');

        $customers = User::where('status', 2)
            ->whereNotNull('estate_id')
            ->get();

        $processed = 0;
        $failed = 0;

        foreach ($customers as $customer) {
            try {
                backfill_utility_payments($customer->id, $customer->estate_id);
                $processed++;
            } catch (\Exception $e) {
                $failed++;
                Logger::error("Backfill failed for user {$customer->id}: " . $e->getMessage());
            }
        }

        $this->info("Done. Processed: {$processed} | Failed: {$failed}");
        Logger::info("Daily backfill completed. Processed: {$processed} | Failed: {$failed}");

        return 0;
    }
}
