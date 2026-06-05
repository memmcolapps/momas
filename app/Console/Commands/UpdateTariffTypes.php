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
    protected $signature = 'tariff:update-types {--revert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tariff type values between nepa/gen and Grid/Off Grid';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $revert = $this->option('revert');

        $this->info(
            $revert
                ? 'Starting tariff type revert...'
                : 'Starting tariff type update...'
        );

        if ($revert) {
            // Grid -> nepa
            $gridCount = Tariff::whereRaw('LOWER(type) = ?', ['grid'])
                ->update(['type' => 'nepa']);

            $this->info("Updated {$gridCount} tariffs from 'Grid' to 'nepa'");

            // Off Grid -> gen
            $offGridCount = Tariff::whereRaw('LOWER(type) = ?', ['off grid'])
                ->update(['type' => 'gen']);

            $this->info("Updated {$offGridCount} tariffs from 'Off Grid' to 'gen'");

            $total = $gridCount + $offGridCount;
        } else {
            // nepa -> Grid
            $nepaCount = Tariff::whereRaw('LOWER(type) LIKE ?', ['%nepa%'])
                ->update(['type' => 'Grid']);

            $this->info("Updated {$nepaCount} tariffs with 'nepa' to 'Grid'");

            // gen -> Off Grid
            $genCount = Tariff::whereRaw('LOWER(type) LIKE ?', ['%gen%'])
                ->update(['type' => 'Off Grid']);

            $this->info("Updated {$genCount} tariffs with 'gen' to 'Off Grid'");

            $total = $nepaCount + $genCount;
        }

        $this->info("Total tariffs updated: {$total}");

        return self::SUCCESS;
    }
}
