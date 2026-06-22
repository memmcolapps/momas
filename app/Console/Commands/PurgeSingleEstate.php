<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurgeSingleEstate extends Command
{
    protected $signature = 'estate:purge {estate_id} {--force}';

    protected $description = 'Safely delete ONE estate only if it was created within the last month';

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

        $cutoff = Carbon::now()->subMonth();

        if (Carbon::parse($estate->created_at)->lt($cutoff)) {
            $this->error("Blocked: Estate is older than 1 month. Cannot delete.");
            return self::FAILURE;
        }

        $this->warn("You are about to delete estate:");
        $this->line("ID: {$estate->id}");
        $this->line("Name: {$estate->title}");
        $this->line("Created: {$estate->created_at}");

        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to continue?")) {
                return self::SUCCESS;
            }
        }

        DB::beginTransaction();

        try {

            // DELETE CHILD DATA FIRST (important)

            DB::table('transactions')->where('estate_id', $estateId)->delete();
            DB::table('meters')->where('estate_id', $estateId)->delete();
            DB::table('tarrif_states')->where('estate_id', $estateId)->delete();
            DB::table('tariffs')->where('estate_id', $estateId)->delete();
            DB::table('transformers')->where('Estate_id', $estateId)->delete();
            DB::table('users')->where('estate_id', $estateId)->delete();

            // DELETE ESTATE LAST
            DB::table('estates')->where('id', $estateId)->delete();

            DB::commit();

            $this->info("Estate {$estateId} deleted successfully.");

            return self::SUCCESS;

        } catch (\Throwable $e) {

            DB::rollBack();

            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
