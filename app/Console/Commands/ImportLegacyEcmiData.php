<?php

namespace App\Console\Commands;

use App\Models\CreditToken;
use App\Models\Estate;
use App\Models\Meter;
use App\Models\Tariff;
use App\Models\TarrifState;
use App\Models\Transformer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportLegacyEcmiData extends Command
{
    protected $signature = 'legacy:import
                            {--path=           : Path to legacy export folder}
                            {--dry-run         : Preview changes without writing to DB}
                            {--transactions=5  : Max recent transactions to import per meter}
                            {--module=         : Run a single module only (estate|transformer|tariff|meter|user_info|user_data|customer|transaction|subaccount|attach_meters)}
                            {--reset           : Delete saved migration state and start fresh}';

    protected $description = 'Import legacy ECMI JSON export into MySQL (resumable, module-by-module)';

    // -----------------------------------------------------------------------
    // Module registry – order matters; dependencies must appear before dependants
    // -----------------------------------------------------------------------
    protected const MODULES = [
        'estate',
        'transformer',
        'tariff',
        'meter',
        'user_info',
        'user_data',
        'customer',
        'utility-subaccount',
        'transaction',
    ];

    protected const MODULE_DEPS = [
        'transformer' => ['estate'],
        'tariff'      => ['estate'],
        'meter'       => ['estate', 'tariff'],
        'user_info'   => ['estate'],
        'customer'    => ['estate', 'tariff', 'meter'],
        'transaction' => ['meter', 'user_data'],
        'utility-subaccount' => ['customer'],
        // estate, user_data have no deps
    ];

    // -----------------------------------------------------------------------
    // In-memory maps (re-hydrated from state file at boot)
    // -----------------------------------------------------------------------
    protected array $estateMap      = [];
    protected array $tariffMap      = [];
    protected array $meterMap       = [];
    protected array $transformerMap = [];

    protected array $invalidTariffKeys = [];
    protected array $tariffTypeMap     = [];
    protected array $tariffConflicts   = [];

    protected ?array $rawTariffStates = null;

    protected array $stats = [
        'estates_matched'         => 0,
        'estates_created'         => 0,
        'tariffs_created'         => 0,
        'tariffs_skipped_invalid' => 0,
        'tariff_states_created'   => 0,
        'tariff_states_skipped'   => 0,
        'meters'                  => 0,
        'tariffs_grid'            => 0,
        'tariffs_offgrid'         => 0,
        'users_info'              => 0,
        'users_data'              => 0,
        'transactions_data'       => 0,
        'transformers'            => 0,
        'customers'               => 0,
    ];

    /** Modules that have been committed successfully (persisted in state file) */
    protected array $completedModules = [];

    // -----------------------------------------------------------------------
    // Entry point
    // -----------------------------------------------------------------------

    public function handle(): int
    {
        $path = $this->option('path') ?: storage_path('app/legacy-export');

        $this->info("Migration path: $path");

        // --reset: wipe saved state and start over
        if ($this->option('reset')) {
            $this->deleteStateFile($path);
            $this->info('State file cleared. Starting fresh.');
        }

        // Load any previously persisted state
        $this->loadState($path);

        // Decide which modules to run
        $targetModule = $this->option('module');

        if ($targetModule !== null) {

            if ($targetModule === 'subaccount' || $targetModule === 'attach_meters' || $targetModule === 'utility-subaccount') {
                $this->info("▶ Running standalone module: {$targetModule}");
                try {
                    match ($targetModule) {
                        'subaccount'         => $this->importSubAccounts($path),
                        'attach_meters'      => $this->attachMeters(),
                        'utility-subaccount' => $this->importUtilitySubAccounts($path),
                    };
                    $this->info("✔ Module [{$targetModule}] complete.");
                } catch (\Throwable $e) {
                    $this->error("✘ Module [{$targetModule}] failed: " . $e->getMessage());
                    $this->error($e->getFile() . ':' . $e->getLine());
                    return self::FAILURE;
                }
                return self::SUCCESS;
            }
            if (!in_array($targetModule, self::MODULES, true)) {
                $this->error("Unknown module: {$targetModule}. Valid: " . implode(', ', self::MODULES));
                return self::FAILURE;
            }
            $modulesToRun = [$targetModule];
        } else {
            // Run every module not yet completed, in dependency order
            $modulesToRun = array_values(
                array_filter(self::MODULES, fn ($m) => !in_array($m, $this->completedModules, true))
            );

            if (empty($modulesToRun)) {
                $this->info('All modules already completed. Use --reset to start over.');
                $this->printSummary();
                return self::SUCCESS;
            }
        }

        $this->info('Modules to run: ' . implode(' → ', $modulesToRun));

        // Run each module in its own transaction so a failure only rolls back
        // the current module, leaving previously committed modules intact.
        foreach ($modulesToRun as $module) {
            $this->info('');
            $this->info("▶ Running module: {$module}");

            DB::beginTransaction();

            try {
                $this->runModule($module, $path);

                if (!$this->isDryRun()) {
                    DB::commit();
                    // Persist maps + mark this module done
                    $this->completedModules[] = $module;
                    $this->saveState($path);
                    $this->info("✔ Module [{$module}] committed and state saved.");
                } else {
                    DB::rollBack();
                    $this->info("✔ Module [{$module}] dry-run complete (no DB writes).");
                }

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("✘ Module [{$module}] failed: " . $e->getMessage());
                $this->error($e->getFile() . ':' . $e->getLine());
                $this->warn('State from previous modules is preserved. Fix the issue and re-run.');
                return self::FAILURE;
            }
        }

        // All modules done — clean up state file (unless dry-run)
        if (!$this->isDryRun() && empty(array_diff(self::MODULES, $this->completedModules))) {
            $this->deleteStateFile($path);
            $this->info('');
            $this->info('All modules complete. State file removed.');
        }

        $this->printSummary();
        $this->info('Import finished successfully.');

        return self::SUCCESS;
    }

    // -----------------------------------------------------------------------
    // Module dispatcher
    // -----------------------------------------------------------------------

    protected function runModule(string $module, string $path): void
    {
        $deps = self::MODULE_DEPS[$module] ?? [];
        $missing = array_filter($deps, fn($d) => !in_array($d, $this->completedModules, true));

        if (!empty($missing)) {
            throw new \RuntimeException(
                "Module [{$module}] requires [" . implode(', ', $missing) . "] to be completed first."
            );
        }

        match ($module) {
            'estate'      => $this->importEstates($path),
            'transformer' => $this->importTransformers($path),
            'tariff'      => $this->runTariffModule($path),
            'meter'       => $this->runMeterModule($path),
            'customer'    => $this->importCustomers($path),
            'user_info'   => $this->importUserInfo($path),
            'user_data'   => $this->importUserData($path),
            'transaction' => $this->runTransactionModule($path),
            'subaccount' => $this->importSubAccounts($path),
            'utility-subaccount' => $this->importUtilitySubAccounts($path),
        };
    }

    /** Tariff module: build invalid map → import tariffs → import tariff states → classify types */
    protected function runTariffModule(string $path): void
    {
        $this->buildInvalidTariffMap($path);
        $this->importTariffs($path);
        $this->importTariffStates($path);
    }

    /** Meter module: import meters → classify tariff types (depends on meter data) */
    protected function runMeterModule(string $path): void
    {
        $this->importMeters($path);
        $this->classifyTariffTypes();
    }

    /**
     * Transaction module: attachMeters() must run first so meter.user_id is
     * populated before we try to create transaction rows.
     */
    protected function runTransactionModule(string $path): void
    {
        if (!$this->isDryRun()) {
            $this->attachMeters();
        }
        $this->importTransactions($path);
    }

    // -----------------------------------------------------------------------
    // State persistence
    // -----------------------------------------------------------------------

    protected function stateFilePath(string $path): string
    {
        return rtrim($path, '/') . '/migration_state.json';
    }

    protected function saveState(string $path): void
    {
        $state = [
            'saved_at'          => now()->toIso8601String(),
            'completed_modules' => $this->completedModules,
            'stats'             => $this->stats,
            'maps'              => [
                'estateMap'         => $this->estateMap,
                'tariffMap'         => $this->tariffMap,
                'meterMap'          => $this->meterMap,
                'transformerMap'    => $this->transformerMap,
                'invalidTariffKeys' => $this->invalidTariffKeys,
                'tariffTypeMap'     => $this->tariffTypeMap,
                'tariffConflicts'   => $this->tariffConflicts,
            ],
        ];

        file_put_contents(
            $this->stateFilePath($path),
            json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    protected function loadState(string $path): void
    {
        $file = $this->stateFilePath($path);

        if (!file_exists($file)) {
            return;
        }

        $state = json_decode(file_get_contents($file), true);

        if (!$state) {
            $this->warn('State file found but could not be parsed — ignoring.');
            return;
        }

        $this->completedModules = $state['completed_modules'] ?? [];
        $this->stats            = array_merge($this->stats, $state['stats'] ?? []);

        $maps = $state['maps'] ?? [];
        $this->estateMap         = $maps['estateMap']         ?? [];
        $this->tariffMap         = $maps['tariffMap']         ?? [];
        $this->meterMap          = $maps['meterMap']          ?? [];
        $this->transformerMap    = $maps['transformerMap']    ?? [];
        $this->invalidTariffKeys = $maps['invalidTariffKeys'] ?? [];
        $this->tariffTypeMap     = $maps['tariffTypeMap']     ?? [];
        $this->tariffConflicts   = $maps['tariffConflicts']   ?? [];

        if (!empty($this->completedModules)) {
            $this->info('Resuming from state file. Completed: ' . implode(', ', $this->completedModules));
        }
    }

    protected function deleteStateFile(string $path): void
    {
        $file = $this->stateFilePath($path);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    protected function read(string $file, string $path): array
    {
        $full = rtrim($path, '/') . '/' . $file;

        if (!file_exists($full)) {
            throw new \RuntimeException("Required export file not found: {$full}");
        }

        return json_decode(file_get_contents($full), false, 512, JSON_THROW_ON_ERROR);
    }

    protected function isDryRun(): bool
    {
        return (bool) $this->option('dry-run');
    }

    protected function tariffKey($tariffId, $buid): string
    {
        return $tariffId . '_' . $buid;
    }

    // -----------------------------------------------------------------------
    // Tariff states (loaded once, cached in memory)
    // -----------------------------------------------------------------------

    protected function loadTariffStates(string $path): array
    {
        if ($this->rawTariffStates === null) {
            $this->rawTariffStates = $this->read('tariff_states.json', $path);
        }

        return $this->rawTariffStates;
    }

    /**
     * Build invalidTariffKeys: a tariff is phased-out when its most recent
     * tariff_states row (latest EffectiveDate, ties broken by highest VersionNo)
     * has Status = INVALID.
     */
    protected function buildInvalidTariffMap(string $path): void
    {
        // Skip re-building if already loaded from persisted state
        if (!empty($this->invalidTariffKeys)) {
            $this->info('Invalid tariff map already loaded from state — skipping rebuild.');
            return;
        }

        $rows   = $this->loadTariffStates($path);
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
                'Skipping ' . count($this->invalidTariffKeys) .
                ' tariff(s) marked INVALID in their latest state: ' .
                implode(', ', array_keys($this->invalidTariffKeys))
            );
        }
    }

    protected function isNewerState($candidate, $current): bool
    {
        $candidateDate = $candidate->EffectiveDate ?? null;
        $currentDate   = $current->EffectiveDate   ?? null;

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

    // -----------------------------------------------------------------------
    // ESTATES
    // -----------------------------------------------------------------------

    protected function importEstates(string $path): void
    {
        $rows = $this->read('estates.json', $path);

        $subAccounts = collect($this->read('paystack_subaccounts.json', $path))
            ->keyBy('BUID');

        foreach ($rows as $row) {
            if (isset($this->estateMap[$row->BUID])) {
                continue;
            }

            $subAcct = $subAccounts[$row->BUID] ?? null;

            $existing = Estate::where('title', $row->Name)->first();

            if ($existing) {
                $this->estateMap[$row->BUID] = $existing->id;
                $this->stats['estates_matched']++;

                // Backfill subaccount + legacy_buid onto existing estate if missing
                if (!$this->isDryRun()) {
                    $updates = [];

                    if (!$existing->legacy_buid) {
                        $updates['legacy_buid'] = $row->BUID;
                    }

                    if ($subAcct && !$existing->paystack_subaccount) {
                        $updates['paystack_subaccount'] = $subAcct->SubAcctID;
                        $updates['account_no']          = $subAcct->BankAccountNo;
                        $updates['account_name']        = $subAcct->BankAccountName;
                        $updates['bank']                = $subAcct->BankName;
                    }

                    if (!empty($updates)) {
                        $existing->update($updates);
                    }
                }

                continue;
            }

            $payload = [
                'title'               => $row->Name,
                'address'             => $row->Address,
                'state'               => $row->State,
                'legacy_buid'         => $row->BUID,
                'status'              => 2,
                'paystack_subaccount' => $subAcct?->SubAcctID       ?? null,
                'account_no'          => $subAcct?->BankAccountNo   ?? null,
                'account_name'        => $subAcct?->BankAccountName ?? null,
                'bank'                => $subAcct?->BankName        ?? null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $this->stats['estates_created']++;

            if ($this->isDryRun()) {
                $this->preview('ESTATE', $payload);
                $this->estateMap[$row->BUID] = 'dry_' . Str::uuid();
                continue;
            }

            $estate = Estate::create($payload);
            $this->estateMap[$row->BUID] = $estate->id;
        }
    }

    // -----------------------------------------------------------------------
    // TRANSFORMERS
    // -----------------------------------------------------------------------

    protected function importTransformers(string $path): void
    {
        $rows = $this->read('transformers.json', $path);

        foreach ($rows as $row) {
            if (isset($this->transformerMap[$row->TransID])) {
                continue;
            }

            $estateId = $this->estateMap[$row->BUID] ?? null;

            if (!$estateId) {
                $this->warn("[WARN] Transformer {$row->TransID}: estate not found for BUID {$row->BUID}");
            }

            $payload = [
                'Estate_id'  => $estateId,
                'estate'     => null,
                'Capacity'   => $row->Capacity,
                'MDMeterSN'  => $row->MDMeterSN,
                'CTRatio'    => $row->CTRatio,
                'Multiplier' => $row->Multiplier,
                'Location'   => $row->Location,
                'Title'      => $row->Name,
                'City'       => $row->City,
                'State'      => $row->State,
                'Status'     => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->stats['transformers']++;

            if ($this->isDryRun()) {
                $this->preview("Transformer {$row->TransID}", $payload);
                $this->transformerMap[$row->TransID] = 'dry_' . Str::uuid();
                continue;
            }

            $transformer = Transformer::updateOrCreate(
                ['Estate_id' => $estateId, 'Title' => $row->Name],
                $payload
            );

            $this->transformerMap[$row->TransID] = $transformer->id;
        }
    }

    // -----------------------------------------------------------------------
    // TARIFFS
    // -----------------------------------------------------------------------

    protected function importTariffs(string $path): void
    {
        $rows = $this->read('tariffs.json', $path);

        foreach ($rows as $row) {
            $key = $this->tariffKey($row->TariffID, $row->BUID);

            if (isset($this->tariffMap[$key])) {
                continue; // already migrated in a previous run
            }

            // if (isset($this->invalidTariffKeys[$key])) {
            //     $this->stats['tariffs_skipped_invalid']++;
            //     continue;
            // }

            $estateId = $this->estateMap[$row->BUID] ?? null;

            if ($estateId === null) {
                $this->warn("[WARN] Tariff {$key}: no matching estate for BUID {$row->BUID}");
            }

            $payload = [
                'title'        => $row->Description,
                'tariff_index' => $row->TariffID,
                'estate_id'    => $estateId,
                'status'       => 2,
                'type'         => null, // set later by classifyTariffTypes()
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

    // -----------------------------------------------------------------------
    // TARIFF STATES
    // -----------------------------------------------------------------------

    protected function importTariffStates(string $path): void
    {
        $rows = $this->loadTariffStates($path);

        $grouped = [];
        foreach ($rows as $row) {
            $key = $this->tariffKey($row->TariffID, $row->BUID);
            $grouped[$key][] = $row;
        }

        foreach ($grouped as $key => $states) {
            $tariffId = $this->tariffMap[$key] ?? null;

            if (!$tariffId) {
                $this->warn("[WARN] TariffState: unresolved tariff {$key} — skipping");
                $this->stats['tariff_states_skipped']++;
                continue;
            }

            $latest = $states[0];
            foreach ($states as $state) {
                if ($this->isNewerState($state, $latest)) {
                    $latest = $state;
                }
            }

            foreach ($states as $state) {
                $isLatest = $state === $latest;

                $payload = [
                    'amount'         => $state->Rate,
                    'effective_from' => $state->EffectiveDate ?? now(),
                    'vat'            => $state->VAT,
                    'fixed_charge'   => $state->FC,
                    'estate_id'      => $this->estateMap[$state->BUID] ?? null,
                    'tariff_id'      => $tariffId,
                    't_index'        => $state->VersionNo,
                    'status'         => $isLatest ? 2 : 0,
                ];

                $this->stats['tariff_states_created']++;

                if ($this->isDryRun()) {
                    $this->preview('Tariff State', $payload);
                    continue;
                }

                TarrifState::create($payload);
            }
        }
    }

    // -----------------------------------------------------------------------
    // METERS
    // -----------------------------------------------------------------------

    protected function importMeters(string $path): void
    {
        $rows = $this->read('meters.json', $path);

        foreach ($rows as $row) {
            if (isset($this->meterMap[$row->MeterNo])) {
                continue;
            }

            $estateId       = $this->estateMap[$row->BUID] ?? null;
            $newTariffId    = $this->resolveTariffId($row->Tariff,  $row->BUID, 'NewTariffID',     $row->MeterNo);
            $newTariffDualId = $this->resolveTariffId($row->Tariff2, $row->BUID, 'NewTariffDualID', $row->MeterNo);
            $oldTariffId    = $this->resolveTariffId($row->OldTariff, $row->BUID, 'OldTariffID',   $row->MeterNo);

            // Classify Grid / Off Grid (safe to do even in dry-run)
            if ($newTariffId) {
                $this->classifyTariff($this->tariffKey($row->Tariff, $row->BUID), 'Grid');
            }

            if ($row->IsDual && $newTariffDualId) {
                $this->classifyTariff($this->tariffKey($row->Tariff2, $row->BUID), 'Off Grid');
            }

            $payload = [
                'estate_id'       => $estateId,
                'meterNo'         => trim($row->MeterNo),
                'status'          => 2,
                'meterModel'      => $row->Model,
                'AccountNo'       => $row->AccountNo,
                'isDualTariff'    => $row->IsDual ? '1' : '0',
                'NewSGC'          => $row->SGC,
                'OldSGC'          => $row->OldSGC,
                'NewTariffID'     => $newTariffId,
                'NewTariffDualID' => $newTariffDualId,
                'OldTariffID'     => $oldTariffId,
                'NewSGCDual'      => $row->SGC2,
                'KRN1'            => $row->KRN1,
                'KRN2'            => $row->KRN2,
                'NeedKCT'         => $row->NeedKCT ? '1' : '0',
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            $this->stats['meters']++;

            if ($this->isDryRun()) {
                $this->meterMap[$row->MeterNo] = 'dry_' . Str::uuid();
                $this->preview("Meter {$row->MeterNo}", $payload);
                continue;
            }

            $meter = Meter::create($payload);
            $this->meterMap[$row->MeterNo] = $meter->id;
        }
    }

    protected function resolveTariffId($tariffRaw, $buid, string $label, string $meterNo): ?int
    {
        if (empty($tariffRaw)) {
            return null;
        }

        $key = $this->tariffKey($tariffRaw, $buid);
        $id  = $this->tariffMap[$key] ?? null;

        if ($id === null) {
            $this->warn("[WARN] Meter {$meterNo}: {$label} tariff {$key} not found (invalid/missing) — left null");
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

        $this->tariffTypeMap[$key]   = 'CONFLICT';
        $this->tariffConflicts[$key] = true;
    }

    // -----------------------------------------------------------------------
    // TARIFF TYPE CLASSIFICATION (called after importMeters)
    // -----------------------------------------------------------------------

    protected function classifyTariffTypes(): void
    {
        foreach ($this->tariffTypeMap as $key => $type) {
            $tariffId = $this->tariffMap[$key] ?? null;

            if (!$tariffId) {
                continue;
            }

            if ($type === 'CONFLICT') {
                $this->warn("[CONFLICT] Tariff {$key} appears as both Grid and Off Grid — left type=null for manual review.");
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

    // -----------------------------------------------------------------------
    // USERS (admin / staff)
    // -----------------------------------------------------------------------

    protected function importUserInfo(string $path): void
    {
        $rows = $this->read('user_info.json', $path);

        $emailize = function ($firstname, $lastname) {
            return trim(strtolower(str_replace(' ', '-', $firstname))) . '-' . trim(strtolower(str_replace(' ', '-', $lastname))) . '@legacy.local.com';
        };

        foreach ($rows as $row) {
            $email = $row->email ?? $emailize($row->first_name, $row->last_name);

            $payload = [
                'first_name'   => $row->first_name ?? null,
                'last_name'    => $row->last_name  ?? null,
                'email'        => $email,
                'password'     => $row->password_hash,
                'role'         => 3,
                'estate_id'    => $this->estateMap[$row->estate_buid] ?? null,
                'estate_name'  => $row->estate_name,
                'status'       => 2,
                'can_login'    => $row->can_login,
                'raw_password' => $row->raw_password,
            ];

            $this->stats['users_info']++;

            if ($this->isDryRun()) {
                $this->preview('USER INFO', $payload);
                continue;
            }

            User::updateOrCreate(['email' => $email], $payload);
        }
    }

    // -----------------------------------------------------------------------
    // USERS (mobile / customer operators)
    // -----------------------------------------------------------------------

    protected function importUserData(string $path): void
    {
        $rows = $this->read('user_data.json', $path);

        foreach ($rows as $row) {
            [$first, $last] = $this->splitName($row->FullName);

            $payload = [
                'first_name' => $first,
                'last_name'  => $last,
                'email'      => strtolower($row->OperatorName),
                'phone'      => $row->PhoneNumber,
                'meterNo'    => $row->MeterNo,
                'role'       => 2,
                'password'   => Hash::make($row->Pw ?? 'default123'),
                'status'     => 2,
            ];

            $this->stats['users_data']++;

            if ($this->isDryRun()) {
                $this->preview('Customer | MOBILE', $payload);
                continue;
            }

            User::updateOrCreate(['meterNo' => $row->MeterNo], $payload);
        }
    }

    // -----------------------------------------------------------------------
    // CUSTOMERS (legacy resident records)
    // -----------------------------------------------------------------------

    protected function importCustomers(string $path): void
    {
        $rows = $this->read('customers.json', $path);

        foreach ($rows as $row) {
            $estateId = $this->estateMap[$row->BUID] ?? null;

            $meterId = null;
            if (!empty($row->MeterNo)) {
                $meter   = Meter::where('meterNo', trim($row->MeterNo))->first();
                $meterId = $meter?->id;
            }

            $tariffId = null;
            if (!empty($row->TariffID)) {
                $tariffId = $this->tariffMap[$this->tariffKey($row->TariffID, $row->BUID)] ?? null;
            }

            $email = !empty($row->EMail)
                ? $row->EMail
                : strtolower(str_replace(' ', '', $row->AccountNo)) . '@legacy.local.com';

            $payload = [
                'first_name'  => trim($row->OtherNames),
                'last_name'   => trim($row->Surname),
                'phone'       => $row->Mobile,
                'email'       => $email,
                'address'     => $row->Address,
                'state'       => $row->State,
                'meterNo'     => $row->MeterNo,
                'meterid'     => $meterId,
                'estate_id'   => $estateId,
                'account_no'  => $row->AccountNo,
                'tariffid'    => $tariffId,
                'estate_name' => $row->BUID,
                'role'        => 2,
                'status'      => 2,
                'can_login'   => 0,
                'password'    => Hash::make('default123'),
                'created_at'  => $row->OpenDate
                    ? date('Y-m-d H:i:s', strtotime($row->OpenDate))
                    : now(),
                'updated_at'  => now(),
            ];

            $this->stats['customers']++;

            if ($this->isDryRun()) {
                $this->preview("CUSTOMER {$row->AccountNo}", $payload);
                continue;
            }

            // Deduplicate emails at insert time
            if (User::where('email', $payload['email'])->exists()) {
                $payload['email'] = 'legacy_' . uniqid() . '@legacy.local.com';
            }

            User::updateOrCreate(['meterNo' => $row->MeterNo], $payload);
        }
    }

    // -----------------------------------------------------------------------
    // ATTACH METERS → USERS (runs inside transaction module, before transactions)
    // -----------------------------------------------------------------------

    protected function attachMeters(): void
    {
        $users = User::whereNotNull('meterNo')->get();

        foreach ($users as $user) {
            Meter::where('meterNo', $user->meterNo)->update(['user_id' => $user->id]);
        }
    }

    // -----------------------------------------------------------------------
    // TRANSACTIONS
    // -----------------------------------------------------------------------

    protected function importTransactions(string $path): void
    {
        $rows  = $this->read('transactions.json', $path);
        $limit = (int) $this->option('transactions');

        $rows = collect($rows)
            ->sortByDesc('TransactionDateTime')
            ->groupBy('MeterNo')
            ->flatMap(fn ($txns) => $txns->take($limit));

        foreach ($rows as $row) {
            $meter = Meter::where('meterNo', trim($row->MeterNo))->first();

            if (!$meter) {
                $this->warn("Transaction {$row->TransactionNo} skipped — meter not found");
                continue;
            }

            if (!$meter->user_id) {
                $this->warn("Transaction {$row->TransactionNo} skipped — meter has no user");
                continue;
            }

            // Idempotency guard
            if (DB::table('transactions')->where('trx_id', (string) $row->TransactionNo)->exists()) {
                continue;
            }

            $payload = [
                'user_id'             => $meter->user_id,
                'estate_id'           => $meter->estate_id,
                'pay_type'            => 'wallet',
                'service_type'        => 'electricity',
                'service'             => 'legacy_ecmi',
                'utility_id'          => $row->MeterNo,
                'utility_amount'      => $row->Units,
                'trx_id'              => (string) $row->TransactionNo,
                'payment_ref'         => $row->transref,
                'amount'              => $row->Amount,
                'fee'                 => $row->FC ?? 0,
                'unit_amount'         => $row->Units,
                'status'              => 2,
                'note'                => $row->Reasons,
                'miscellaneous'       => $row->Token,
                'action_payload'      => json_encode([
                    'legacy_transaction_no' => $row->TransactionNo,
                    'account_no'            => $row->AccountNo,
                    'buid'                  => $row->BUID,
                    'token'                 => $row->Token,
                    'token_type'            => $row->TokenType,
                    'vat'                   => $row->VAT,
                    'fc'                    => $row->FC,
                    'mmf'                   => $row->MMF,
                    'kva'                   => $row->KVA,
                ]),
                'vending_amount'      => $row->CostOfUnits,
                'service_rendered_at' => $row->TransactionDateTime,
                'created_at'          => $row->TransactionDateTime,
                'updated_at'          => now(),
            ];

            $this->stats['transactions_data']++;

            if ($this->isDryRun()) {
                $this->preview("Transaction {$row->TransactionNo}", $payload);
                continue;
            }

            DB::table('transactions')->insert($payload);

            $this->info('Entered Credit Token ' . $row->TransactionNo . '  ->  ' . $row->TokenType);
            $this->info('Entered Credit Token ' . $row->TransactionNo);
            if (!DB::table('credit_tokens')->where('trx_id', (string) $row->TransactionNo)->exists()) {
                $estateName = null;
                if ($meter->estate_id) {
                    $estate = Estate::find($meter->estate_id);
                    $estateName = $estate?->title;
                }

                CreditToken::updateOrCreate([
                    'trx_id'           => (string) $row->TransactionNo,
                ], [
                    'user_id'          => $meter->user_id,
                    'trx_id'           => (string) $row->TransactionNo,
                    'meterNo'          => trim($row->MeterNo),
                    'token'            => $row->Token,
                    'amount'           => $row->Amount,
                    'amount_charged'   => $row->Amount,
                    'vat'              => $row->VAT ?? 0,
                    'vatAmount'        => (string) ($row->VAT ?? 0),
                    'costOfUnit'       => (string) ($row->CostOfUnits ?? 0),
                    'unitkwh'          => (string) ($row->Units ?? 0),
                    'fee'              => $row->FC ?? 0,
                    'estate_id'        => $meter->estate_id,
                    'estate_name'      => $estateName,
                    'tariff_id'        => $meter->NewTariffID,
                    'tariff_amount'    => null,
                    'tariffPerKWatt'   => null,
                    'kct_tokens'       => null,
                    'customer_email'   => null,
                    'receiver_meterNo' => null,
                    'status'           => 2,
                    'created_at'       => $row->TransactionDateTime,
                    'updated_at'       => $row->TransactionDateTime,
                ]);
            }
        }
    }

    // -----------------------------------------------------------------------
    // Utilities
    // -----------------------------------------------------------------------

    protected function splitName(string $name): array
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

    // -----------------------------------------------------------------------
    // SUBACCOUNT BACKFILL (standalone — safe to re-run, no state dependency)
    // -----------------------------------------------------------------------

    protected function importSubAccounts(string $path): void
    {
        $rows = $this->read('paystack_subaccounts.json', $path);

        $updated  = 0;
        $skipped  = 0;
        $notFound = 0;

        foreach ($rows as $row) {
            // Resolve estate: legacy_buid is the clean match; fall back to title
            $estate = Estate::where('legacy_buid', $row->BUID)->first()
                ?? Estate::where('title', $row->EstateName)->first()
                ?? Estate::where('title', 'like', '%' . $row->SubAccountName . '%' )->first();

            if (!$estate) {
                $this->warn("[NOT FOUND] No estate matched BUID={$row->BUID} / Name={$row->EstateName} — skipping");
                $notFound++;
                continue;
            }

            // Skip rows where all four fields are already populated
            if (
                $estate->paystack_subaccount &&
                $estate->account_no         &&
                $estate->account_name       &&
                $estate->bank
            ) {
                $this->line("[SKIP] Estate '{$estate->title}' already has subaccount data.");
                $skipped++;
                continue;
            }

            $updates = [];

            // Always write legacy_buid if missing — makes future runs use the fast path
            if (!$estate->legacy_buid) {
                $updates['legacy_buid'] = $row->BUID;
            }

            if (!$estate->paystack_subaccount && !empty($row->SubAcctID)) {
                $updates['paystack_subaccount'] = $row->SubAcctID;
            }

            if (!$estate->account_no && !empty($row->BankAccountNo)) {
                $updates['account_no'] = $row->BankAccountNo;
            }

            if (!$estate->account_name && !empty($row->BankAccountName)) {
                $updates['account_name'] = $row->BankAccountName;
            }

            if (!$estate->bank && !empty($row->BankName)) {
                $updates['bank'] = $row->BankName;
            }

            if (empty($updates)) {
                $skipped++;
                continue;
            }

            if ($this->isDryRun()) {
                $this->preview("SUBACCOUNT → Estate '{$estate->title}' (id={$estate->id})", $updates);
                $updated++;
                continue;
            }

            // foreach($updates as $key => $value) {
            //     $estate->$key = $estate->$value;
            // }
            // $estate->save();
            // DB::commit();
            DB::table('estates')
                ->where('id', $estate->id)
                ->update($updates);

            // $estate->update($updates);

            $this->info("[OK] Estate '{$estate->title}' (id={$estate->id}) updated: " . implode(', ', array_keys($updates)) . implode(', ', array_values($updates)));
            $updated++;
        }

        $this->newLine();
        $this->table(['Metric', 'Count'], [
            ['Estates updated',   $updated],
            ['Estates skipped',   $skipped],
            ['Estates not found', $notFound],
        ]);
    }

    protected function importUtilitySubAccounts(string $path): void
    {
        $rows = $this->read('subaccounts.json', $path);
        $customers = $this->read('customers.json', $path);

        $customerMeterMap = [];
        foreach ($customers as $c) {
            $acctNo = trim($c->AccountNo ?? '');
            if ($acctNo !== '') {
                $customerMeterMap[$acctNo] = trim($c->MeterNo ?? '');
            }
        }

        $created = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $acctNo = trim($row->AccountNo ?? '');
            $meterNo = $customerMeterMap[$acctNo] ?? null;

            if (!$meterNo) {
                $this->warn("Customer not found for account {$acctNo}");
                $skipped++;
                continue;
            }

            $user = User::where('meterNo', $meterNo)->first();

            if (!$user) {
                $this->warn("User not found for meter {$meterNo}");
                $skipped++;
                continue;
            }

            $payload = [
                'estate_id'        => $user->estate_id,
                'user_id'          => $user->id,
                'type'             => 'debt',
                'title'            => $row->SubAccountAbbre,
                'amount'           => $row->AmountAttached ?? 0,
                'balance'          => $row->Balance,
                'start_date'       => $row->StartDate,
                'mode_of_payment'  => $row->ModeOfPayment,
                'payment_amount'   => $row->PaymentAmount,
                'activated'        => (bool)$row->activated,
                'operator_id'      => $row->OperatorID,
                'status'           => 2,
                'created_at'       => $row->Date ?? now(),
                'updated_at'       => $row->lastmodified ?? now(),
            ];

            if ($this->isDryRun()) {
                $this->preview("UTILITY {$row->SubAccountNo}", $payload);
                continue;
            }

            DB::table('utilities')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'title'   => $row->SubAccountAbbre,
                ],
                $payload
            );

            $created++;
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Imported', $created],
                ['Skipped', $skipped],
            ]
        );
    }

    protected function printSummary(): void
    {
        $this->newLine();
        $this->info($this->isDryRun() ? '── DRY RUN SUMMARY ──' : '── IMPORT SUMMARY ──');

        $this->table(['Metric', 'Count'], [
            ['Estates matched (existing)',      $this->stats['estates_matched']],
            ['Estates created',                 $this->stats['estates_created']],
            ['Transformers imported',           $this->stats['transformers']],
            ['Tariffs created',                 $this->stats['tariffs_created']],
            ['Tariffs skipped (INVALID)',        $this->stats['tariffs_skipped_invalid']],
            ['Tariff states created',           $this->stats['tariff_states_created']],
            ['Tariff states skipped',           $this->stats['tariff_states_skipped']],
            ['Meters created',                  $this->stats['meters']],
            ['Tariffs classified Grid',         $this->stats['tariffs_grid']],
            ['Tariffs classified Off Grid',     $this->stats['tariffs_offgrid']],
            ['Tariffs left null (conflict)',     count($this->tariffConflicts)],
            ['Users (admin/staff) created',     $this->stats['users_info']],
            ['Users (mobile/customer) created', $this->stats['users_data']],
            ['Customers created',               $this->stats['customers']],
            ['Transactions imported',           $this->stats['transactions_data']],
        ]);

        $this->newLine();
        $this->info('Completed modules: ' . (
            empty($this->completedModules)
                ? 'none'
                : implode(' → ', $this->completedModules)
        ));

        if (!empty($this->tariffConflicts)) {
            $this->warn('Conflicted tariffs (manual review needed): ' . implode(', ', array_keys($this->tariffConflicts)));
        }
    }
}
