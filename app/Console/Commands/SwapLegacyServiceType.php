<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SwapLegacyServiceType extends Command
{
    protected $signature = 'estate:swap-service-type {estate_id} {--dry-run}';

    protected $description = 'Update legacy_ecmi transactions for an estate to credit_token in the service_type and service columns';

    public function handle(): int
    {
        $estateId = $this->argument('estate_id');

        $estate = DB::table('estates')
            ->where('id', $estateId)
            ->first();

        if (!$estate) {
            $this->error("Estate not found.");
            return self::FAILURE;
        }

        $query = DB::table('transactions')
            ->where('estate_id', $estateId)
            ->where('service', 'legacy_ecmi');

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info("No legacy_ecmi transactions found for estate {$estateId}.");
            return self::SUCCESS;
        }

        $this->line("Estate: {$estate->title} (ID: {$estate->id})");
        $this->line("Affected transactions: {$count}");

        if ($this->option('dry-run')) {
            $sample = (clone $query)->limit(5)->pluck('trx_id')->toArray();
            $this->line("Sample trx_ids: " . implode(', ', $sample));
            $this->info("Dry run — no changes made.");
            return self::SUCCESS;
        }

        if (!$this->confirm("Update {$count} transactions from legacy_ecmi to credit_token?")) {
            return self::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $updated = $query->update([
                'service_type' => 'credit_token',
                'service'      => 'credit_token',
                'migrated'     => true,
            ]);

            DB::commit();

            $this->info("Updated {$updated} transaction(s).");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
