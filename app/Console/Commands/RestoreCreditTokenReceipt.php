<?php

namespace App\Console\Commands;

use App\Models\CreditToken;
use App\Models\Estate;
use App\Models\Meter;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestoreCreditTokenReceipt extends Command
{
    protected $signature = 'credit-token:restore-receipt
                            {--dry-run : Preview the records without writing to the database}';

    protected $description = 'Recreate transaction and credit token records from a credit token receipt';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Credit token receipt restore');
        $this->line('Enter the details exactly as they appear on the receipt.');
        $this->newLine();

        try {
            $receipt = $this->collectReceipt();
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $existingTransaction = Transaction::where('trx_id', $receipt['trx_id'])->first();
        $existingCreditToken = CreditToken::where('trx_id', $receipt['trx_id'])->first();

        if ($existingTransaction && $existingCreditToken) {
            $this->info("Both transaction and credit token records already exist for {$receipt['trx_id']}. Nothing to do.");

            return self::SUCCESS;
        }

        try {
            $user = $this->resolveUser($receipt['meter_no'], $receipt['customer_email']);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $meter = Meter::where('meterNo', $receipt['meter_no'])->first();

        $userId = $existingTransaction?->user_id ?? $user->id;
        $estateId = $existingTransaction?->estate_id
            ?? $meter?->estate_id
            ?? $user->estate_id;
        $estate = $estateId ? Estate::find($estateId) : null;
        $tariffId = $receipt['tariff_id']
            ?? $meter?->NewTariffID
            ?? $user->tariffid;

        $recordUser = User::find($userId) ?? $user;

        $transactionPayload = $this->buildTransactionPayload(
            $receipt,
            $userId,
            $estateId,
            $tariffId
        );

        $creditTokenPayload = $this->buildCreditTokenPayload(
            $receipt,
            $recordUser,
            $estateId,
            $estate?->title ?? $recordUser->estate_name,
            $tariffId
        );

        $this->newLine();
        $this->line('Resolved customer: '.trim(($recordUser->first_name ?? '').' '.($recordUser->last_name ?? ''))." (user_id: {$recordUser->id})");
        $this->line('Resolved estate: '.($estate?->title ?? $estateId ?? 'n/a'));
        $this->newLine();

        $this->table(
            ['Record', 'Status'],
            [
                ['transactions', $existingTransaction ? 'already exists (skip)' : ($dryRun ? 'will create' : 'create')],
                ['credit_tokens', $existingCreditToken ? 'already exists (skip)' : ($dryRun ? 'will create' : 'create')],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('[DRY RUN] Transaction payload:');
            $this->line(json_encode($transactionPayload, JSON_PRETTY_PRINT));

            if (! $existingCreditToken) {
                $this->newLine();
                $this->info('[DRY RUN] Credit token payload:');
                $this->line(json_encode($creditTokenPayload, JSON_PRETTY_PRINT));
            }

            $this->info('Dry run complete. No records were written.');

            return self::SUCCESS;
        }

        if (! $this->confirm('Create the missing records now?', true)) {
            $this->warn('Aborted. No records were written.');

            return self::SUCCESS;
        }

        DB::beginTransaction();

        try {
            if (! $existingTransaction) {
                $trx = new Transaction();
                $trx->timestamps = false;

                foreach ($transactionPayload as $key => $value) {
                    $trx->{$key} = $value;
                }

                $trx->save();

                $this->info("Transaction created (id: {$trx->id}, trx_id: {$trx->trx_id}).");
            }

            if (! $existingCreditToken) {
                $creditToken = new CreditToken();
                $creditToken->timestamps = false;

                foreach ($creditTokenPayload as $key => $value) {
                    $creditToken->{$key} = $value;
                }

                $creditToken->save();

                $this->info("Credit token created (id: {$creditToken->id}, trx_id: {$creditToken->trx_id}).");
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->error('Restore failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Receipt restore complete.');

        return self::SUCCESS;
    }

    protected function collectReceipt(): array
    {
        $receipt = [
            'trx_id' => $this->askRequired('TRX ID'),
            'meter_no' => $this->askRequired('Meter number'),
            'amount' => $this->askNumeric('Amount paid'),
            'token' => $this->askRequired('Token'),
            'unit' => $this->askNumeric('Unit (kWh)'),
            'vat' => $this->askNumeric('VAT amount', 0),
            'cost_of_unit' => $this->askNumeric('Cost of unit'),
            'tariff_amount' => $this->askNumeric('Tariff amount', 0),
            'date' => $this->askOptional('Receipt date/time (Y-m-d H:i:s)', now()->format('Y-m-d H:i:s')),
            'fee' => $this->askNumeric('Transaction fee', 0, false),
            'debt_charged' => $this->askNumeric('Debt charged', 0, false),
            'utility_charged' => $this->askNumeric('Utility charged', 0, false),
            'debt_owed' => $this->askNumeric('Debt owed', 0, false),
            'service_charge_owed' => $this->askNumeric('Service charge owed', 0, false),
            'kct_token1' => $this->askOptional('KCT token 1'),
            'kct_token2' => $this->askOptional('KCT token 2'),
            'tariff_id' => $this->askOptional('Tariff ID (blank to use the meter tariff)'),
            'receiver_meter_no' => $this->askOptional('Receiver meter number (blank if not paying for others)'),
            'customer_email' => $this->askOptional('Customer email (blank to identify by meter number)'),
        ];

        try {
            $receipt['date'] = Carbon::parse($receipt['date']);
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException('Invalid receipt date/time: '.$receipt['date']);
        }

        $receipt['tariff_id'] = $receipt['tariff_id'] !== null && $receipt['tariff_id'] !== ''
            ? $receipt['tariff_id']
            : null;

        return $receipt;
    }

    protected function resolveUser(string $meterNo, ?string $email): User
    {
        if ($email) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                throw new \InvalidArgumentException("No user found with email {$email}.");
            }

            return $user;
        }

        $meter = Meter::where('meterNo', $meterNo)->first();

        if ($meter && $meter->user_id) {
            $user = User::find($meter->user_id);

            if ($user) {
                return $user;
            }
        }

        $user = User::where('meterNo', $meterNo)->first();

        if (! $user) {
            throw new \InvalidArgumentException(
                "Unable to resolve a user for meter {$meterNo}. Re-run and supply the customer email."
            );
        }

        return $user;
    }

    protected function buildTransactionPayload(array $receipt, int $userId, $estateId, $tariffId): array
    {
        $isForOthers = $receipt['receiver_meter_no'] !== null && $receipt['receiver_meter_no'] !== '';

        return [
            'user_id' => $userId,
            'estate_id' => $estateId,
            'pay_type' => 'manual',
            'service_type' => $isForOthers ? 'credit_token_others' : 'credit_token',
            'service' => $isForOthers ? 'CREDIT TOKEN PURCHASE(OTHERS)' : 'CREDIT TOKEN PURCHASE',
            'trx_id' => $receipt['trx_id'],
            'tariff_id' => $tariffId,
            'amount' => round($receipt['amount'], 2),
            'fee' => (string) round($receipt['fee'], 2),
            'unit_amount' => round($receipt['cost_of_unit'], 2),
            'status' => 2,
            'migrated' => true,
            'vending_amount' => round($receipt['cost_of_unit'], 2),
            'breakdown' => [
                'arrearsOwed' => (string) round($receipt['debt_charged'], 2),
                'utilityOwed' => (string) round($receipt['utility_charged'], 2),
                'serviceFee' => (string) round($receipt['fee'], 2),
                'vatAmount' => (string) round($receipt['vat'], 2),
                'tariffAmount' => (string) round($receipt['tariff_amount'], 2),
                'vendingAmount' => (string) round($receipt['cost_of_unit'], 2),
                'unit' => (string) round($receipt['unit'], 2),
            ],
            'action_payload' => json_encode([
                'source' => 'receipt_restore',
                'debt_breakdown' => [
                    'debt_owed' => (string) round($receipt['debt_owed'], 2),
                    'service_charge_owed' => (string) round($receipt['service_charge_owed'], 2),
                ],
            ]),
            'created_at' => $receipt['date'],
            'updated_at' => $receipt['date'],
        ];
    }

    protected function buildCreditTokenPayload(
        array $receipt,
        User $user,
        $estateId,
        ?string $estateName,
        $tariffId
    ): array {
        $kctTokens = array_values(array_filter([
            $receipt['kct_token1'],
            $receipt['kct_token2'],
        ], fn ($token) => $token !== null && $token !== ''));

        return [
            'trx_id' => $receipt['trx_id'],
            'user_id' => $user->id,
            'meterNo' => $receipt['meter_no'],
            'token' => $receipt['token'],
            'amount' => round($receipt['cost_of_unit'], 2),
            'amount_charged' => round($receipt['amount'], 2),
            'vat' => round($receipt['vat'], 2),
            'vatAmount' => (string) round($receipt['vat'], 2),
            'costOfUnit' => (string) round($receipt['cost_of_unit'], 2),
            'tariffPerKWatt' => null,
            'unitkwh' => (string) round($receipt['unit'], 2),
            'tariff_amount' => (string) round($receipt['tariff_amount'], 2),
            'tariff_id' => $tariffId,
            'estate_id' => $estateId,
            'estate_name' => $estateName,
            'customer_email' => $user->email,
            'status' => 2,
            'fee' => round($receipt['fee'], 2),
            'kct_tokens' => $kctTokens ? implode(',', $kctTokens) : null,
            'receiver_meterNo' => $receipt['receiver_meter_no'] ?: null,
            'created_at' => $receipt['date'],
            'updated_at' => $receipt['date'],
        ];
    }

    protected function askRequired(string $question): string
    {
        while (true) {
            $answer = trim((string) $this->ask($question));

            if ($answer !== '') {
                return $answer;
            }

            $this->error('This field is required.');

            if (! $this->input->isInteractive()) {
                throw new \InvalidArgumentException("Missing required field: {$question}");
            }
        }
    }

    protected function askOptional(string $question, $default = null): ?string
    {
        $answer = $this->ask($question, $default);

        if ($answer === null) {
            return $default;
        }

        $answer = trim((string) $answer);

        return $answer === '' ? $default : $answer;
    }

    protected function askNumeric(string $question, $default = null, bool $required = true): float
    {
        while (true) {
            $answer = $required && $default === null
                ? $this->ask($question)
                : $this->ask($question, $default);

            if (($answer === null || trim((string) $answer) === '')) {
                if (! $required) {
                    return (float) ($default ?? 0);
                }

                $this->error('A number is required.');

                if (! $this->input->isInteractive()) {
                    throw new \InvalidArgumentException("Missing required number: {$question}");
                }

                continue;
            }

            $value = $this->normalizeNumber($answer);

            if (is_numeric($value)) {
                return (float) $value;
            }

            $this->error('Please enter a valid number.');

            if (! $this->input->isInteractive()) {
                throw new \InvalidArgumentException("Invalid number for {$question}: {$answer}");
            }
        }
    }

    protected function normalizeNumber($value): string
    {
        return str_replace([',', '₦', ' '], '', trim((string) $value));
    }
}
