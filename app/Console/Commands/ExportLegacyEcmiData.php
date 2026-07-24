<?php

namespace App\Console\Commands;

use App\Models\MigrationMap;
use App\Models\MigrationState;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExportLegacyEcmiData extends Command
{
    protected $signature = 'legacy:export
                        {estate?}
                        {--chunk=100 : Number of unprocessed estates per run}
                        {--reset     : Clear export state and start fresh}
                        {--status    : Show export progress without exporting}
                        {--module=   : Run a single export module only (subaccount|utility-subaccount)}';

    protected $description = 'Export MSSQL legacy data into JSON files (chunked, resumable)';

    protected array $chunkBuids = [];
    protected int $totalEstates = 0;
    protected int $exportedCount = 0;
    protected int $remainingCount = 0;

    protected function legacy()
    {
        return DB::connection('mssql_legacy');
    }

    public function handle(): int
    {
        $path = storage_path('app/legacy-export');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // --reset: clear export state
        if ($this->option('reset')) {
            MigrationState::clearContext('export');
            MigrationMap::clearMap('exported_buids');
            $this->info('Export state cleared. Starting fresh.');
        }

        // --status: show progress and exit
        if ($this->option('status')) {
            return $this->showStatus();
        }

        // Load estate list and determine chunk
        $this->prepareChunk();

        // Single-module mode
        if ($module = $this->option('module')) {
            return match ($module) {
                'subaccount'         => $this->exportPaystackSubAccounts($path) ?? self::SUCCESS,
                'utility-subaccount' => $this->exportUtilitySubAccounts($path) ?? self::SUCCESS,
                default              => $this->error("Unknown module: {$module}") ?? self::FAILURE,
            };
        }

        $estateArg = $this->argument('estate');

        if ($estateArg) {
            $this->info("Exporting single estate: {$estateArg}");
        } else {
            $this->info("Exporting chunk: {$this->exportedCount} of {$this->totalEstates} total ({$this->remainingCount} remaining)");
        }

        $this->info("Exporting legacy data...");

        $this->exportEstates($path, $estateArg);
        $this->exportPaystackSubAccounts($path, $estateArg);
        $this->exportTransformers($path, $estateArg);
        $this->exportTariffs($path, $estateArg);
        $this->exportTariffStates($path, $estateArg);
        $this->exportMeters($path, $estateArg);
        $this->exportUserInfo($path, $estateArg);
        $this->exportUserData($path, $estateArg);
        $this->exportCustomers($path, $estateArg);
        $this->exportTransactions($path, $estateArg);
        $this->exportSubAccounts($path, $estateArg);

        // Persist export state (only for chunk mode, not single-estate mode)
        if (!$estateArg) {
            $this->saveExportState();
        }

        $this->info("Export completed successfully");

        return self::SUCCESS;
    }

    // -----------------------------------------------------------------------
    // Chunk preparation
    // -----------------------------------------------------------------------

    protected function prepareChunk(): void
    {
        $estateArg = $this->argument('estate');

        if ($estateArg) {
            $this->chunkBuids = [$estateArg];
            $this->totalEstates = 1;
            $this->exportedCount = 0;
            $this->remainingCount = 1;
            return;
        }

        $allBuids = $this->legacy()
            ->table('BusinessUnit')
            ->pluck('BUID')
            ->map(fn ($b) => (string) $b)
            ->toArray();

        $this->totalEstates = count($allBuids);

        $exportedBuids = MigrationMap::getKeys('exported_buids');

        $remaining = array_values(array_filter($allBuids, fn ($b) => !in_array($b, $exportedBuids, true)));

        $this->remainingCount = count($remaining);
        $this->exportedCount = $this->totalEstates - $this->remainingCount;

        $chunkSize = (int) $this->option('chunk');

        $this->chunkBuids = array_slice($remaining, 0, $chunkSize);

        if (empty($this->chunkBuids)) {
            $this->info("All estates already exported. Use --reset to start over.");
        }
    }

    // -----------------------------------------------------------------------
    // --status
    // -----------------------------------------------------------------------

    protected function showStatus(): int
    {
        $allBuids = $this->legacy()
            ->table('BusinessUnit')
            ->pluck('BUID')
            ->map(fn ($b) => (string) $b)
            ->toArray();

        $total = count($allBuids);
        $exported = MigrationMap::getKeys('exported_buids');
        $exportedCount = count($exported);
        $remaining = $total - $exportedCount;

        $states = MigrationState::context('export')->get();

        $this->info('── EXPORT STATUS ──');
        $this->table(['Metric', 'Value'], [
            ['Total estates', $total],
            ['Exported', $exportedCount],
            ['Remaining', $remaining],
            ['Chunk size', $this->option('chunk')],
        ]);

        if ($states->isNotEmpty()) {
            $this->info('');
            $this->table(['Module', 'Status', 'Stats'], $states->map(fn ($s) => [
                $s->module,
                $s->status,
                $s->stats ? json_encode($s->stats) : '—',
            ])->toArray());
        }

        return self::SUCCESS;
    }

    // -----------------------------------------------------------------------
    // --reset handled in handle()
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // Save export state
    // -----------------------------------------------------------------------

    protected function saveExportState(): void
    {
        foreach ($this->chunkBuids as $buid) {
            MigrationMap::setMapping('exported_buids', $buid, $buid);
        }

        MigrationState::markCompleted('export', 'chunk', [
            'chunk_size'    => count($this->chunkBuids),
            'total'         => $this->totalEstates,
            'exported'      => $this->exportedCount + count($this->chunkBuids),
            'remaining'     => $this->remainingCount - count($this->chunkBuids),
            'exported_at'   => now()->toIso8601String(),
        ]);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    protected function write(string $file, array $data, string $path)
    {
        file_put_contents(
            $path . '/' . $file,
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Check if a row's BUID is in the current chunk.
     * When exporting a single estate (by argument), all rows pass.
     */
    protected function inChunk($buid): bool
    {
        if ($this->argument('estate')) {
            return true;
        }

        return in_array((string) $buid, $this->chunkBuids, true);
    }

    /**
     * Filter a MSSQL query result to only rows whose BUID is in the chunk.
     */
    protected function filterByChunk(array $rows): array
    {
        return array_values(array_filter($rows, fn ($row) => $this->inChunk($row->BUID ?? null)));
    }

    /* ================= ESTATES ================= */

    protected function exportEstates($path, $estate)
    {
        $query = $this->legacy()->table('BusinessUnit');

        if ($estate) {
            $query->where('BUID', $estate)->orWhere('Name', $estate);
        } else {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $this->write('estates.json', $query->get()->toArray(), $path);

        $this->info("Exported estates");
    }

    protected function exportPaystackSubAccounts($path, $estate): void
    {
        $query = $this->legacy()
            ->table('Paystack_SubAcct')
            ->join('BusinessUnit', 'BusinessUnit.BUID', '=', 'Paystack_SubAcct.RUID')
            ->select(
                'Paystack_SubAcct.*',
                'BusinessUnit.BUID',
                'BusinessUnit.Name as EstateName'
            );

        if ($estate) {
            $query->where('Paystack_SubAcct.RUID', $estate)
                ->orWhere('BusinessUnit.Name', $estate);
        } else {
            $query->whereIn('Paystack_SubAcct.RUID', $this->chunkBuids);
        }

        $rows = $query->get()->toArray();

        $this->write('paystack_subaccounts.json', $rows, $path);

        $this->info("Exported " . count($rows) . " Paystack subaccount(s) → paystack_subaccounts.json");
    }

    /* ================= TARIFFS ================= */

    protected function exportTariffs($path, $estate)
    {
        $query = $this->legacy()->table('Tariff');

        if ($estate) {
            $query->where('BUID', $estate);
        } else {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $this->write('tariffs.json', $query->get()->toArray(), $path);

        $this->info("Exported tariffs");
    }

    /* ============== TARIFF STATES ============== */

    protected function exportTariffStates($path, $estate)
    {
        $query = $this->legacy()->table('TariffRates');

        if ($estate) {
            $query->where('BUID', $estate);
        } else {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $this->write('tariff_states.json', $query->orderBy('EffectiveDate')->get()->toArray(), $path);

        $this->info("Exported tariff states");
    }

    /* ================= METERS ================= */

    protected function exportMeters($path, $estate)
    {
        $query = $this->legacy()->table('Meters');

        if ($estate) {
            $query->where('BUID', $estate);
        } else {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $this->write('meters.json', $query->orderBy('MeterNo')->get()->toArray(), $path);

        $this->info("Exported meters");
    }

    /* =============== USER INFO =============== */

    protected function exportUserInfo($path, $estate)
    {
        $query = $this->legacy()->table('UserInfo');

        if ($estate) {
            $query->where('BUID', $estate)
                ->orWhere('BusinessUnit', $estate);
        } else {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $rows = $query->orderBy('OperatorId')->get();

        $export = [];

        foreach ($rows as $row) {

            $password = $this->reconstructPassword(
                $row->OperatorId,
                (int) $row->Pw_Len
            );

            [$firstName, $lastName] = $this->splitName($row->FullName);

            $email = $this->sanitizeEmailForExport($row->OperatorName);

            $export[] = [
                'operator_id' => $row->OperatorId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'estate_buid' => $row->BUID,
                'estate_name' => $row->BusinessUnit,
                'password_hash' => Hash::make($password),
                'activated' => $row->Activated,
                'can_login' => $row->Activated ? 1 : 0,
                'created_at' => $row->OperatorDate ?? now(),
                'raw_password' => $password,
            ];
        }

        $this->write('user_info.json', $export, $path);

        $this->info("Exported user info (with hashed passwords)");
    }

    /* =============== USER DATA =============== */

    protected function exportUserData($path, $estate)
    {
        $query = $this->legacy()->table('UserData')
            ->join('Meters', 'Meters.MeterNo', '=', 'UserData.MeterNo')
            ->select('UserData.*', 'Meters.BUID as meter_buid');

        if ($estate) {
            $query->where('Meters.BUID', $estate);
        } else {
            $query->whereIn('Meters.BUID', $this->chunkBuids);
        }

        $this->write('user_data.json', $query->orderBy('UserData.OperatorId')->get()->toArray(), $path);

        $this->info("Exported user data");
    }

    protected function reconstructPassword(int $operatorId, int $length): string
    {
        $passwordRow = $this->legacy()
            ->table('UserPw')
            ->where('OperatorId', $operatorId)
            ->first();

        if (!$passwordRow) {
            return Str::random(12);
        }

        $password = '';

        for ($i = 1; $i <= $length; $i++) {

            $column = 'pw' . $i;

            $ascii = $passwordRow->{$column} ?? null;

            if ($ascii === null) {
                continue;
            }

            $password .= chr((int) $ascii);
        }

        return $password ?: Str::random(12);
    }

    protected function splitName(?string $name): array
    {
        $name = trim((string) $name);

        if (empty($name)) {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $name);

        $firstName = array_shift($parts);

        $lastName = count($parts)
            ? implode(' ', $parts)
            : null;

        return [$firstName, $lastName];
    }

    protected function sanitizeEmailForExport(?string $email): ?string
    {
        $email = trim((string) $email);

        if (empty($email)) {
            return null;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $exists = User::where('email', $email)->exists();

        if ($exists) {
            return null;
        }

        return strtolower($email);
    }


    protected function exportTransactions($path, $estate)
    {
        $query = $this->legacy()
            ->table('Transactions')
            ->orderByDesc('TransactionDateTime');

        if ($estate) {
            $query->where('BUID', $estate);
        } else {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $this->write(
            'transactions.json',
            $query->get()->toArray(),
            $path
        );

        $this->info('Exported transactions');
    }

    protected function exportTransformers($path, $estate)
    {
        $query = $this->legacy()
            ->table('Transformer');

        if ($estate) {
            $query->where('BUID', $estate);
        } else {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $this->write(
            'transformers.json',
            $query->get()->toArray(),
            $path
        );

        $this->info('Exported transfomers');
    }

    protected function exportCustomers($path, $estate)
    {
        $query = $this->legacy()
            ->table('Customers');

        if ($estate) {
            $query->where('BUID', $estate);
        } else {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $this->write(
            'customers.json',
            $query->get()->toArray(),
            $path
        );

        $this->info('Exported customers');
    }

    protected function exportUtilitySubAccountCustomers(string $path): void
    {
        $query = $this->legacy()->table('Customers');

        if (!$this->argument('estate')) {
            $query->whereIn('BUID', $this->chunkBuids);
        }

        $rows = $query->get()->toArray();
        $this->write('customers.json', $rows, $path);
        $this->info("Exported " . count($rows) . " customers for utility subaccount mapping");
    }

    protected function exportUtilitySubAccounts(string $path, $estate): void
    {
        $this->exportUtilitySubAccountCustomers($path);
        $this->exportSubAccounts($path, $estate);
    }

    protected function exportSubAccounts($path, $estate)
    {
        $query = $this->legacy()->table('SubAccount');

        if ($estate) {
            $query->join('Meters', 'Meters.AccountNo', '=', 'SubAccount.AccountNo')
                ->where('Meters.BUID', $estate)
                ->select('SubAccount.*');
        } elseif (!empty($this->chunkBuids)) {
            $query->join('Meters', 'Meters.AccountNo', '=', 'SubAccount.AccountNo')
                ->whereIn('Meters.BUID', $this->chunkBuids)
                ->select('SubAccount.*');
        }

        $rows = $query->get()->toArray();

        $this->write('subaccounts.json', $rows, $path);

        $this->info("Exported ".count($rows)." subaccounts");
    }
}
