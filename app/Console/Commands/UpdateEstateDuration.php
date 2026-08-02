<?php

namespace App\Console\Commands;

use App\Models\Estate;
use Illuminate\Console\Command;

class UpdateEstateDuration extends Command
{
    protected $signature = 'estate:update-duration {--dry-run : Preview changes without writing}';

    protected $description = 'Set duration to monthly for estates with a legacy_buid and no duration set';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $estates = Estate::whereNotNull('legacy_buid')
            ->where('legacy_buid', '!=', '')
            ->whereNull('duration')
            ->get();

        if ($estates->isEmpty()) {
            $this->warn('No estates found with a legacy_buid and no duration set.');
            return self::SUCCESS;
        }

        $this->info(
            ($dryRun ? '[DRY-RUN] ' : '') . "Found {$estates->count()} estate(s) with a legacy_buid and no duration set."
        );

        foreach ($estates as $estate) {
            $this->line("  {$estate->id} [{$estate->legacy_buid}] {$estate->title} -> monthly");
        }

        if (!$dryRun) {
            $updated = Estate::whereNotNull('legacy_buid')
                ->where('legacy_buid', '!=', '')
                ->whereNull('duration')
                ->update(['duration' => 'monthly']);

            $this->info("Updated {$updated} estate(s) to monthly.");
        }

        return self::SUCCESS;
    }
}
