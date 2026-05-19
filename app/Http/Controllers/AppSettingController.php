<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\StandardResponse;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    public function updateAppVersion(Request $request)
    {
        $request->validate([
            'app_minimum_version'    => 'nullable|string',
            'app_latest_version'     => 'nullable|string',
            'app_last_update_date'   => 'nullable|string',
            'app_size'               => 'nullable|string',
            'app_playstore_url'       => 'nullable|url',
            'app_appstore_url'        => 'nullable|url',
            'app_update_description'  => 'nullable|string',
        ]);

        $fields = [
            'app_minimum_version'    => 'App Minimum Version',
            'app_latest_version'     => 'App Latest Version',
            'app_last_update_date'   => 'App Last Update Date',
            'app_size'               => 'App Size',
            'app_playstore_url'       => 'App Play Store URL',
            'app_appstore_url'        => 'App App Store URL',
            'app_update_description'  => 'App Update Description',
        ];

        foreach ($fields as $key => $title) {
            if ($request->has($key)) {
                AppSetting::set($key, $request->input($key), $title, null, 'system');
            }
        }

        return StandardResponse::success(200, 'App version updated successfully');
    }
}
