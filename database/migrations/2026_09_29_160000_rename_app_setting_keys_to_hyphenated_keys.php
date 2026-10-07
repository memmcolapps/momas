<?php

use App\Models\AppSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Settings seeded and written with underscored keys before the spelling was
     * unified on the hyphenated form that ConfigManagementService reads with.
     *
     * @var array<string, string>
     */
    private const KEYS = [
        'app_minimum_version' => 'app-minimum-version',
        'app_latest_version' => 'app-latest-version',
        'app_last_update_date' => 'app-last-update-date',
        'app_size' => 'app-size',
        'app_playstore_url' => 'app-playstore-url',
        'app_appstore_url' => 'app-appstore-url',
        'app_update_description' => 'app-update-description',
        'momas_max_emergency_token' => 'momas-max-emergency-token',
    ];

    public function up(): void
    {
        foreach (self::KEYS as $legacy => $current) {
            $this->renameKey($legacy, $current);
        }

        AppSetting::clearCache();
    }

    public function down(): void
    {
        // Not reversed on purpose. A guarded reverse cannot tell a row this
        // migration renamed from one that was already correct, so rolling back
        // would move correct keys into the spelling no reader resolves.
    }

    private function renameKey(string $from, string $to): void
    {
        if (! AppSetting::where('key', $from)->whereNull('estate_id')->exists()) {
            return;
        }

        // A row on the current key is the one the readers already resolve, so
        // it wins. The orphan is left in place rather than deleted.
        if (AppSetting::where('key', $to)->whereNull('estate_id')->exists()) {
            return;
        }

        AppSetting::where('key', $from)->whereNull('estate_id')->update(['key' => $to]);
    }
};
