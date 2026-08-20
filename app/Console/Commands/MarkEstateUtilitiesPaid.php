<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\User;
use App\Models\UtilitiesPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkEstateUtilitiesPaid extends Command
{
    protected $signature = 'estate:mark-paid {estate_id} {--dry-run : Preview changes without updating}';

    protected $description = 'Set all utilities payments for users under an estate to paid';

    public function handle(): int
    {
        $estateId = $this->argument('estate_id');

        $estate = Estate::find($estateId);

        if (!$estate) {
            $this->error("Estate #{$estateId} not found.");
            return self::FAILURE;
        }

        $this->info("Estate: {$estate->title} (ID: {$estate->id})");

        $userIds = User::where('estate_id', $estateId)->pluck('id');

        if ($userIds->isEmpty()) {
            $this->warn('No users found under this estate.');
            return self::SUCCESS;
        }

        $unpaidPayments = UtilitiesPayment::whereIn('user_id', $userIds)
            ->where('status', '<>', 2)
            ->get();

        if ($unpaidPayments->isEmpty()) {
            $this->warn('No unpaid utilities payments found for users under this estate.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("Found {$unpaidPayments->count()} unpaid utilities payment(s) across {$userIds->count()} user(s).");

        if ($this->option('dry-run')) {
            $this->warn('[DRY-RUN] No changes made. Remove --dry-run to execute.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Proceed with marking all as paid? This cannot be undone.')) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        try {
            DB::beginTransaction();

            $updated = UtilitiesPayment::whereIn('user_id', $userIds)
                ->where('status', '<>', 2)
                ->update([
                    'status' => 2,
                    'update_source' => 'system',
                ]);

            DB::commit();

            $this->newLine();
            $this->info("Marked {$updated} utilities payment(s) as paid.");
            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
