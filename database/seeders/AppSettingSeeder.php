<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'title' => 'App Minimum Version',
                'key'   => 'app_minimum_version',
                'value' => config('constants.app_update_data.app_minimum_version'),
            ],
            [
                'title' => 'App Latest Version',
                'key'   => 'app_latest_version',
                'value' => config('constants.app_update_data.app_latest_version'),
            ],
            [
                'title' => 'App Last Update Date',
                'key'   => 'app_last_update_date',
                'value' => config('constants.app_update_data.app_last_update_date'),
            ],
            [
                'title' => 'App Size',
                'key'   => 'app_size',
                'value' => config('constants.app_update_data.app_size'),
            ],
            [
                'title' => 'App Play Store URL',
                'key'   => 'app_playstore_url',
                'value' => config('constants.app_update_data.app_playstore_url'),
            ],
            [
                'title' => 'App App Store URL',
                'key'   => 'app_appstore_url',
                'value' => config('constants.app_update_data.app_appstore_url'),
            ],
            [
                'title' => 'App Update Description',
                'key'   => 'app_update_description',
                'value' => config('constants.app_update_data.app_update_description'),
            ],
        ];

        foreach ($settings as $setting) {
            AppSetting::set(
                $setting['key'],
                $setting['value'],
                $setting['title'],
                null,
                'system'
            );
        }
    }
}
