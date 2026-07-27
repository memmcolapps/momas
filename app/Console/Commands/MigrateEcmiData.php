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

class MigrateEcmiData extends Command
{
    protected $signature = 'legacy:migrate
                            {estate? : Optional estate/BUID filter}
                            {--chunk=500 : Chunk size}
                            {--transactions=5: Number of latest transactions to import}
                            {--dry-run : Run without inserting}';

    protected $description = 'Migrate legacy MSSQL data into MySQL';

    protected array $estateMap = [];
    protected array $tariffMap = [];
    protected array $meterMap = [];
    protected array $transformerMap = [];
    protected? Estate $estate = null;

    protected ?string $estateFilter = null;

    public function handle(): int
    {
        $this->estateFilter = $this->argument('estate');

        $this->info('=====================================');
        $this->info('Starting Legacy Migration');

        if ($this->estateFilter) {
            $this->info('Estate Filter: ' . $this->estateFilter);
        }
        $this->info('=====================================');

        try {
            DB::beginTransaction();

            $this->migrateEstates();
            $this->migrateTransformers();
            $this->migrateTariffs();
            $this->migrateTariffStates();
            $this->migrateMeters();
            $this->migrateUserInfoUsers();
            $this->migrateUserDataUsers();
            $this->attachMetersToUsers();
            $this->importTransactions(
                (int) ($this->option('transactions') ?? 5)
            );

            DB::commit();

            $this->info('=====================================');
            $this->info('Migration completed successfully');
            $this->info('=====================================');

            return self::SUCCESS;

        } catch (\Throwable $e) {
            DB::rollback();

            $this->error('Migration failed');
            $this->error($e->getMessage());
            $this->error($e->getFile() . ':' . $e->getLine());

            report($e);

            return self::FAILURE;
        }
    }

    protected function legacy()
    {
        return DB::connection('mssql_legacy');
    }

    protected function chunkSize(): int
    {
        return (int) $this->option('chunk');
    }

    protected function isDryRun(): bool
    {
        return (bool) $this->option('dry-run');
    }

    /* =========================================================
     | ESTATES
     * ========================================================= */

