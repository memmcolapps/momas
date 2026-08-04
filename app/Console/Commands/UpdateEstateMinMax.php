<?php

namespace App\Console\Commands;

use App\Models\Estate;
use Illuminate\Console\Command;

class UpdateEstateMinMax extends Command
{
    protected $signature = 'estate:update-min-max {--dry-run : Preview changes without writing}';

    protected $description = 'Set min_pur to 1000 and max_pur to 10000000 for legacy estates where each is null';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $estates = Estate::whereNotNull('legacy_buid')
            ->whereRaw("TRIM(legacy_buid) != ''")
            ->get();

        if ($estates->isEmpty()) {
            $this->warn('No estates found with a legacy_buid.');
            return self::SUCCESS;
        }

        $this->info(
            ($dryRun ? '[DRY-RUN] ' : '') . "Processing {$estates->count()} estate(s) with a legacy_buid..."
        );

        $minUpdated = 0;
        $maxUpdated = 0;

        foreach ($estates as $estate) {
            $updates = [];

            if (is_null($estate->min_pur)) {
                $updates['min_pur'] = 1000;
                $this->line("  {$estate->id} [{$estate->legacy_buid}] {$estate->title} -> min_pur: 1000");
                $minUpdated++;
            }

            if (is_null($estate->max_pur)) {
                $updates['max_pur'] = 10000000;
                $this->line("  {$estate->id} [{$estate->legacy_buid}] {$estate->title} -> max_pur: 10000000");
                $maxUpdated++;
            }

            if (!$dryRun && !empty($updates)) {
                $estate->update($updates);
            }
        }

        $this->newLine();
        $this->info("Done: {$minUpdated} min_pur value(s) set, {$maxUpdated} max_pur value(s) set.");

        return self::SUCCESS;
    }
}
