<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\Meter;
use App\Models\Transformer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixEstateMeterTransformer extends Command
{
    protected $signature = 'meter:fix-estate-transformer {--dry-run : Preview changes without writing} {--all : Run against all estates instead of only estates with a legacy_buid}';

    protected $description = 'Reassign meters whose transformer does not belong to their estate to the most recent estate transformer';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $estates = Estate::query();
        if (!$this->option('all')) {
            $estates->whereNotNull('legacy_buid')->where('legacy_buid', '!=', '');
        }
        $estates = $estates->get();

        if ($estates->isEmpty()) {
            $this->warn('No estates found to process.');
            return self::SUCCESS;
        }

        $this->info(
            ($dryRun ? '[DRY-RUN] ' : '') . "Processing {$estates->count()} estate(s)..."
        );

        $totalMeters = 0;
        $totalReassigned = 0;
        $totalCleared = 0;

        foreach ($estates as $estate) {
            $mostRecent = Transformer::where('Estate_id', $estate->id)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first();

            $meters = Meter::where('estate_id', $estate->id)
                ->whereNotNull('TransformerID')
                ->where(function ($query) use ($estate) {
                    $query->whereNotExists(function ($sub) use ($estate) {
                        $sub->select(DB::raw(1))
                            ->from('transformers')
                            ->whereColumn('transformers.id', 'meters.TransformerID')
                            ->where('transformers.Estate_id', $estate->id);
                    });
                })
                ->get();

            if ($meters->isEmpty()) {
                continue;
            }

            $this->line("  {$estate->id} [{$estate->legacy_buid}] {$estate->title}: {$meters->count()} meter(s)");

            foreach ($meters as $meter) {
                $replacement = $mostRecent ? $mostRecent->id : null;
                $action = $replacement ? "transformer {$meter->TransformerID} -> {$replacement}" : "transformer {$meter->TransformerID} -> null";

                $this->line("    meter {$meter->id} [{$meter->meterNo}]: {$action}");

                if (!$dryRun) {
                    $meter->update(['TransformerID' => $replacement]);
                }

                $totalMeters++;
                $replacement ? $totalReassigned++ : $totalCleared++;
            }
        }

        $this->newLine();
        $this->info(
            "Done: {$totalMeters} meter(s) processed, {$totalReassigned} reassigned, {$totalCleared} cleared."
        );

        return self::SUCCESS;
    }
}
