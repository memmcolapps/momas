<?php

namespace App\Http\Controllers;

use App\Services\StandardResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function checkAppVersion (Request $request) {
        // Checks app version


        return StandardResponse::success(200, 'Fetched Version successfully', [
            'description' => 'What\'s new in this update',
            'minimum_version' => '1.0.0',
            'current_version' => '1.0.0',
            'last_update_date' => '2025-01-18',
            'app_size' => '60mb',
            'play_store_url' => 'a_random_thing',
            'app_store_url' => 'another_random_thing',
        ]);
    }
}
