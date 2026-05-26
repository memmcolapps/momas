<?php

namespace App\Models;

use App\Constants\ServiceTypeConstants;
use App\Constants\TransactionConstants;
use App\Events\MeterTokenGenerated;
use App\Services\PaystackPaymentService;
use App\Services\TokenGenerationService;
use App\Services\VatCalculator;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Meter extends Model
{
    use HasFactory; //, SoftDeletes;

    protected $fillable = [

        'user_id',
        'meterNo',
        'meterType',
        'payType',
        'meterModel',
        'AccountNo',
        'estate_id',
        'TransformerID',
        'isDualTariff',
        'NewSGC',
        'OldSGC',
        'NewTariffID',
        'NewTariffDualID',
        'OldTariffDualID',
        'OldTariffID',
        'NewSGCDual',
        'OldSGCDual',
        'NewTariffDual',
        'OldTariffDual',
        'KRN1',
        'KRN2',
        'NeedKCT',
        'CreditTypeID',
        'AddedBy'







    ];

    protected $casts = [
        'user_id'=> 'integer',
        'debit' => 'integer',
        'credit' => 'integer',
        'balance' => 'integer',
        'amount' => 'integer',
        'fee' => 'integer',
        'from_user_id' => 'integer',
        'main_wallet' => 'integer',
        'status' => 'integer',
        'e_charges' => 'integer',
        'charge' => 'integer',
        'resolve' => 'integer',
    ];

//    public function user()
//    {
//        return $this->belongsTo(User::class);
//    }
//
//
//    public function estate()
//    {
//        return $this->belongsTo(Estate::class);
//    }

    public function transformer()
    {
        return $this->belongsTo(Transformer::class);
    }

    public function credit_token()
    {
        return $this->belongsTo(CreditToken::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class, 'estate_id', 'id');
    }

    public function isActive()
    {
        return $this->status !== 0;
    }

    /**
     * Calculate token values based on tariff and transaction amount.
     *
     * This method extracts the calculation logic for determining
     * service fees, estate charges, VAT, and final unit values.
     *
     * @param int $tariff_id The ID of the tariff to use for calculations
     * @param \App\Models\Transaction $trx The transaction object containing the amount
     * @return array Array of calculated values including service fees, charges, and unit details
     * @throws \Exception When the amount is too small after deductions or unit is less than 0.1KWh
     */
    public function calculateTokenValues(int $tariff_id, Transaction $trx): array
    {
        $tariffState = TarrifState::where('tariff_id', $tariff_id)->where('status', 2)->first();
        $tariffAmount = $tariffState->amount ?? 0;
        $vat = $tariffState->vat ?? 0;
        $fixedCharge = $tariffState->fixed_charge ?? 0;

        // NEW CALCULATION FLOW:
        // [1] 2.5% Service Fee
        $amount = $trx->vending_amount ?? $trx->amount;
        $percn = (2.5 / 100) * (int)$amount;
        $afterServiceFee = $amount - $percn;

        // [2] Estate Service Charge
        $est = Estate::where('id', $this->estate_id)->first();
        if ($est->charge_fee_flat != null) {
            $estateFee = $est->charge_fee_flat;
        } else if ($est->charge_fee_precent != null) {
            $estateFee = ($est->charge_fee_precent / 100) * (int)$amount;
        } else {
            $estateFee = 0;
        }
        $afterEstateFee = $afterServiceFee - $estateFee;

        // [3] Tariff Fixed Charge
        $afterFixedCharge = $afterEstateFee - $fixedCharge;

        // Validate that amount after deductions is not negative or too small
        if ($afterFixedCharge <= 0) {
            $minimumRequired = $percn + $estateFee + $fixedCharge + 10; // Adding small buffer
            throw new Exception('Amount too small! After deducting service fee (NGN ' . number_format($percn, 2) .
                '), estate fee (NGN ' . number_format($estateFee, 2) .
                '), and fixed charge (NGN ' . number_format($fixedCharge, 2) .
                '), the remaining amount would be NGN ' . number_format($afterFixedCharge, 2) .
                '. Please enter at least NGN ' . number_format($minimumRequired, 2) . ' to proceed.');
        }

        // [4] VAT Calculation on remaining amount
        $calculator = new VatCalculator();
        $params = [
            'amountText' => $afterFixedCharge,
            'tariffAmount' => $tariffAmount,
            'utilitiesAmount' => 0,
            'vat' => $vat,
        ];

        $vatAmount = $calculator->calculateVatAmount($params);
        $vending_amount = $calculator->calculateCostOfUnit($params);
        $unit = $calculator->calculateTariffAmountPerKWatt($params);

        if ($unit < 0.1) {
            throw new Exception('Kwh purchase cannot be less than 0.1KWh. Please increase the amount entered.');
        }


        return [
            'tariffAmount' => $tariffAmount,
            'vat' => $vat,
            'fixedCharge' => $fixedCharge,
            'serviceFee' => $percn,
            'afterServiceFee' => $afterServiceFee,
            'estateFee' => $estateFee,
            'afterEstateFee' => $afterEstateFee,
            'afterFixedCharge' => $afterFixedCharge,
            'vatAmount' => $vatAmount,
            'vending_amount' => $vending_amount,
            'unit' => $unit,
        ];
    }

    /**
     * Generate a new token for the meter after payment verification.
     *
     * This method handles the token generation process after a successful payment.
     * It validates the meter status, verifies the transaction via Paystack,
     * generates the meter token, and creates the necessary records.
     *
     * @param int $tariff_id The ID of the tariff to use for token generation
     * @param int $unit The number of units to purchase
     * @param string $trx_id The transaction reference ID
     * @param float $vat The VAT amount applied to the transaction
     * @param float $vending_amount The total vending amount paid
     * @param string $email The customer's email address for notifications
     * @param string $verify Verification method: "verify" (Paystack verify), "poll" (Paystack poll), or "null" (skip verification)
     * @return \Illuminate\Http\JsonResponse|void Returns JSON response on failure, void on success
     * @throws \Exception Thrown when: meter is inactive, transaction already completed, payment verification fails, or token generation fails
     */
    public function getNewToken(
        $tariff_id,
        $trx_id,
        $verify="verify",
        $receiver_meterNo='',
        $action='momas_meter'
    ) {

        $other_meter = null;

        if ($receiver_meterNo) {
            $other_meter = self::where('meterNo', $receiver_meterNo)
                // ->where('estate_id', $this->estate_id)
                ->first();

            if (!$other_meter) {

                // dd($receiver_meterNo);
                throw new Exception('You Cannot Vend for this Meter');
            }
        }

        $user = User::where('id', $this->user_id)->firstOrFail();


        try {
            DB::transaction(function () use (
                $tariff_id,
                $trx_id,
                $verify,
                $user,
                $receiver_meterNo,
                $other_meter,
                $action
            ) {
                $trx = Transaction::where('trx_id', $trx_id)
                    ->firstOrFail();

                // Calculate token values using the dedicated method

                $calculatedValues = $this->calculateTokenValues($tariff_id, $trx);

                // Extract calculated values
                $tariffAmount = $calculatedValues['tariffAmount'];
                $vat = $calculatedValues['vat'];
                $fixedCharge = $calculatedValues['fixedCharge'];
                $percn = $calculatedValues['serviceFee'];
                $afterServiceFee = $calculatedValues['afterServiceFee'];
                $estateFee = $calculatedValues['estateFee'];
                $afterEstateFee = $calculatedValues['afterEstateFee'];
                $afterFixedCharge = $calculatedValues['afterFixedCharge'];
                $vatAmount = $calculatedValues['vatAmount'];
                $vending_amount = $calculatedValues['vending_amount'];
                $unit = $calculatedValues['unit'];


                $email = $user->email;

                $service = $other_meter ? "CREDIT TOKEN PURCHASE(OTHERS)" : "CREDIT TOKEN PURCHASE";
                $service_type = $other_meter ? ServiceTypeConstants::CREDIT_TOKEN_OTHERS : ServiceTypeConstants::CREDIT_TOKEN;


                $meter = $other_meter ?? $this;


                if (! $meter->isActive() || ($receiver_meterNo && ! $this->isActive())) {
                    throw new Exception("Meter is unable to carrying out operations");
                }


                if ($trx->status === 2) {
                    throw new Exception ("Transaction already completed please restart a new transaction to generate token");
                }

                $paystack_engine = new PaystackPaymentService();

                $verifier_engine = match ($verify) {
                    "verify" => fn($arg) => $paystack_engine->verifyTransaction($arg),
                    "poll" => fn($arg) => $paystack_engine->pollTransactionStatus($arg),
                    "null" => fn($arg) => [
                        'is_successful' => true,
                        'status' => true,
                        'data' => [],
                    ],
                };


                if ($trx->status === 0) {
                    $verify = $verifier_engine($trx_id);

                    if (! $verify['is_successful']) {
                        Logger::error('verify_transaction failed', [
                            'message' => 'Buggy verify transaction failure - verify should never fail at this point',
                            'trx' => $trx,
                            'trx_id' => $trx->id,
                        ]);

                        throw new Exception("Transaction Failed");
                    }
                }

                if ($trx->status === 1) {
                    Logger::error('verify_transaction failed', [
                        'message' => 'Payment failed',
                        'trx' => $trx,
                        'trx_id' => $trx->id,
                    ]);

                    throw new Exception("Transaction Failed");
                }


                $need_kct = $meter->NeedKCT;

                $tariff_index = Tariff::where('id', $tariff_id)->first()->tariff_index ?? null;

                $token_gen = TokenGenerationService::generateMeterToken($meter, $tariff_index, $unit, $meter->NeedKCT);

                Transaction::where('trx_id', $trx_id)->update([
                    'service' => $service,
                    'service_type' => $service_type,
                    'tariff_id' => $tariff_id,
                    'unit_amount' => $vending_amount,
                    // 'vat' => $vatAmount,
                ]);


                if ( ! $token_gen['success']) {
                    // dump('Failed Meter: 317');
                    Transaction::where('trx_id', $trx_id)->update([
                        'note' => 'token generation failed',
                        'status' => 3,
                        'wallet_creditted' => $vending_amount,
                    ]);


                    if ($action == 'momas_meter') {
                        User::where('id', $this->user_id)->first()->creditWallet($trx->vending_amount ?? $trx->amount);
                    }

                    throw new Exception("Vending server not connected, Retry again on transaction history");
                }


                $token = $token_gen['data']['token'];

                $tariffState = TarrifState::where('tariff_id', $tariff_id)->where('status', 2)->first();
                $tariffAmount = $tariffState->amount ?? 0;

                $cdt = CreditToken::updateOrCreate([
                    'trx_id' => $trx_id,
                    'user_id' => $this->user_id,
                    'meterNo' => $this->meterNo,
                ],
                    [
                    'amount' => $vending_amount,
                    'amount_charged' => $trx->amount,
                    'customer_email' => $email,
                    'receiver_meterNo' => $receiver_meterNo,
                    'unitkwh' => $unit,
                    'vat' => $vat,
                    'estate_id' => $this->estate_id,
                    'estate_name' => $user->estate_name,
                    'token' => $token,
                    'status' => 2,
                    'vatAmount' => $vat,
                    'tariff_amount' => $tariffAmount,
                    'tariff_id' => $tariff_id
                ]);

                $kct_token1 = $kct_token2 = null;
                if ($need_kct) {
                    $kct_tokens = $token_gen['data']['kct_token'];
                    $kct_token1 = $kct_tokens[0];
                    $kct_token2 = $kct_tokens[1];

                    MeterToken::create([
                        'user_id' => $this->user_id,
                        'trx_id' => $trx_id,
                        'meterNo' => $this->meterNo,
                        'token' => $token,
                        'amount' => $total_paid ?? 0,
                        'unit' => $unit,
                        'kct_tokens' => $kct_tokens[0] . "," . $kct_tokens[1],
                        'vat' => $vat,
                        'estate_id' => $this->estate_id,
                        'status' => 2,
                        'receiver_meterNo' => $receiver_meterNo,
                    ]);
                }

                Transaction::where('trx_id', $trx_id)->update(['status' => '2']);

                MeterTokenGenerated::dispatch(
                    $cdt,
                    $trx->amount,
                    $kct_token1,
                    $kct_token2,
                    $receiver_meterNo,
                    $receiver_meterNo ? 'CREDIT TOKEN PURCHASE(OTHERS)' : null
                );

            });
        } catch (Exception $e) {

            $trx = Transaction::where('trx_id', $trx_id)->first();
            $amount = $trx->vending_amount ?? $trx->amount;

            if ($action == 'momas_meter') {
                User::where('id', $this->user_id)->first()->creditWallet($amount);

                Transaction::where('trx_id', $trx_id)->update([
                    'wallet_creditted' => $amount,
                    'status' => 3,
                ]);
            }

            throw $e;
        }
    }

    /**
     * Generate a new KCT (Key Change Token) for the meter.
     *
     * This method handles the KCT token generation process.
     * It validates the meter status, verifies the transaction via Paystack,
     * generates the KCT tokens, and updates the KctToken record.
     *
     * @param string $trx_id The transaction reference ID
     * @param string $meterNo The meter number
     * @param string $sgc The SGC (Standard Group Classification)
     * @param string $tosgc The target SGC
     * @param string $ti The TI (Token Identifier)
     * @param int $toti The total TI (defaults to 1)
     * @param string $verify Verification method: "verify" (Paystack verify), "poll" (Paystack poll), or "null" (skip verification)
     * @return bool Returns true on success
     * @throws \Exception Thrown when: meter is inactive, transaction already completed, payment verification fails, or token generation fails
     */
    public function getNewKctToken($trx_id, $meterNo, $sgc, $tosgc, $ti, $toti = 1, $verify = "null")
    {
        DB::transaction(function () use ($trx_id, $meterNo, $sgc, $tosgc, $ti, $toti, $verify) {
            if ($this->status === 0) {
                throw new Exception("Meter is unable from carrying out operations");
            }

            $trx = Transaction::where('trx_id', $trx_id)->firstOrFail();

            if ($trx->status === 2) {
                throw new Exception("Transaction already completed please restart a new transaction to generate token");
            }

            $paystack_engine = new PaystackPaymentService();

            $verifier_engine = match ($verify) {
                "verify" => fn($arg) => $paystack_engine->verifyTransaction($arg),
                "poll" => fn($arg) => $paystack_engine->pollTransactionStatus($arg),
                "null" => fn($arg) => [
                    'is_successful' => true,
                    'status' => true,
                    'data' => [],
                ],
            };

            if ($trx->status === 0) {
                $verify_result = $verifier_engine($trx_id);

                if (!$verify_result['is_successful']) {
                    throw new Exception("Transaction Failed");
                }
            }

            if ($trx->status === 1) {
                throw new Exception("Transaction Failed");
            }

            // Generate KCT token using the TokenGenerationService
            $kct_result = TokenGenerationService::generateKctToken(
                $this,
                $meterNo,
                $sgc,
                $tosgc,
                $ti,
                $toti
            );

            Transaction::where('trx_id', $trx_id)->update([
                'service' => "KCT TOKEN PURCHASE",
                'service_type' => "kct_token",
            ]);


            if (!$kct_result['success']) {
                Transaction::where('trx_id', $trx_id)->update([
                    'note' => 'KCT token generation failed: ' . ($kct_result['error'] ?? 'Unknown error'),
                    'status' => 3,
                ]);
                throw new Exception($kct_result['error'] ?? 'KCT token generation failed');
            }


            // Update KctToken record with the generated tokens
            KctToken::where('trx_id', $trx_id)->update([
                'kct_token1' => $kct_result['data']['kct_token1'],
                'kct_token2' => $kct_result['data']['kct_token2'],
                'kct_tokens' => implode(',', $kct_result['data']),
                'status' => 2
            ]);

            // Update transaction status
            Transaction::where('trx_id', $trx_id)->update(['status' => 2]);
        });

        return true;
    }

    /**
     * Generate a new clear credit token for the meter.
     *
     * This method handles the clear credit token generation process.
     * It validates the meter status, verifies the transaction via Paystack,
     * generates the clear credit token and KCT tokens, and creates the necessary records.
     *
     * @param int $tariff_id The ID of the tariff to use for token generation
     * @param string $trx_id The transaction reference ID
     * @param string $email The customer's email address for notifications
     * @param string $verify Verification method: "verify" (Paystack verify), "poll" (Paystack poll), or "null" (skip verification)
     * @return bool Returns true on success
     * @throws \Exception Thrown when: meter is inactive, transaction already completed, payment verification fails, or token generation fails
     */
    public function getNewClearCreditToken($tariff_id, $trx_id, $email = null, $verify = "null")
    {
        dump("Meter: 387");
        DB::transaction(function () use ($tariff_id, $trx_id, $email, $verify) {
            // Validate meter status
            if ($this->status === 0) {
                throw new Exception("Meter is unable from carrying out operations");
            }

            // Get user and set email
            $user = User::where('id', $this->user_id)->firstOrFail();
            $email = (! $email || $email === 'null')
                ? $user->email
                : $email;

            // Get transaction
            $trx = Transaction::where('trx_id', $trx_id)->firstOrFail();

            if ($trx->status === 2) {
                throw new Exception("Transaction already completed please restart a new transaction to generate token");
            }

            // Setup Paystack verification engine
            $paystack_engine = new PaystackPaymentService();

            $verifier_engine = match ($verify) {
                "verify" => fn($arg) => $paystack_engine->verifyTransaction($arg),
                "poll" => fn($arg) => $paystack_engine->pollTransactionStatus($arg),
                "null" => fn($arg) => [
                    'is_successful' => true,
                    'status' => true,
                    'data' => [],
                ],
            };

            // Verify transaction if needed
            if ($trx->status === 0) {
                $verify_result = $verifier_engine($trx_id);

                if (!$verify_result['is_successful']) {
                    throw new Exception("Transaction Failed");
                }
            }

            if ($trx->status === 1) {
                throw new Exception("Transaction Failed");
            }

            // Get tariff index
            $tariff = Tariff::where('id', $tariff_id)->first();
            $tariff_index = $tariff->tariff_index ?? null;

            // Generate clear credit token
            dump("Meter: 438", "tariff_id -> " . $tariff_id);
            $clear_credit_result = TokenGenerationService::generateClearCreditToken($this, $tariff_index);

            // Update transaction
            Transaction::where('trx_id', $trx_id)->update([
                'service' => "CLEAR CREDIT TOKEN PURCHASE",
                'service_type' => "clear_credit_token",
                'tariff_id' => $tariff_id,
            ]);

            if (!$clear_credit_result['success']) {
                Transaction::where('trx_id', $trx_id)->update([
                    'note' => 'Clear credit token generation failed: ' . ($clear_credit_result['error'] ?? 'Unknown error'),
                    'status' => 3,
                ]);

                throw new Exception($clear_credit_result['error'] ?? 'Clear credit token generation failed');
            }

            $clear_credit_token = $clear_credit_result['data']['token'];

            // Save ClearcreditToken record
            ClearcreditToken::updateOrCreate([
                'trx_id' => $trx_id,
                'user_id' => $this->user_id,
                'meterNo' => $this->meterNo,
            ],
                [
                'amount' => $trx->amount ?? 0,
                'vat' => $trx->vat ?? 0,
                'estate_id' => $this->estate_id,
                'estate_name' => $user->estate_name,
                'tariff_id' => $tariff_id,
                'token' => $clear_credit_token,
                'status' => 2
            ]);

            // Update transaction status
            Transaction::where('trx_id', $trx_id)->update(['status' => 2]);
        });

        return true;
    }

    /**
     * Generate a new tamper token for the meter.
     *
     * This method handles the tamper token generation process.
     * It validates the meter status, verifies the transaction via Paystack,
     * generates the tamper token, and creates the necessary records.
     *
     * @param int $tariff_id The ID of the tariff to use for token generation
     * @param string $trx_id The transaction reference ID
     * @param float $vending_amount The total vending amount paid
     * @param string $email The customer's email address for notifications
     * @param string $verify Verification method: "verify" (Paystack verify), "poll" (Paystack poll), or "null" (skip verification)
     * @return bool Returns true on success
     * @throws \Exception Thrown when: meter is inactive, transaction already completed, payment verification fails, or token generation fails
     */
    public function getNewTamperToken($tariff_id, $trx_id, $vending_amount = 0, $email = null, $verify = "null")
    {
        DB::transaction(function () use ($tariff_id, $trx_id, $vending_amount, $email, $verify) {
            // Validate meter status
            if ($this->status === 0) {
                throw new Exception("Meter is unable from carrying out operations");
            }

            // Get user and set email
            $user = User::where('id', $this->user_id)->firstOrFail();
            $email = (! $email || $email === 'null')
                ? $user->email
                : $email;

            // Get transaction
            $trx = Transaction::where('trx_id', $trx_id)->firstOrFail();

            if ($trx->status === 2) {
                throw new Exception("Transaction already completed please restart a new transaction to generate token");
            }

            // Setup Paystack verification engine
            $paystack_engine = new PaystackPaymentService();

            $verifier_engine = match ($verify) {
                "verify" => fn($arg) => $paystack_engine->verifyTransaction($arg),
                "poll" => fn($arg) => $paystack_engine->pollTransactionStatus($arg),
                "null" => fn($arg) => [
                    'is_successful' => true,
                    'status' => true,
                    'data' => [],
                ],
            };

            // Verify transaction if needed
            if ($trx->status === 0) {
                $verify_result = $verifier_engine($trx_id);

                if (!$verify_result['is_successful']) {
                    throw new Exception("Transaction Failed");
                }
            }

            if ($trx->status === 1) {
                throw new Exception("Transaction Failed");
            }

            // Get tariff index
            $tariff = Tariff::where('id', $tariff_id)->first();
            $tariff_index = $tariff->tariff_index ?? null;

            // Generate tamper token
            $tamper_result = TokenGenerationService::generateTamperToken($this, $tariff_index);

            // Update transaction
            Transaction::where('trx_id', $trx_id)->update([
                'service' => "TAMPER TOKEN PURCHASE",
                'service_type' => "meter",
                'tariff_id' => $tariff_id,
            ]);

            if (!$tamper_result['success']) {
                Transaction::where('trx_id', $trx_id)->update([
                    'note' => 'Tamper token generation failed: ' . ($tamper_result['error'] ?? 'Unknown error'),
                    'status' => 3,
                ]);
                throw new Exception($tamper_result['error'] ?? 'Tamper token generation failed');
            }

            $tamper_token = $tamper_result['data']['token'];

            // Save TamperToken record
            TamperToken::updateOrCreate([
                'trx_id' => $trx_id,
                'user_id' => $this->user_id,
                'meterNo' => $this->meterNo,
            ],
                [
                'amount' => $vending_amount,
                'estate_id' => $this->estate_id,
                'token' => $tamper_token,
                'status' => 2
            ]);

            // Update transaction status
            Transaction::where('trx_id', $trx_id)->update(['status' => 2]);
        });

        return true;
    }

    /**
     * Initiate clear credit token purchase payment.
     *
     * This method handles the payment initiation for clear credit token purchase.
     * It uses PaystackPaymentService to make payment, creates the transaction
     * with action_payload, and returns the payment authorization URL.
     *
     * @param int $tariff_id The ID of the tariff to use
     * @param float $amount The amount to pay
     * @param string $email The customer's email address
     * @param string $sub_account The Paystack subaccount for the agent
     * @return array Returns payment initialization data with authorization URL
     * @throws \Exception Thrown when payment initialization fails
     */
    public function buyClearCreditToken($tariff_id, $amount, $email, $sub_account = null)
    {
        // Get tariff
        $tariff = Tariff::where('id', $tariff_id)->first();

        if (!$tariff) {
            throw new Exception("Tariff not found");
        }

        // Get user
        $user = User::where('id', $this->user_id)->first();

        if (!$user) {
            throw new Exception("User not found");
        }

        // Create action payload
        $action_payload = [
            'action' => 'momas_clear_credit_token',
            'user_id' => $this->user_id,
            'meterNo' => $this->meterNo,
            'tariff_id' => $tariff_id,
            'tariff_index' => $tariff->tariff_index,
            'amount' => $amount,
            'email' => $email,
            'estate_id' => $this->estate_id,
        ];

        // Create transaction record
        $trx_id = generate_unique_string('MOMAS');

        Transaction::create([
            'trx_id' => $trx_id,
            'user_id' => $this->user_id,
            'amount' => $amount,
            'action_payload' => json_encode($action_payload),
            'service' => 'CLEAR CREDIT TOKEN PURCHASE',
            'service_type' => 'meter',
            'status' => 0, // Pending payment
        ]);

        // Prepare payment data
        $paymentData = [
            'amount' => $amount * 100, // Convert to kobo
            'email' => $email,
            'sub_account' => $sub_account,
            'metadata' => [
                'trx_id' => $trx_id,
                'meterNo' => $this->meterNo,
                'action' => 'momas_clear_credit_token',
            ],
        ];

        // Initialize Paystack payment
        $paystack = new PaystackPaymentService();
        $paymentResult = $paystack->makePayment($paymentData);

        if (!$paymentResult['status']) {
            // Update transaction status to failed
            Transaction::where('trx_id', $trx_id)->update([
                'status' => 1, // Failed
                'note' => 'Payment initialization failed: ' . ($paymentResult['message'] ?? 'Unknown error'),
            ]);

            throw new Exception($paymentResult['message'] ?? 'Payment initialization failed');
        }

        // Return payment initialization data
        return [
            'status' => true,
            'trx_id' => $trx_id,
            'authorization_url' => $paymentResult['data']['authorization_url'] ?? null,
            'reference' => $paymentResult['reference'] ?? null,
        ];
    }

}
