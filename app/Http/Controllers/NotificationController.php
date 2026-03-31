<?php

namespace App\Http\Controllers;

use App\Services\StandardResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function checkAppVersion (Request $request) {
        // Checks app version


        return StandardResponse::success(200, 'Fetched Version successfully', [
            'description' => "Experience a faster, more reliable app. We've improved our payment system, redesigned the interface for smooth navigation, and added smart loading indicators so you're never left waiting. Jump in and get going.",
            'minimum_version' => '1.0.2',
            'latest_version' => '1.1.0',
            'last_update_date' => '2026-04-02',
            'app_size' => '26.1mb',
            'play_store_url' => 'https://play.google.com/store/apps/details?id=com.memmcol.momaspayplus',
            'app_store_url' => 'https://apps.apple.com/us/app/momaspay-plus/id6743942353',
        ]);
    }
}
