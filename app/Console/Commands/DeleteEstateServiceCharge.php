<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\UserUtility;
use App\Models\Utility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteEstateServiceCharge extends Command
{
    protected $signature = 'estate:delete-service-charge {estate_id} {--dry-run : Preview changes without deleting}';

    protected $description = 'Delete service_charge type utilities on an estate and its residents';

    public function handle(): int
    {
        $estateId = $this->argument('estate_id');

        $estate = Estate::find($estateId);

        if (!$estate) {
            $this->error("Estate #{$estateId} not found.");
            return self::FAILURE;
        }

        $this->info("Estate: {$estate->title} (ID: {$estate->id})");

        $utilities = Utility::where('estate_id', $estateId)
            ->where('type', 'service_charge')
            ->get();

        if ($utilities->isEmpty()) {
            $this->warn('No service_charge type utilities found for this estate.');
            return self::SUCCESS;
        }

        $utilityIds = $utilities->pluck('id');

        $userUtilities = UserUtility::whereIn('utility_id', $utilityIds)->get();

        $this->newLine();
        $this->info("Found {$utilities->count()} service_charge utility(ies):");
        foreach ($utilities as $utility) {
            $this->line("  - [{$utility->id}] {$utility->title} (amount: {$utility->amount})");
        }

        $this->newLine();
        $this->info("Found {$userUtilities->count()} linked user_utilities record(s) to delete.");

        if ($this->option('dry-run')) {
            $this->warn('[DRY-RUN] No changes made. Remove --dry-run to execute.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Proceed with deletion? This cannot be undone.')) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        try {
            DB::beginTransaction();

            UserUtility::whereIn('utility_id', $utilityIds)->delete();
            Utility::whereIn('id', $utilityIds)->delete();

            DB::commit();

            $this->newLine();
            $this->info("Deleted {$utilities->count()} utility(ies) and {$userUtilities->count()} user_utilities record(s).");
            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
