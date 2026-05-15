<?php

namespace App\Http\Controllers;

use App\Services\StandardResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function checkAppVersion (Request $request) {
        // Checks app version


        return StandardResponse::success(200, 'Fetched Version successfully', [
            'description' => config('constants.app_update_data.app_update_description'),
            'minimum_version' => config('constants.app_update_data.app_minimum_version'),
            'latest_version' => config('constants.app_update_data.app_latest_version'),
            'last_update_date' => config('constants.app_update_data.app_last_update_date'),
            'app_size' => config('constants.app_update_data.app_size'),
            'play_store_url' => config('constants.app_update_data.app_playstore_url'),
            'app_store_url' => config('constants.app_update_data.app_appstore_url'),
        ]);
    }
}
