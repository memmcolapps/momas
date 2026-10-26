<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertUser;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AlertUserService
{
    public function createAlert(Alert $alert, ?Group $group = null): Alert
    {
        if (! $group) {
            return $alert->load('group', 'alertUsers');
        }

        $userIds = $group->all_users
            ? User::query()->pluck('id')->all()
            : User::query()
                ->whereIn('id', $this->groupUserIds($group))
                ->pluck('id')
                ->all();

        $timestamp = now();

        $rows = array_map(fn ($userId) => [
            'alert_id' => $alert->id,
            'user_id' => $userId,
            'acknowledged_at' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ], $userIds);

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 500) as $chunk) {
                AlertUser::query()->insertOrIgnore($chunk);
            }
        });

        return $alert->load('group', 'alertUsers');
    }

    public function getAllAlerts(int $userId): Collection
    {
        return $this->fetchAlerts($userId, false);
    }

    public function getActiveAlerts(int $userId): Collection
    {
        return $this->fetchAlerts($userId, true);
    }

    public function acknowledgeAlert(int $userId, int $alertId): AlertUser
    {
        $alertUser = AlertUser::query()
            ->where('alert_id', $alertId)
            ->where('user_id', $userId)
            ->first();

        if (! $alertUser) {
            throw (new ModelNotFoundException())->setModel(AlertUser::class);
        }

        if ($alertUser->acknowledged_at === null) {
            $alertUser->acknowledged_at = now();
            $alertUser->save();
        }

        return $alertUser;
    }

    private function fetchAlerts(int $userId, bool $activeOnly): Collection
    {
        $query = Alert::query()
            ->join('alert_users', 'alert_users.alert_id', '=', 'alerts.id')
            ->where('alert_users.user_id', $userId)
            ->select('alerts.*', 'alert_users.acknowledged_at')
            ->orderByRaw($this->priorityOrder())
            ->orderByDesc('alerts.created_at');

        if ($activeOnly) {
            $now = now();

            $query->where(function ($query) use ($now) {
                $query->whereNull('alerts.displays_at')
                    ->orWhere('alerts.displays_at', '<=', $now);
            })->where(function ($query) use ($now) {
                $query->whereNull('alerts.expires_at')
                    ->orWhere('alerts.expires_at', '>=', $now);
            });
        }

        $displayAssigned = false;

        return $query->get()->map(function (Alert $alert) use (&$displayAssigned) {
            $display = ! $displayAssigned && $alert->acknowledged_at === null;

            if ($display) {
                $displayAssigned = true;
            }

            $payload = $alert->toArray();
            unset($payload['acknowledged_at']);

            $payload['display'] = $display;

            return $payload;
        });
    }

    private function groupUserIds(Group $group): array
    {
        return collect($group->users ?? [])
            ->map(fn ($userId) => (int) $userId)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function priorityOrder(): string
    {
        $case = 'CASE alerts.priority';

        foreach (Alert::PRIORITY_WEIGHTS as $priority => $weight) {
            $case .= " WHEN '{$priority}' THEN {$weight}";
        }

        return $case.' ELSE 0 END DESC';
    }
}
