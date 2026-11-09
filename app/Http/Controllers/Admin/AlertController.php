<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AlertMode;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Estate;
use App\Models\Group;
use App\Models\Meter;
use App\Models\User;
use App\Services\AlertImageService;
use App\Services\AlertUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AlertController extends Controller
{
    public function create()
    {
        $this->ensureSuperAdmin();

        return view('admin.alerts.create', [
            'estates' => Estate::query()->orderBy('title')->get(['id', 'title']),
            'customers' => User::query()
                ->where('role', User::CUSTOMER)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'email', 'phone', 'estate_id']),
            'priorities' => Alert::PRIORITY_WEIGHTS,
            'alertTypes' => Alert::TYPES,
            'modes' => AlertMode::options(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureSuperAdmin();

        $ctaButtons = collect(is_array($request->input('buttons')) ? $request->input('buttons') : [])
            ->filter(fn ($button) => is_array($button) && (filled($button['text'] ?? null) || filled($button['cta_url'] ?? null)))
            ->values()
            ->all();

        $request->merge(['buttons' => $ctaButtons]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(array_keys(Alert::TYPES))],
            'mode' => ['required', Rule::in(array_keys(AlertMode::options()))],
            'text' => [
                'nullable',
                'string',
                Rule::requiredIf(fn () => in_array($request->input('type'), [Alert::TYPE_TEXT_NOTICE, Alert::TYPE_FULL_NOTICE], true)),
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                Rule::when(
                    $request->input('type') !== Alert::TYPE_FULL_NOTICE,
                    ['dimensions:width=720,height=960']
                ),
                Rule::requiredIf(fn () => in_array($request->input('type'), [Alert::TYPE_IMAGE_NOTICE, Alert::TYPE_FULL_NOTICE], true)),
            ],
            'priority' => ['required', Rule::in(array_keys(Alert::PRIORITY_WEIGHTS))],
            'displays_at' => 'required|date',
            'expires_at' => 'nullable|date|after:displays_at',
            'all_users' => 'nullable|boolean',
            'estates' => 'nullable|array',
            'estates.*' => 'integer|exists:estates,id',
            'users' => 'nullable|array',
            'users.*' => [
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::CUSTOMER)),
            ],
            'meter_numbers' => 'nullable|string',
            'buttons' => 'nullable|array',
            'buttons.*.text' => 'required|string|max:255',
            'buttons.*.cta_url' => 'required|string|max:2048',
        ], [
            'image.dimensions' => 'The alert image must be exactly 720 × 960 pixels.',
        ]);

        $allUsers = $request->boolean('all_users');
        $estateIds = $allUsers ? [] : $this->integerValues($validated['estates'] ?? []);
        $userIds = $allUsers ? [] : $this->integerValues($validated['users'] ?? []);
        $meterNumbers = $allUsers ? [] : $this->parseMeterNumbers($validated['meter_numbers'] ?? '');

        if (! $allUsers && empty($estateIds) && empty($userIds) && empty($meterNumbers)) {
            return back()
                ->withInput()
                ->withErrors(['targets' => 'Select at least one estate, customer, or meter number when not targeting all users.']);
        }

        if (! empty($meterNumbers)) {
            $existingMeters = Meter::query()
                ->whereIn('meterNo', $meterNumbers)
                ->pluck('meterNo')
                ->all();

            $missingMeters = array_values(array_diff($meterNumbers, $existingMeters));

            if (! empty($missingMeters)) {
                return back()
                    ->withInput()
                    ->withErrors(['meter_numbers' => 'Meter number(s) not found: '.implode(', ', $missingMeters)]);
            }
        }

        $alert = DB::transaction(function () use ($request, $validated, $allUsers, $estateIds, $userIds, $meterNumbers) {
            $type = $validated['type'];
            $text = in_array($type, [Alert::TYPE_TEXT_NOTICE, Alert::TYPE_FULL_NOTICE], true)
                ? ($validated['text'] ?? null)
                : null;

            $alert = Alert::create([
                'title' => $validated['title'],
                'type' => $type,
                'mode' => $validated['mode'],
                'text' => $text,
                'priority' => $validated['priority'],
                'displays_at' => $validated['displays_at'],
                'expires_at' => $validated['expires_at'] ?? null,
            ]);

            if (in_array($type, [Alert::TYPE_IMAGE_NOTICE, Alert::TYPE_FULL_NOTICE], true) && $request->hasFile('image')) {
                (new AlertImageService())->upload($request->file('image'), $alert, [
                    'width' => 720,
                    'height' => 960,
                    'fit' => 'CONTAIN',
                ]);
            }

            if (! empty($validated['buttons'])) {
                $alert->buttons()->createMany($validated['buttons']);
            }

            $group = Group::create([
                'alert_id' => $alert->id,
                'all_users' => $allUsers,
                'estates' => empty($estateIds) ? null : $estateIds,
                'users' => empty($userIds) ? null : $userIds,
                'meter_numbers' => empty($meterNumbers) ? null : $meterNumbers,
            ]);

            (new AlertUserService())->createAlert($alert, $group);

            return $alert;
        });

        return redirect()
            ->route('alerts.show', ['id' => $alert->id])
            ->with('message', 'Alert created successfully and recipients have been assigned.');
    }

    public function stats(Request $request)
    {
        $this->ensureSuperAdmin();

        $filters = $this->filters($request);
        $stats = (new AlertUserService())->getAlertStats($filters);

        return view('admin.alerts.stats', compact('stats', 'filters'));
    }

    public function index(Request $request)
    {
        $this->ensureSuperAdmin();

        $filters = $this->filters($request);
        $alerts = (new AlertUserService())->getAlertList($filters);

        return view('admin.alerts.index', compact('alerts', 'filters'));
    }

    public function show(Request $request)
    {
        $this->ensureSuperAdmin();

        $request->validate([
            'id' => 'required|integer|exists:alerts,id',
        ]);

        $alert = Alert::with('group', 'buttons', 'alertImage')->findOrFail($request->input('id'));
        $group = $alert->group;

        $estateNames = $group
            ? Estate::query()
                ->whereIn('id', $group->estates ?? [])
                ->orderBy('title')
                ->pluck('title')
            : collect();

        $selectedCustomers = $group
            ? User::query()
                ->whereIn('id', $group->users ?? [])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(['id', 'first_name', 'last_name', 'email', 'phone'])
            : collect();

        $recipients = (new AlertUserService())->getAlertUsers($alert);
        $totalRecipients = $alert->alertUsers()->count();
        $acknowledged = $alert->alertUsers()->whereNotNull('acknowledged_at')->count();
        $unacknowledged = $totalRecipients - $acknowledged;
        $acknowledgedPercentage = $totalRecipients > 0
            ? round(($acknowledged / $totalRecipients) * 100, 1)
            : 0;

        return view('admin.alerts.view', compact(
            'alert',
            'group',
            'estateNames',
            'selectedCustomers',
            'recipients',
            'totalRecipients',
            'acknowledged',
            'unacknowledged',
            'acknowledgedPercentage'
        ));
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->isSuperAdmin(),
            403,
            'Only super admins can manage alerts.'
        );
    }

    private function filters(Request $request): array
    {
        return $request->only([
            'title',
            'created_from',
            'created_to',
            'displays_from',
            'displays_to',
            'expires_from',
            'expires_to',
        ]);
    }

    private function integerValues(array $values): array
    {
        return collect($values)
            ->map(fn ($value) => (int) $value)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function parseMeterNumbers(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($meterNumber) => trim($meterNumber))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
