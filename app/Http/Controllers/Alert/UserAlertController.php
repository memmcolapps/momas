<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Services\AlertUserService;
use App\Services\StandardResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = (new AlertUserService())->getAllAlerts((int) Auth::id());

        return StandardResponse::success(200, 'Alerts fetched successfully', $alerts);
    }

    public function active(Request $request)
    {
        $alerts = (new AlertUserService())->getActiveAlerts((int) Auth::id());

        return StandardResponse::success(200, 'Active alerts fetched successfully', $alerts);
    }

    public function acknowledge(int $alert)
    {
        try {
            $alertUser = (new AlertUserService())->acknowledgeAlert((int) Auth::id(), $alert);
        } catch (ModelNotFoundException $e) {
            return StandardResponse::error(404, 'Alert not found');
        }

        return StandardResponse::success(200, 'Alert acknowledged successfully', $alertUser);
    }
}
