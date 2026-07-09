<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportLegacyEcmiData extends Command
{
    protected $signature = 'legacy:export
                        {estate?}
                        {--chunk=1000}
                        {--module= : Run a single export module only (subaccount)}';

    protected $description = 'Export MSSQL legacy data into JSON files';

    protected function legacy()
    {
        return DB::connection('mssql_legacy');
    }

    public function handle()
    {
        $estate = $this->argument('estate');

        $path = storage_path('app/legacy-export');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Single-module mode
        if ($module = $this->option('module')) {
            return match ($module) {
                'subaccount'            => $this->exportPaystackSubAccounts($path, $estate) ?? self::SUCCESS,
                'utility-subaccount'    => $this->exportUtilitySubAccounts($path, $estate) ?? self::SUCCESS,
                default                 => $this->error("Unknown module: {$module}") ?? self::FAILURE,
            };
        }

        $this->info("Exporting legacy data...");

        $this->exportEstates($path, $estate);
        $this->exportPaystackSubAccounts($path, $estate);
        $this->exportTransformers($path, $estate);
        $this->exportTariffs($path, $estate);
        $this->exportTariffStates($path, $estate);
        $this->exportMeters($path, $estate);
        $this->exportUserInfo($path, $estate);
        $this->exportUserData($path, $estate);
        $this->exportCustomers($path, $estate);
        $this->exportTransactions($path, $estate);
        $this->exportUtilitySubAccounts($path, $estate);

        $this->info("Export completed successfully");

        return self::SUCCESS;
    }

    protected function write(string $file, array $data, string $path)
    {
        file_put_contents(
            $path . '/' . $file,
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }

    /* ================= ESTATES ================= */

    protected function exportEstates($path, $estate)
    {
        $query = $this->legacy()->table('BusinessUnit');

        if ($estate) {
            $query->where('BUID', $estate)->orWhere('Name', $estate);
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
        }

        $rows = $query->orderBy('OperatorId')->get();

        $export = [];

        foreach ($rows as $row) {

            // 🔐 RECONSTRUCT ORIGINAL PASSWORD (same logic as migration)
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
        }

        $rows = $query->get()->toArray();

        $this->write('subaccounts.json', $rows, $path);

        $this->info("Exported ".count($rows)." subaccounts");
    }
}
