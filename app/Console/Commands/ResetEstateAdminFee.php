<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\Setting;
use App\Models\UtilitiesPayment;
use Illuminate\Console\Command;

class ResetEstateAdminFee extends Command
{
    protected $signature = 'admin-fee:reset {--dry-run : Preview changes without writing}';

    protected $description = 'Set estate and settings admin fee to 0, then mark all pending admin_fee utility payments as paid';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $prefix = $dryRun ? '[DRY-RUN] ' : '';

        $estates = Estate::count();
        $settings = Setting::count();
        $payments = UtilitiesPayment::where('type', 'admin_fee')
            ->where('status', '<>', 2)
            ->count();

        $this->info("{$prefix}Found {$estates} estate(s), {$settings} setting row(s), {$payments} pending admin_fee payment(s).");

        if ($dryRun) {
            return self::SUCCESS;
        }

        Estate::query()->update(['admin_fee' => 0]);
        $this->line("  Zeroed admin_fee for {$estates} estate(s).");

        Setting::query()->update(['admin_fee' => 0]);
        $this->line("  Zeroed admin_fee for {$settings} setting row(s).");

        UtilitiesPayment::where('type', 'admin_fee')
            ->where('status', '<>', 2)
            ->update(['status' => 2]);
        $this->line("  Marked {$payments} pending admin_fee payment(s) as paid.");

        $this->info('Done.');

        return self::SUCCESS;
    }
}
