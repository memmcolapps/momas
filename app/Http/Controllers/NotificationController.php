<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\ConfigManagementService;
use App\Services\StandardResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function checkAppVersion(Request $request)
    {
        $keys = [
            'description'      => 'app_update_description',
            'minimum_version'  => 'app_minimum_version',
            'latest_version'   => 'app_latest_version',
            'last_update_date' => 'app_last_update_date',
            'app_size'         => 'app_size',
            'play_store_url'   => 'app_playstore_url',
            'app_store_url'    => 'app_appstore_url',
        ];

        $data = [];
        $configService = new ConfigManagementService();
        foreach ($keys as $responseKey => $settingKey) {
            $data[$responseKey] = $configService->getConfig(underscore_to_hyphen($settingKey))
                ?? config("constants.app_update_data.{$settingKey}");
        }

        return StandardResponse::success(200, 'Fetched Version successfully', $data);
    }
}
