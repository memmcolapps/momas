<?php

namespace App\Console\Commands;

use App\Models\Meter;
use App\Models\Utility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BulkDeleteDebt extends Command
{
    protected $signature = 'app:bulk-delete-debt {file}';

    protected $description = 'Read meter numbers from a file (one per line) and delete all debt records for each meter\'s user';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $meterNumbers = array_map('trim', $lines);
        $meterNumbers = array_values(array_unique($meterNumbers));

        if (empty($meterNumbers)) {
            $this->error('No meter numbers found in file.');
            return 1;
        }

        $this->info("Processing " . count($meterNumbers) . " unique meter(s)...");

        $deleted = 0;
        $skipped = 0;
        $skippedMeters = [];

        DB::beginTransaction();

        try {
            foreach ($meterNumbers as $meterNo) {
                $meter = Meter::where('meterNo', $meterNo)->first();

                if (!$meter) {
                    $skipped++;
                    $skippedMeters[] = $meterNo;
                    $this->warn("  Meter not found: {$meterNo}");
                    continue;
                }

                $count = Utility::where('user_id', $meter->user_id)
                    ->where('type', 'debt')
                    ->delete();

                $deleted += $count;
                $this->line("  Deleted {$count} debt(s) for meter {$meterNo} (user_id: {$meter->user_id})");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info("Done. Debts deleted: {$deleted}, Skipped meters: {$skipped}");

        if (!empty($skippedMeters)) {
            $this->warn('Skipped meters:');
            foreach ($skippedMeters as $m) {
                $this->warn("  - {$m}");
            }
        }

        return 0;
    }
}