    protected function migrateEstates(): void
    {
        $this->info('Migrating estates...');

        $query = $this->legacy()
            ->table('BusinessUnit');

        if ($this->estateFilter) {
            $query->where('BUID', $this->estateFilter)
                  ->orWhere('Name', $this->estateFilter);
        }

        $rows = $query->get();

        foreach ($rows as $row) {

            $existing = Estate::where('title', $row->Name)->first();

            if ($existing) {
                $this->estateMap[$row->BUID] = $existing->id;
                continue;
            }

            $payload = [
                'title' => $row->Name,
                'address' => $row->Address,
                'state' => $row->State,
                'status' => $row->status1 === 'N' ? 1 : 0,
                'min_pur' => $row->MinimumVend ?? 0,
                'max_pur' => $row->MaximumVend ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($this->isDryRun()) {
                $this->line('DRY RUN: Estate => ' . json_encode($payload));
                $this->estateMap[$row->BUID] = Str::uuid();
                continue;
            }

            $estate = Estate::create($payload);
            $this->estate = $estate;

            $this->estateMap[$row->BUID] = $estate->id;
        }

        $this->info('Estates migrated');
    }

    /* =========================================================
     | TARIFFS
     * ========================================================= */

    protected function migrateTariffs(): void
    {
        $this->info('Migrating tariffs...');

        $query = $this->legacy()
            ->table('Tariff');

        if ($this->estateFilter) {
            $query->where('BUID', $this->estateFilter);
        }

        $rows = $query->get();

        foreach ($rows as $row) {

            $estateId = $this->estateMap[$row->BUID] ?? null;

            $existing = Tariff::where('tariff_index', $row->TariffID)
                ->where('estate_id', $estateId)
                ->first();

            if ($existing) {
                $this->tariffMap[$row->TariffID . '_' . $row->BUID] = $existing->id;
                continue;
            }

            $payload = [
                'title' => $row->Description,
                'tariff_index' => $row->TariffID,
                'estate_id' => $estateId,
                'status' => $row->status1 === 'N' ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($this->isDryRun()) {
                $this->line('DRY RUN: Tariff => ' . json_encode($payload));
                continue;
            }

            $tariff = Tariff::create($payload);

            $this->tariffMap[$row->TariffID . '_' . $row->BUID] = $tariff->id;
        }

        $this->info('Tariffs migrated');
    }

    /* =========================================================
     | TARIFF STATES
     * ========================================================= */

    protected function migrateTariffStates(): void
    {
        $this->info('Migrating tariff states...');

        $query = $this->legacy()
            ->table('TariffRates')
            ->orderBy('EffectiveDate');

        if ($this->estateFilter) {
            $query->where('BUID', $this->estateFilter);
        }

        $rows = $query->get();

        foreach ($rows as $row) {

            $estateId = $this->estateMap[$row->BUID] ?? null;

            $tariffId = $this->tariffMap[$row->TariffID . '_' . $row->BUID] ?? null;

            $payload = [
                'amount' => $row->Rate,
                'effective_from' => optional($row->EffectiveDate)
                    ? date('Y-m-d', strtotime($row->EffectiveDate))
                    : now()->format('Y-m-d'),
                'vat' => $row->VAT,
                'fixed_charge' => $row->FC,
                'estate_id' => $estateId,
                'tariff_id' => $tariffId,
                't_index' => $row->VersionNo,
                'status' => strtoupper($row->Status) === 'ACTIVE' ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($this->isDryRun()) {
                $this->line('DRY RUN: Tariff State => ' . json_encode($payload));
                continue;
            }

            TarrifState::create($payload);
        }

        $this->info('Tariff states migrated');
    }

    /* =========================================================
     | METERS
     * ========================================================= */

    protected function migrateMeters(): void
    {
        $this->info('Migrating meters...');

        $query = $this->legacy()
            ->table('Meters')
            ->orderBy('MeterNo');

        if ($this->estateFilter) {
            $query->where('BUID', $this->estateFilter);
        }

        $query
            ->chunk($this->chunkSize(), function ($rows) {

                foreach ($rows as $row) {

                    if (empty($row->MeterNo)) {
                        continue;
                    }

                    $existing = Meter::where('meterNo', $row->MeterNo)->first();

                    if ($existing) {
                        $this->meterMap[$row->MeterNo] = $existing->id;
                        continue;
                    }

                    $transformerId = $this->transformerMap[$row->TransID . '_' . $row->BUID] ?? null;

                    $estateId = $this->estateMap[$row->BUID] ?? null;

                    $payload = [
                        'estate_id' => $estateId,
                        'meterNo' => trim($row->MeterNo),
                        'status' => 2,
                        'meterModel' => $row->Model,
                        'AccountNo' => $row->AccountNo,
                        'isDualTariff' => $row->IsDual ? '1' : '0',
                        // 'NewTariffDualID' => $row->Tariff2,
                        // 'OldTariffID' => $row->OldTariff,
                        'NewSGC' => $row->SGC,
                        'OldSGC' => $row->OldSGC,
                        // 'NewTariffID' => $row->Tariff,
                        'NewSGCDual' => $row->SGC2,
                        // AFTER
                        'NewTariffID'     => $this->tariffMap[$row->Tariff    . '_' . $row->BUID] ?? null,
                        'NewTariffDualID' => $this->tariffMap[$row->Tariff2   . '_' . $row->BUID] ?? null,
                        'OldTariffID'     => $this->tariffMap[$row->OldTariff . '_' . $row->BUID] ?? null,
                        'transformer_id' => $transformerId,
                        'KRN1' => $row->KRN1,
                        'KRN2' => $row->KRN2,
                        'NeedKCT' => $row->NeedKCT ? '1' : '0',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if ($this->isDryRun()) {
                        $this->line('DRY RUN: Meter => ' . json_encode($payload));
                        continue;
                    }

                    $meter = Meter::create($payload);

                    $this->meterMap[$row->MeterNo] = $meter->id;
                }
            });

        $this->info('Meters migrated');
    }

    /* =========================================================
     | USER INFO USERS (ROLE 3)
     * ========================================================= */

    protected function migrateUserInfoUsers(): void
    {
        $this->info('Migrating UserInfo users...');

        $query = $this->legacy()
            ->table('UserInfo')
            ->orderBy('OperatorId');

        if ($this->estateFilter) {
            $query->where('BUID', $this->estateFilter)
                  ->orWhere('BusinessUnit', $this->estateFilter);
        }

        $query
            ->chunk($this->chunkSize(), function ($rows) {

                foreach ($rows as $row) {

                    $password = $this->reconstructPassword(
                        $row->OperatorId,
                        (int) $row->Pw_Len
                    );

                    [$firstName, $lastName] = $this->splitName($row->FullName);

                    $email = $this->sanitizeUniqueEmail($row->OperatorName);

                    $estateId = $this->estateMap[$row->BUID] ?? null;

                    $payload = [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role' => 3,
                        'estate_id' => $estateId,
                        'estate_name' => $row->BusinessUnit,
                        'status' => $row->Activated ? 2 : 0,
                        'can_login' => $row->Activated ? 1 : 0,
                        'created_at' => $row->OperatorDate ?? now(),
                        'updated_at' => now(),
                    ];

                    if ($this->isDryRun()) {
                        $this->line('DRY RUN: UserInfo User => ' . json_encode($payload));
                        continue;
                    }

                    User::create($payload);
                }
            });

        $this->info('UserInfo users migrated');
    }

    /* =========================================================
     | USER DATA USERS (ROLE 2)
     * ========================================================= */

    protected function migrateUserDataUsers(): void
    {
        $this->info('Migrating UserData users...');

        $query = $this->legacy()
            ->table('UserData')
            ->join('Meters', 'Meters.MeterNo', '=', 'UserData.MeterNo')
            ->select('UserData.*', 'Meters.BUID as meter_buid')
            ->orderBy('UserData.OperatorId');

        if ($this->estateFilter) {
            $query->where('Meters.BUID', $this->estateFilter);
        }

        $query
            ->chunk($this->chunkSize(), function ($rows) {

                foreach ($rows as $row) {

                    [$firstName, $lastName] = $this->splitName($row->FullName);

                    $email = $this->sanitizeUniqueEmail($row->OperatorName);

                    $meter = DB::table('meters')
                        ->where('meterNo', trim($row->MeterNo))
                        ->first();

                    $estateId = $meter->estate_id ?? null;
                    $accountNo = $meter->AccountNo ?? null;
                    $tariffId = $meter->NewTariffID ?? null;

                    $estate = null;

                    if ($estateId) {
                        $estate = Estate::find($estateId);
                    }

                    $payload = [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'phone' => $row->PhoneNumber,
                        'email' => $email,
                        'meterNo' => $row->MeterNo,
                        'password' => Hash::make($row->Pw),
                        'role' => 2,
                        'status' => $row->Activated ? 2 : 0,
                        'can_login' => $row->Activated ? 1 : 0,
                        'estate_id' => $estateId,
                        'estate_name' => $estate?->title,
                        'account_no' => $accountNo,
                        'tariffid' => $tariffId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if ($this->isDryRun()) {
                        $this->line('DRY RUN: UserData User => ' . json_encode($payload));
                        continue;
                    }

                    $user = User::create($payload);

                    if ($meter) {
                        DB::table('meters')
                            ->where('id', $meter->id)
                            ->update([
                                'user_id' => $user->id,
                            ]);
                    }
                }
            });

        $this->info('UserData users migrated');
    }

    /* =========================================================
     | ATTACH METERS TO USERS
     * ========================================================= */

    protected function attachMetersToUsers(): void
    {
        $this->info('Attaching meters to users...');

        $users = User::whereNotNull('meterNo')->get();

        foreach ($users as $user) {

            DB::table('meters')
                ->where('meterNo', $user->meterNo)
                ->update([
                    'user_id' => $user->id,
                ]);
        }

        $this->info('Meters attached');
    }

    /* =========================================================
     | HELPERS
     * ========================================================= */

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

    protected function sanitizeUniqueEmail(?string $email): ?string
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

    protected function importTransactions(int $limit = 5): void
    {
        $this->info("Importing {$limit} transaction(s)...");

        $transactions = DB::connection('mssql_legacy')
            ->table('Transactions')
            ->orderByDesc('TransactionDateTime')
            ->limit($limit)
            ->get();

        foreach ($transactions as $row) {

            $meter = Meter::where('meterNo', trim($row->MeterNo))
                ->first();

            if (!$meter || !$meter->user_id) {

                $this->warn(
                    "Skipping Transaction {$row->TransactionNo}: User not found for meter {$row->MeterNo}"
                );

                continue;
            }

            $exists = DB::table('transactions')
                ->where('trx_id', (string) $row->TransactionNo)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('transactions')->insert([
                'user_id' => $meter->user_id,
                'estate_id' => $meter->estate_id,

                'pay_type' => 'wallet',
                'service_type' => 'electricity',
                'service' => 'legacy_ecmi',

                'utility_id' => $row->MeterNo,
                'utility_amount' => $row->Units,

                'trx_id' => (string) $row->TransactionNo,
                'payment_ref' => $row->transref,

                'amount' => (float) $row->Amount,
                'fee' => (float) ($row->FC ?? 0),

                'unit_amount' => (float) ($row->Units ?? 0),

                'status' => (
                    $row->TransactionComplete
                    && strtoupper($row->status ?? 'N') === 'N'
                ) ? 1 : 0,

                'note' => $row->Reasons,

                'miscellaneous' => $row->Token,

                'action_payload' => json_encode([
                    'legacy_transaction_no' => $row->TransactionNo,
                    'meter_no' => $row->MeterNo,
                    'account_no' => $row->AccountNo,
                    'csp_client_id' => $row->CSPClientID,
                    'buid' => $row->BUID,
                    'token' => $row->Token,
                    'token_type' => $row->TokenType,
                    'vat' => $row->VAT,
                    'fc' => $row->FC,
                    'mmf' => $row->MMF,
                    'kva' => $row->KVA,
                ]),

                'vending_amount' => (float) ($row->CostOfUnits ?? 0),

                'service_rendered_at' => $row->TransactionDateTime,

                'created_at' => $row->TransactionDateTime,
                'updated_at' => now(),
            ]);
        }

        $this->info("Transactions imported successfully");
    }

        protected function migrateTransformers(): void
    {
        $this->info('Migrating transformers...');

        $query = $this->legacy()
            ->table('Transformers');

        if ($this->estateFilter) {
            $query->where('BUID', $this->estateFilter);
        }

        $rows = $query->get();

        foreach ($rows as $row) {

            $estateId = $this->estateMap[$row->BUID] ?? null;

            $existing = DB::table('transformers')
                ->where('Title', $row->Name)
                ->where('MDMeterSN', $row->MDMeterSN)
                ->first();

            if ($existing) {
                $this->transformerMap[$row->TransID . '_' . $row->BUID] = $existing->id;

                continue;
            }

            $payload = [
                'Estate_id' => $estateId,
                'estate' => Estate::find($estateId)?->title,
                'Title' => $row->Name,
                'Capacity' => $row->Capacity,
                'MDMeterSN' => $row->MDMeterSN,
                'CTRatio' => $row->CTRatio,
                'Multiplier' => $row->Multiplier,
                'Location' => $row->Location,
                'City' => $row->City,
                'State' => $row->State,
                'Status' => strtoupper($row->status1 ?? 'N') === 'N' ? 2 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($this->isDryRun()) {

                $this->line(
                    'DRY RUN: Transformer => ' .
                    json_encode($payload)
                );

                continue;
            }

            $transformer = DB::table('transformers')->insertGetId($payload);

            $this->transformerMap[$row->TransID . '_' . $row->BUID] = $transformer;
        }

        $this->info('Transformers migrated');
    }
}

// ```php
// protected $fillable = [
//     'first_name',
//     'last_name',
//     'phone',
//     'email',
//     'password',
//     'role',
//     'meterNo',
//     'estate_id',
//     'estate_name',
//     'account_no',
//     'tariffid',
//     'status',
//     'can_login',
// ];
// ```

// ---

// ## Meter Model

// ```php
// protected $fillable = [
//     'user_id',
//     'estate_id',
//     'meterNo',
//     'status',
//     'meterModel',
//     'AccountNo',
//     'isDualTariff',
//     'NewTariffDualID',
//     'OldTariffID',
//     'NewSGC',
//     'OldSGC',
//     'NewTariffID',
//     'NewSGCDual',
//     'KRN1',
//     'KRN2',
//     'NeedKCT',
// ];
// ```
