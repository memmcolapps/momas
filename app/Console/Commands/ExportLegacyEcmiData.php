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
                            {--chunk=1000}';

    protected $description = 'Export MSSQL legacy data into JSON files';

    protected function legacy()
    {
        return DB::connection('mssql_legacy');
    }

    public function handle(): int
    {
        $estate = $this->argument('estate');

        $path = storage_path('app/legacy-export');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->info("Exporting legacy data...");

        $this->exportEstates($path, $estate);
        $this->exportTariffs($path, $estate);
        $this->exportTariffStates($path, $estate);
        $this->exportMeters($path, $estate);
        $this->exportUserInfo($path, $estate);
        $this->exportUserData($path, $estate);

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
                'password_hash' => Hash::make($password), // 🔥 IMPORTANT CHANGE
                'activated' => $row->Activated,
                'can_login' => $row->Activated ? 1 : 0,
                'created_at' => $row->OperatorDate ?? now(),
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
}
