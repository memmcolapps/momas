<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\Meter;
use App\Models\Tariff;
use App\Models\TarrifState;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportLegacyEcmiData extends Command
{
    protected $signature = 'legacy:import {--path=} {--dry-run}';

    protected $description = 'Import legacy JSON export into MySQL';

    protected array $estateMap = [];
    protected array $tariffMap = [];
    protected array $meterMap = [];

    /** TariffID_BUID keys whose latest tariff_states.json Status is INVALID */
    protected array $invalidTariffKeys = [];

    /** TariffID_BUID => 'Grid' | 'Off Grid' | 'CONFLICT' */
    protected array $tariffTypeMap = [];

    /** TariffID_BUID keys that got conflicting Grid/Off Grid votes */
    protected array $tariffConflicts = [];

    /** Cache so tariff_states.json is only read from disk once */
    protected ?array $rawTariffStates = null;

    protected array $stats = [
        'estates_matched' => 0,
        'estates_created' => 0,
        'tariffs_created' => 0,
        'tariffs_skipped_invalid' => 0,
        'tariff_states_created' => 0,
        'tariff_states_skipped' => 0,
        'meters' => 0,
        'tariffs_grid' => 0,
        'tariffs_offgrid' => 0,
        'users_info' => 0,
        'users_data' => 0,
    ];

    public function handle(): int
    {
        $path = $this->option('path') ?: storage_path('app/legacy-export');

        $this->info("Importing from: $path");

        DB::beginTransaction();

        try {
            $this->buildInvalidTariffMap($path);
            $this->importEstates($path);
            $this->importTariffs($path);
            $this->importTariffStates($path);
            $this->importMeters($path);
            $this->classifyTariffTypes();
            $this->importUserInfo($path);
            $this->importUserData($path);
            $this->attachMeters();

            DB::commit();

            $this->printSummary();

            $this->info('Import completed successfully');

            return self::SUCCESS;

        } catch (\Throwable $e) {

            DB::rollBack();

            $this->error($e->getMessage());
            $this->error($e->getFile() . ':' . $e->getLine());

            return self::FAILURE;
        }
    }

    protected function read($file, $path)
    {
        return json_decode(file_get_contents($path . '/' . $file));
    }

    protected function isDryRun(): bool
    {
        return $this->option('dry-run');
    }

    protected function tariffKey($tariffId, $buid): string
    {
        return $tariffId . '_' . $buid;
    }

    /* ================= TARIFF STATES (loaded once, reused) ================= */

    protected function loadTariffStates($path): array
    {
        if ($this->rawTariffStates === null) {
            $this->rawTariffStates = $this->read('tariff_states.json', $path);
        }

        return $this->rawTariffStates;
    }

    /**
     * Determine which tariffs are phased out: a tariff is INVALID if its
     * most recent tariff_states.json row has Status = INVALID.
     * "Most recent" = latest EffectiveDate, ties broken by highest VersionNo.
     * Adjust isNewerState() if your data defines "latest" differently.
     */
    protected function buildInvalidTariffMap($path): void
    {
        $rows = $this->loadTariffStates($path);

        $latest = [];

        foreach ($rows as $row) {
            $key = $this->tariffKey($row->TariffID, $row->BUID);

            if (!isset($latest[$key]) || $this->isNewerState($row, $latest[$key])) {
                $latest[$key] = $row;
            }
        }

        foreach ($latest as $key => $row) {
            if (strtoupper(trim($row->Status ?? '')) === 'INVALID') {
                $this->invalidTariffKeys[$key] = true;
            }
        }

        if (!empty($this->invalidTariffKeys)) {
            $this->warn(
                'Skipping ' . count($this->invalidTariffKeys) . ' tariff(s) marked INVALID in their latest state: '
                . implode(', ', array_keys($this->invalidTariffKeys))
            );
        }
    }

    protected function isNewerState($candidate, $current): bool
    {
        $candidateDate = $candidate->EffectiveDate ?? null;
        $currentDate = $current->EffectiveDate ?? null;

        if ($candidateDate && $currentDate) {
            $cmp = strtotime($candidateDate) <=> strtotime($currentDate);
            if ($cmp !== 0) {
                return $cmp > 0;
            }
        } elseif ($candidateDate xor $currentDate) {
            return (bool) $candidateDate;
        }

        return ($candidate->VersionNo ?? 0) > ($current->VersionNo ?? 0);
    }

    /* ================= ESTATES ================= */

    protected function importEstates($path)
    {
        $rows = $this->read('estates.json', $path);

        foreach ($rows as $row) {

            $existing = Estate::where('title', $row->Name)->first();

            if ($existing) {
                $this->estateMap[$row->BUID] = $existing->id;
                $this->stats['estates_matched']++;
                continue;
            }

            $payload = [
                'title' => $row->Name,
                'address' => $row->Address,
                'state' => $row->State,
                'status' => $row->status1 === 'N' ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->stats['estates_created']++;

            if ($this->isDryRun()) {
                $this->preview('ESTATE', $payload);
                $this->estateMap[$row->BUID] = Str::uuid();
                continue;
            }

            $estate = Estate::create($payload);

            $this->estateMap[$row->BUID] = $estate->id;
        }
    }

    /* ================= TARIFFS ================= */

    protected function importTariffs($path)
    {
        $rows = $this->read('tariffs.json', $path);

        foreach ($rows as $row) {

            $key = $this->tariffKey($row->TariffID, $row->BUID);

            if (isset($this->invalidTariffKeys[$key])) {
                $this->stats['tariffs_skipped_invalid']++;
                continue;
            }

            $estateId = $this->estateMap[$row->BUID] ?? null;

            if ($estateId === null) {
                $this->warn("[WARN] Tariff {$key}: no matching estate for BUID {$row->BUID}");
            }

            $payload = [
                'title' => $row->Description,
                'tariff_index' => $row->TariffID,
                'estate_id' => $estateId,
                'status' => $row->status1 === 'N' ? 1 : 0,
                'type' => null, // filled in by classifyTariffTypes() after meters are processed
            ];

            $this->stats['tariffs_created']++;

            if ($this->isDryRun()) {
                $this->preview('Tariff', $payload);
                $this->tariffMap[$key] = random_int(1000, 9999);
                continue;
            }

            $tariff = Tariff::create($payload);

            $this->tariffMap[$key] = $tariff->id;
        }
    }

    /* ================= TARIFF STATES ================= */

    protected function importTariffStates($path)
    {
        $rows = $this->loadTariffStates($path);

        foreach ($rows as $row) {

            $key = $this->tariffKey($row->TariffID, $row->BUID);

            if (isset($this->invalidTariffKeys[$key])) {
                // parent tariff was phased out — don't migrate its rate history either
                $this->stats['tariff_states_skipped']++;
                continue;
            }

            $estateId = $this->estateMap[$row->BUID] ?? null;
            $tariffId = $this->tariffMap[$key] ?? null;

            if (!$tariffId) {
                $this->warn("[WARN] TariffState references unresolved tariff {$key} - skipping");
                $this->stats['tariff_states_skipped']++;
                continue;
            }

            $payload = [
                'amount' => $row->Rate,
                'effective_from' => $row->EffectiveDate ?? now(),
                'vat' => $row->VAT,
                'fixed_charge' => $row->FC,
                'estate_id' => $estateId,
                'tariff_id' => $tariffId,
                't_index' => $row->VersionNo,
                'status' => strtoupper(trim($row->Status ?? '')) === 'ACTIVE' ? 1 : 0,
            ];

            $this->stats['tariff_states_created']++;

            if ($this->isDryRun()) {
                $this->preview('Tariff State', $payload);
                continue;
            }

            TarrifState::create($payload);
        }
    }

    /* ================= METERS ================= */

    protected function importMeters($path)
    {
        $rows = $this->read('meters.json', $path);

        foreach ($rows as $row) {

            $estateId = $this->estateMap[$row->BUID] ?? null;

            $newTariffId = $this->resolveTariffId($row->Tariff, $row->BUID, 'NewTariffID', $row->MeterNo);
            $newTariffDualId = $this->resolveTariffId($row->Tariff2, $row->BUID, 'NewTariffDualID', $row->MeterNo);
            $oldTariffId = $this->resolveTariffId($row->OldTariff, $row->BUID, 'OldTariffID', $row->MeterNo);

            // Classification happens regardless of dry-run, so a dry-run preview
            // reflects real Grid/Off Grid results.
            if ($newTariffId) {
                $this->classifyTariff($this->tariffKey($row->Tariff, $row->BUID), 'Grid');
            }

            if ($row->IsDual && $newTariffDualId) {
                $this->classifyTariff($this->tariffKey($row->Tariff2, $row->BUID), 'Off Grid');
            }

            // OldTariff is intentionally left out of Grid/Off Grid classification.

            $payload = [
                'estate_id' => $estateId,
                'meterNo' => trim($row->MeterNo),
                'status' => 2,
                'meterModel' => $row->Model,
                'AccountNo' => $row->AccountNo,
                'isDualTariff' => $row->IsDual ? '1' : '0',
                'NewSGC' => $row->SGC,
                'OldSGC' => $row->OldSGC,
                'NewTariffID' => $newTariffId,
                'NewTariffDualID' => $newTariffDualId,
                'OldTariffID' => $oldTariffId,
                'NewSGCDual' => $row->SGC2,
                'KRN1' => $row->KRN1,
                'KRN2' => $row->KRN2,
                'NeedKCT' => $row->NeedKCT ? '1' : '0',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->stats['meters']++;

            if ($this->isDryRun()) {
                $this->meterMap[$row->MeterNo] = Str::uuid();
                $this->preview("Meter {$row->MeterNo}", $payload);
                continue;
            }

            $meter = Meter::create($payload);

            $this->meterMap[$row->MeterNo] = $meter->id;
        }
    }

    /**
     * Resolve a raw legacy tariff id (Tariff / Tariff2 / OldTariff) to the
     * migrated tariff's DB id. Returns null if absent or unresolved
     * (commonly because it was an INVALID tariff that got skipped).
     */
    protected function resolveTariffId($tariffRaw, $buid, string $label, string $meterNo): ?int
    {
        if (empty($tariffRaw)) {
            return null;
        }

        $key = $this->tariffKey($tariffRaw, $buid);
        $id = $this->tariffMap[$key] ?? null;

        if ($id === null) {
            $this->warn("[WARN] Meter {$meterNo}: {$label} tariff {$key} not found (invalid/missing) - left null");
        }

        return $id;
    }

    protected function classifyTariff(string $key, string $type): void
    {
        if (!isset($this->tariffTypeMap[$key])) {
            $this->tariffTypeMap[$key] = $type;
            return;
        }

        if ($this->tariffTypeMap[$key] === $type || $this->tariffTypeMap[$key] === 'CONFLICT') {
            return;
        }

        // Same tariff used as Grid on one meter and Off Grid on another.
        $this->tariffTypeMap[$key] = 'CONFLICT';
        $this->tariffConflicts[$key] = true;
    }

    /* ================= TARIFF TYPE CLASSIFICATION (after meters) ================= */

    protected function classifyTariffTypes(): void
    {
        foreach ($this->tariffTypeMap as $key => $type) {

            $tariffId = $this->tariffMap[$key] ?? null;

            if (!$tariffId) {
                continue;
            }

            if ($type === 'CONFLICT') {
                $this->warn("[CONFLICT] Tariff {$key} appears as both Grid and Off Grid on different meters. Left type=null for manual review.");
                continue;
            }

            $this->stats[$type === 'Grid' ? 'tariffs_grid' : 'tariffs_offgrid']++;

            if ($this->isDryRun()) {
                $this->preview("Tariff Type [{$key}]", ['tariff_id' => $tariffId, 'type' => $type]);
                continue;
            }

            Tariff::where('id', $tariffId)->update(['type' => $type]);
        }
    }

    /* ================= USERS ================= */

    protected function importUserInfo($path)
    {
        $rows = $this->read('user_info.json', $path);

        foreach ($rows as $row) {

            $payload = [
                'first_name' => $row->first_name ?? null,
                'last_name' => $row->last_name ?? null,
                'email' => $row->email,
                'password' => $row->password_hash,
                'role' => 3,
                'estate_id' => $this->estateMap[$row->estate_buid] ?? null,
                'estate_name' => $row->estate_name,
                'status' => $row->activated ? 2 : 0,
                'can_login' => $row->can_login,
                'raw_password' => $row->raw_password,
            ];

            $this->stats['users_info']++;

            if ($this->isDryRun()) {
                $this->preview('USER INFO', (array) $payload);
                continue;
            }

            User::create($payload);
        }
    }

    protected function importUserData($path)
    {
        $rows = $this->read('user_data.json', $path);

        foreach ($rows as $row) {

            [$first, $last] = $this->split($row->FullName);

            $payload = [
                'first_name' => $first,
                'last_name' => $last,
                'email' => strtolower($row->OperatorName),
                'phone' => $row->PhoneNumber,
                'meterNo' => $row->MeterNo,
                'role' => 2,
                'password' => Hash::make($row->Pw ?? 'default123'),
            ];

            $this->stats['users_data']++;

            if ($this->isDryRun()) {
                $this->preview('Customer | MOBILE', $payload);
                continue;
            }

            User::create($payload);
        }
    }

    protected function attachMeters()
    {
        $users = User::whereNotNull('meterNo')->get();

        foreach ($users as $user) {

            Meter::where('meterNo', $user->meterNo)
                ->update(['user_id' => $user->id]);
        }
    }

    protected function split($name)
    {
        $parts = explode(' ', trim($name));
        return [$parts[0] ?? null, $parts[1] ?? null];
    }

    protected function preview(string $type, array $data): void
    {
        $this->line('=====================================');
        $this->info("[DRY RUN] {$type}");
        $this->line(json_encode($data, JSON_PRETTY_PRINT));
        $this->line('=====================================');
    }

    protected function printSummary(): void
    {
        $this->newLine();
        $this->info($this->isDryRun() ? 'DRY RUN SUMMARY' : 'IMPORT SUMMARY');

        $this->table(['Metric', 'Count'], [
            ['Estates matched (existing)', $this->stats['estates_matched']],
            ['Estates created', $this->stats['estates_created']],
            ['Tariffs created', $this->stats['tariffs_created']],
            ['Tariffs skipped (INVALID)', $this->stats['tariffs_skipped_invalid']],
            ['Tariff states created', $this->stats['tariff_states_created']],
            ['Tariff states skipped', $this->stats['tariff_states_skipped']],
            ['Meters created', $this->stats['meters']],
            ['Tariffs classified Grid', $this->stats['tariffs_grid']],
            ['Tariffs classified Off Grid', $this->stats['tariffs_offgrid']],
            ['Tariffs left null (conflict)', count($this->tariffConflicts)],
            ['Users (admin/staff) created', $this->stats['users_info']],
            ['Users (mobile/customer) created', $this->stats['users_data']],
        ]);

        if (!empty($this->tariffConflicts)) {
            $this->warn('Conflicted tariffs (manual review needed): ' . implode(', ', array_keys($this->tariffConflicts)));
        }
    }
}
