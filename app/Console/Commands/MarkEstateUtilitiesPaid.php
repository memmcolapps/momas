<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\User;
use App\Models\Utility;
use App\Models\UtilitiesPayment;
use Carbon\Carbon;
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

        $duration = $estate->duration ?? null;

        if (!$duration) {
            $this->error("Estate #{$estateId} has no duration configured.");
            return self::FAILURE;
        }

        $this->info("Estate: {$estate->title} (ID: {$estate->id})");

        $userIds = User::where('estate_id', $estateId)->pluck('id');

        if ($userIds->isEmpty()) {
            $this->warn('No users found under this estate.');
            return self::SUCCESS;
        }

        $usersWithPending = UtilitiesPayment::where('type', 'utilities')
            ->whereIn('user_id', $userIds)
            ->where('status', '<>', 2)
            ->pluck('user_id')
            ->unique();

        $allUsersWithPayment = UtilitiesPayment::where('type', 'utilities')
            ->whereIn('user_id', $userIds)
            ->pluck('user_id')
            ->unique();

        $usersWithoutPending = $userIds->reject(fn ($id) => $allUsersWithPayment->contains($id));

        if ($usersWithPending->isEmpty() && $usersWithoutPending->isEmpty()) {
            $this->warn('No action needed. All users already have paid utilities payments.');
            return self::SUCCESS;
        }

        $this->newLine();

        if ($usersWithPending->isNotEmpty()) {
            $this->info("Found {$usersWithPending->count()} user(s) with pending utilities payment(s) to mark as paid.");
        }

        if ($usersWithoutPending->isNotEmpty()) {
            $utilityAmount = Utility::where('estate_id', $estateId)
                ->where('type', 'service_charge')
                ->sum('amount');

            $this->info("Found {$usersWithoutPending->count()} user(s) without any utilities payment — will create one (amount: {$utilityAmount}).");
        }

        if ($this->option('dry-run')) {
            $this->warn('[DRY-RUN] No changes made. Remove --dry-run to execute.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Proceed? This cannot be undone.')) {
            $this->warn('Aborted.');
            return self::SUCCESS;
        }

        try {
            DB::beginTransaction();

            $updated = 0;

            if ($usersWithPending->isNotEmpty()) {
                $updated = UtilitiesPayment::where('type', 'utilities')
                    ->whereIn('user_id', $usersWithPending)
                    ->where('status', '<>', 2)
                    ->update([
                        'status' => 2,
                        'update_source' => 'system',
                    ]);
            }

            $created = 0;

            if ($usersWithoutPending->isNotEmpty()) {
                $utilityAmount = Utility::where('estate_id', $estateId)
                    ->where('type', 'service_charge')
                    ->sum('amount');

                $now = Carbon::now();
                $nextDueDate = $now->copy();
                match ($duration) {
                    'weekly'  => $nextDueDate->addWeek(),
                    'monthly' => $nextDueDate->addMonth(),
                    'yearly'  => $nextDueDate->addYear(),
                };

                foreach ($usersWithoutPending as $userId) {
                    UtilitiesPayment::create([
                        'estate_id'     => $estateId,
                        'user_id'       => $userId,
                        'amount'        => $utilityAmount,
                        'total_amount'  => $utilityAmount,
                        'duration'      => $duration,
                        'next_due_date' => $nextDueDate,
                        'type'          => 'utilities',
                        'created_at'    => $now->startOfMonth(),
                    ]);

                    $created++;
                }

                UtilitiesPayment::where('type', 'utilities')
                    ->whereIn('user_id', $usersWithoutPending)
                    ->where('status', '<>', 2)
                    ->update([
                        'status' => 2,
                        'update_source' => 'system',
                    ]);
            }

            DB::commit();

            $this->newLine();
            $this->info("Done. Marked {$updated} existing and created {$created} new utilities payment(s) as paid.");
            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
