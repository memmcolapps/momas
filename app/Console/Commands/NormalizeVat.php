<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TarrifState;
use App\Models\Estate;

class NormalizeVat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vat:normalize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set all non-zero VAT values in tarrif_states and estates to 7.5';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting VAT normalization...');

        $tariffCount = TarrifState::where('vat', '!=', 0)
            ->update(['vat' => 7.5]);

        $this->info("Updated {$tariffCount} tarrif_states rows (vat: non-zero -> 7.5)");

        $estateCount = Estate::where('estate_vat', '!=', 0)
            ->update(['estate_vat' => 7.5]);

        $this->info("Updated {$estateCount} estates rows (estate_vat: non-zero -> 7.5)");

        $this->info('VAT normalization complete.');

        return self::SUCCESS;
    }
}
