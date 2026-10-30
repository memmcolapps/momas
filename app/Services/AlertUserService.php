<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertUser;
use App\Models\Group;
use App\Models\Meter;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AlertUserService
{
    public function createAlert(Alert $alert, ?Group $group = null): Alert
    {
        if (! $group) {
            return $alert->load('group', 'alertUsers');
        }

        $userIds = $this->resolveRecipients($group);

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

    public function getAlertQuery(array $filters): Builder
    {
        return $this->applyFilters(Alert::query(), $filters);
    }

    public function getAlertList(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->getAlertQuery($filters)
            ->with('group')
            ->withCount(['alertUsers as recipients_count'])
            ->withCount([
                'alertUsers as acknowledged_count' => fn (Builder $query) => $query->whereNotNull('acknowledged_at'),
            ])
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAlertStats(array $filters): array
    {
        $now = now();
        $baseQuery = $this->getAlertQuery($filters);

        $total = (clone $baseQuery)->count();

        $active = (clone $baseQuery)
            ->where(function (Builder $query) use ($now) {
                $query->whereNull('displays_at')
                    ->orWhere('displays_at', '<=', $now);
            })
            ->where(function (Builder $query) use ($now) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', $now);
            })
            ->count();

        $expired = (clone $baseQuery)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->count();

        $totalRecipients = AlertUser::query()
            ->whereIn('alert_id', (clone $baseQuery)->select('alerts.id'))
            ->count();

        $viewed = AlertUser::query()
            ->whereIn('alert_id', (clone $baseQuery)->select('alerts.id'))
            ->whereNotNull('acknowledged_at')
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'viewed' => $viewed,
            'expired' => $expired,
            'total_recipients' => $totalRecipients,
            'view_percentage' => $totalRecipients > 0 ? round(($viewed / $totalRecipients) * 100, 1) : 0,
            'expire_percentage' => $total > 0 ? round(($expired / $total) * 100, 1) : 0,
        ];
    }

    public function getAlertUsers(Alert $alert, int $perPage = 50): LengthAwarePaginator
    {
        return $alert->alertUsers()
            ->with('user')
            ->orderByRaw('CASE WHEN acknowledged_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('acknowledged_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    private function fetchAlerts(int $userId, bool $activeOnly): Collection
    {
        $query = Alert::query()
            ->with('buttons')
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

    private function resolveRecipients(Group $group): array
    {
        if ($group->all_users) {
            return User::query()->pluck('id')->all();
        }

        $userIds = collect();

        $selectedUserIds = $this->integerValues($group->users);

        if ($selectedUserIds !== []) {
            $userIds = $userIds->merge(
                User::query()
                    ->whereIn('id', $selectedUserIds)
                    ->where('role', User::CUSTOMER)
                    ->pluck('id')
            );
        }

        $estateIds = $this->integerValues($group->estates);

        if ($estateIds !== []) {
            $userIds = $userIds->merge(
                User::query()
                    ->whereIn('estate_id', $estateIds)
                    ->where('role', User::CUSTOMER)
                    ->pluck('id')
            );
        }

        $meterNumbers = $this->stringValues($group->meter_numbers);

        if ($meterNumbers !== []) {
            $meterUserIds = Meter::query()
                ->whereIn('meterNo', $meterNumbers)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            if ($meterUserIds->isNotEmpty()) {
                $userIds = $userIds->merge(
                    User::query()
                        ->whereIn('id', $meterUserIds)
                        ->pluck('id')
                );
            }
        }

        return $userIds->unique()->values()->all();
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['title'])) {
            $query->where('title', 'like', '%'.trim($filters['title']).'%');
        }

        $dateFilters = [
            'created_from' => ['created_at', '>='],
            'created_to' => ['created_at', '<='],
            'displays_from' => ['displays_at', '>='],
            'displays_to' => ['displays_at', '<='],
            'expires_from' => ['expires_at', '>='],
            'expires_to' => ['expires_at', '<='],
        ];

        foreach ($dateFilters as $filter => [$column, $operator]) {
            if (! empty($filters[$filter])) {
                $query->whereDate($column, $operator, $filters[$filter]);
            }
        }

        return $query;
    }

    private function integerValues(?array $values): array
    {
        return collect($values ?? [])
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function stringValues(?array $values): array
    {
        return collect($values ?? [])
            ->map(fn ($value) => trim((string) $value))
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
