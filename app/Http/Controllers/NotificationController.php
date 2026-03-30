<?php

namespace App\Http\Controllers;

use App\Services\StandardResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function checkAppVersion (Request $request) {
        // Checks app version


        return StandardResponse::success(200, 'Fetched Version successfully', [
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'minimum_version' => '1.0.2',
            'latest_version' => '1.1.0',
            'last_update_date' => '2026-03-31',
            'app_size' => '60mb',
            'play_store_url' => 'https://play.google.com/store/apps/details?id=com.memmcol.momaspayplus',
            'app_store_url' => 'https://apps.apple.com/us/app/momaspay-plus/id6743942353',
        ]);
    }
}
