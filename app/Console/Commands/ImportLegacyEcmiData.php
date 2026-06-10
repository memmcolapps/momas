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

    public function handle(): int
    {
        $path = $this->option('path') ?: storage_path('app/legacy-export');

        $this->info("Importing from: $path");

        DB::beginTransaction();

        try {
            $this->importEstates($path);
            $this->importTariffs($path);
            $this->importTariffStates($path);
            $this->importMeters($path);
            $this->importUserInfo($path);
            $this->importUserData($path);
            $this->attachMeters();

            DB::commit();

            $this->info("Import completed successfully");

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

    /* ================= ESTATES ================= */

    protected function importEstates($path)
    {
        $rows = $this->read('estates.json', $path);

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
                'created_at' => now(),
                'updated_at' => now(),
            ];

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

            $estateId = $this->estateMap[$row->BUID] ?? null;

            $payload = [
                'title' => $row->Description,
                'tariff_index' => $row->TariffID,
                'estate_id' => $estateId,
                'status' => $row->status1 === 'N' ? 1 : 0,
            ];

            if ($this->isDryRun()) {
                $this->preview('Tarrif', $payload);
                $this->tariffMap[$row->TariffID . '_' . $row->BUID] = Str::uuid();
                continue;
            }

            $tariff = Tariff::create($payload);

            $this->tariffMap[$row->TariffID . '_' . $row->BUID] = $tariff->id;
        }
    }

    /* ================= TARIFF STATES ================= */

    protected function importTariffStates($path)
    {
        $rows = $this->read('tariff_states.json', $path);

        foreach ($rows as $row) {

            $estateId = $this->estateMap[$row->BUID] ?? null;
            $tariffId = $this->tariffMap[$row->TariffID . '_' . $row->BUID] ?? null;

            $payload = [
                'amount' => $row->Rate,
                'effective_from' => $row->EffectiveDate ?? now(),
                'vat' => $row->VAT,
                'fixed_charge' => $row->FC,
                'estate_id' => $estateId,
                'tariff_id' => $tariffId,
                't_index' => $row->VersionNo,
                'status' => strtoupper($row->Status) === 'ACTIVE' ? 1 : 0,
            ];

            if ($this->isDryRun()) {
                $this->preview('Tarrif State', $payload);
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

            $payload = [
                'estate_id' => $estateId,
                'meterNo' => $row->MeterNo,
                'status' => 2,
                'meterModel' => $row->Model,
                'AccountNo' => $row->AccountNo,
                'NewTariffID' => $row->Tariff,
                'OldTariffID' => $row->OldTariff,
            ];

            if ($this->isDryRun()) {
                $this->meterMap[$row->MeterNo] = Str::uuid();
                $this->preview('Meter', $payload);
                continue;
            }

            $meter = Meter::create($payload);

            $this->meterMap[$row->MeterNo] = $meter->id;
        }
    }

    /* ================= USERS ================= */

    protected function importUserInfo($path)
    {
        $rows = $this->read('user_info.json', $path);

        foreach ($rows as $row) {

            // [$first, $last] = $this->split($row->first_name ?? null . ' ' . $row->last_name ?? null);
            // dump($row);

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
            ];

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
}
