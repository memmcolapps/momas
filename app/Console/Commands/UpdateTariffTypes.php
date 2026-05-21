<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tariff;

class UpdateTariffTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tariff:update-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tariff type values: nepa -> Grid, gen -> Off Grid';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting tariff type update...');

        // Update tariffs with type containing 'nepa' (case-insensitive) to 'Grid'
        $nepaCount = Tariff::whereRaw('LOWER(type) LIKE ?', ['%nepa%'])->update(['type' => 'Grid']);
        $this->info("Updated {$nepaCount} tariffs with 'nepa' to 'Grid'");

        // Update tariffs with type containing 'gen' (case-insensitive) to 'Off Grid'
        $genCount = Tariff::whereRaw('LOWER(type) LIKE ?', ['%gen%'])->update(['type' => 'Off Grid']);
        $this->info("Updated {$genCount} tariffs with 'gen' to 'Off Grid'");

        $total = $nepaCount + $genCount;
        $this->info("Total tariffs updated: {$total}");

        return 0;
    }
}
