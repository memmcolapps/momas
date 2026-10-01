<?php

namespace App\Http\Controllers;

use App\Models\Logger;
use App\Models\Meter;
use App\Models\Tariff;
use App\Services\ConfigManagementService;
use App\Services\EmergencyTokenService;
use App\Services\StandardResponse;
use App\Services\UtilityManagementService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class EmergencyTokenController extends Controller
{
    /**
     * Report whether the authenticated customer may request an emergency token.
     *
     * The service refuses to vend an emergency token while any debt-type
     * utility is still owed, so a customer is only eligible when that total
     * is zero. The same calculation the service performs is used here to
     * keep the check and the generation in agreement.
     *
     * The response also carries the meter, the tariffs assigned to it and the
     * token value breakdown for each tariff at the maximum emergency amount so
     * the client can render the request screen without a second round trip.
     */
    public function checkEligibility(Request $request)
    {
        $auth_user = Auth::user();

        if (! $auth_user->estate_id) {
            return StandardResponse::error(422, 'User not attached to any estate');
        }

        $meter = $this->resolveMeter($auth_user);

        if (! $meter) {
            return StandardResponse::error(404, 'Meter not found');
        }

        try {
            $total_debt = round((float) (new UtilityManagementService())
                ->calculateUserOwedUtility((int) $auth_user->id, (int) $auth_user->estate_id)['total_owed'], 2);
        } catch (Throwable $e) {
            Logger::error('EmergencyTokenController failed to check eligibility', [
                'user_id' => $auth_user->id,
                'error' => $e->getMessage(),
            ]);

            return StandardResponse::error(500, 'An error occurred');
        }

        return StandardResponse::success(200, 'Emergency token eligibility retrieved', [
            'can_get_emergency_token' => $total_debt <= 0,
            'total_debt' => $total_debt,
            'max_amount' => $this->maxAmount(),
            'meter' => [
                'meterNo' => $meter->meterNo,
                'estate_id' => $meter->estate_id,
                'is_active' => $meter->isActive(),
            ],
            'tariffs' => $this->tariffsFor($meter),
        ]);
    }

    /**
     * Generate an emergency token for the authenticated customer's own meter.
     */
    public function generateEmergencyToken(Request $request)
    {
        $auth_user = Auth::user();

        if (! $auth_user->estate_id) {
            return StandardResponse::error(422, 'User not attached to any estate');
        }

        $maxAmount = $this->maxAmount();

        $validator = Validator::make($request->all(), [
            'tariff_id' => [
                'required',
                'integer',
                Rule::exists('tariffs', 'id')->where('estate_id', $auth_user->estate_id),
            ],
            'amount' => 'required|numeric|min:1|max:' . $maxAmount,
        ]);

        if ($validator->fails()) {
            return StandardResponse::error(422, 'Validation Error', [
                'validation_error' => $validator->errors(),
            ]);
        }

        $meter = $this->resolveMeter($auth_user);

        if (! $meter) {
            return StandardResponse::error(404, 'Meter not found');
        }

        if (! $meter->isActive()) {
            return StandardResponse::error(403, 'Meter unable to perform this action reach out to your estate admin for support', []);
        }

        try {
            $result = EmergencyTokenService::generate(
                $meter,
                (int) $request->tariff_id,
                (int) $request->amount
            );
        } catch (Exception $e) {
            return StandardResponse::error(422, $e->getMessage());
        }

        return StandardResponse::success(200, 'Emergency token generated successfully', $result);
    }

    /**
     * Locate the authenticated customer's own meter within their estate.
     */
    private function resolveMeter($auth_user): ?Meter
    {
        return Meter::where('user_id', $auth_user->id)
            ->where('estate_id', $auth_user->estate_id)
            ->first();
    }

    /**
     * List the tariffs assigned to the meter, each with the token values it
     * yields at the maximum emergency amount.
     *
     * A tariff whose values cannot be calculated at that amount (misconfigured
     * tariff state, units below the 0.1kWh floor) is still listed, just without
     * a breakdown, so one bad tariff cannot fail the whole eligibility check.
     */
    private function tariffsFor(Meter $meter): array
    {
        $assignedIds = array_filter([
            $meter->NewTariffID,
            $meter->OldTariffID,
            $meter->NewTariffDual,
            $meter->OldTariffDual,
            $meter->NewTariffDualID,
            $meter->OldTariffDualID,
        ]);

        if (empty($assignedIds)) {
            return [];
        }

        $maxAmount = $this->maxAmount();

        return Tariff::where('estate_id', $meter->estate_id)
            ->whereIn('id', $assignedIds)
            ->select('id', 'title', 'type', 'tariff_index')
            ->get()
            ->map(function (Tariff $tariff) use ($meter, $maxAmount) {
                try {
                    $breakdown = $meter->calculateTokenValuesByAmount($tariff->id, $maxAmount);
                } catch (Throwable $e) {
                    Logger::warning('Emergency token breakdown unavailable for tariff', [
                        'meterNo' => $meter->meterNo,
                        'tariff_id' => $tariff->id,
                        'amount' => $maxAmount,
                        'error' => $e->getMessage(),
                    ]);

                    $breakdown = null;
                }

                return [
                    'id' => $tariff->id,
                    'title' => $tariff->title,
                    'type' => $tariff->type,
                    'tariff_index' => $tariff->tariff_index,
                    'breakdown' => $breakdown,
                ];
            })
            ->all();
    }

    private function maxAmount(): int
    {
        return (int) (app(ConfigManagementService::class)
            ->getConfig('momas-max-emergency-token') ?? config('constants.momas_max_emergency_token'));
    }
}
