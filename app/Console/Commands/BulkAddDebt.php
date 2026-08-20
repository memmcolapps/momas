<?php

namespace App\Console\Commands;

use App\Models\Meter;
use App\Models\Utility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BulkAddDebt extends Command
{
    protected $signature = 'app:bulk-add-debt {file} {amount} {title=Bulk Debt}';

    protected $description = 'Read meter numbers from a file (one per line) and add a debt of the given amount to each meter\'s user';

    public function handle(): int
    {
        $file = $this->argument('file');
        $amount = $this->argument('amount');
        $title = $this->argument('title');

        if (!is_numeric($amount) || $amount <= 0) {
            $this->error('Amount must be a positive number.');
            return 1;
        }

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $meterNumbers = array_map('trim', $lines);
        $meterNumbers = array_filter($meterNumbers);

        if (empty($meterNumbers)) {
            $this->error('No meter numbers found in file.');
            return 1;
        }

        $this->info("Processing " . count($meterNumbers) . " meter(s) with debt of {$amount}...");

        $created = 0;
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

                Utility::create([
                    'user_id' => $meter->user_id,
                    'estate_id' => $meter->estate_id,
                    'type' => 'debt',
                    'title' => $title,
                    'amount' => $amount,
                    'start_date' => now()->toDateString(),
                    'mode_of_payment' => 'full_payment',
                    'activated' => true,
                    'operator_id' => 1,
                ]);

                $created++;
                $this->line("  Debt created for meter {$meterNo} (user_id: {$meter->user_id})");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info("Done. Created: {$created}, Skipped: {$skipped}");

        if (!empty($skippedMeters)) {
            $this->warn('Skipped meters:');
            foreach ($skippedMeters as $m) {
                $this->warn("  - {$m}");
            }
        }

        return 0;
    }
}
