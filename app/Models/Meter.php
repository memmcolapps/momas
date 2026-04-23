<?php

namespace App\Models;

use App\Services\PaystackPaymentService;
use App\Services\TokenGenerationService;
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
    public function getNewToken($tariff_id, $unit, $trx_id, $vat, $vending_amount, $email=null, $verify="verify") {
        // dump('before');
        $user = User::where('id', $this->user_id)->firstOrFail();
        // dump('after');

        // dd(DB::transactionLevel());

        // $cdt = CreditToken::create([
        //     'trx_id' => $trx_id,
        //     // 'user_id' => $action_payload['user_id'],
        //     'meterNo' => $this->meterNo,
        //     'amount' => $vending_amount,
        //     'amount_charged' => $vending_amount,
        //     // 'customer_email' => $email,
        //     // 'unitkwh' => $unit,
        //     'vat' => $vat,
        //     'estate_id' => $this->estate_id,
        //     'estate_name' => $user->estate_name,
        //     // 'token' => null,
        //     'status' => 0
        // ]);

        DB::transaction(function () use ($tariff_id, $unit, $trx_id, $vat, $vending_amount, $email, $verify, $user) {
            // dump('getNewToken', $this->user_id, $this);
            $email = (! $email || $email === 'null')
                ? $user->email
                : $email;

            // dump('fetched User', $user->toArray(), $user->toArray()['email']);


            if ($this->status === 0) {
                throw new Exception("Meter is unable from carrying out operations");
            }

            $trx = Transaction::where('trx_id', $trx_id)
                ->firstOrFail();

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

            // dump ($verifier_engine);

            if ($trx->status === 0) {
                $verify = $verifier_engine($trx_id);

                if (! $verify['is_successful']) {
                    throw new Exception("Transaction Failed");
                }
            }

            if ($trx->status === 1) {
                throw new Exception("Transaction Failed");
            }

            // dump('Passed trx ver');

            $need_kct = $this->NeedKCT;

            $tariff_index = Tariff::where('id', $tariff_id)->first()->tariff_index ?? null;
            $token_gen = TokenGenerationService::generateMeterToken($this, $tariff_index, $unit, $this->NeedKCT);


            // dd($token_gen);


            Transaction::where('trx_id', $trx_id)->update([
                'service' => "CREDIT TOKEN PURCHASE",
                'service_type' => "credit_token",
                'tariff_id' => $tariff_id,
                'unit_amount' => $vending_amount,
            ]);


            if ( ! $token_gen['success']) {
                Transaction::where('trx_id', $trx_id)->update([
                    'note' => 'kct generation failed',
                    'status' => 3,
                ]);
                // User::where('id', $this->user_id)->first()->creditWallet($vending_amount);


                throw new Exception("Vending server not connected, Retry again on transaction history");
            }


            // dump("got here meter:188");
            $token = $token_gen['data']['token'];
            // dump("got here meter:190");

            $tariffState = TarrifState::where('tariff_id', $tariff_id)->where('status', 2)->first();
            $tariffAmount = $tariffState->amount ?? 0;


            $cdt = CreditToken::updateOrCreate([
                'trx_id' => $trx_id,
                'user_id' => $this->user_id,
                'meterNo' => $this->meterNo,
            ],
                [
                'amount' => $vending_amount,
                'amount_charged' => $vending_amount,
                'customer_email' => $email,
                // 'receiver_meterNo' => $receiver_meterNo,
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

            // dump("got here meter:227");

            // dump($cdt->toArray());


            if ($need_kct) {

                // dump('entered needkct meter:240');
                $kct_tokens = $token_gen['data']['kct_token'];

                $met = new MeterToken();
                $met->user_id = $this->user_id;
                $met->trx_id = $trx_id;
                $met->meterNo = $this->meterNo;
                $met->token = $token;
                $met->amount = $total_paid ?? 0;
                $met->unit = $unit;
                $met->kct_tokens = $kct_tokens[0] . "," . $kct_tokens[1];
                $met->vat = $vat;
                $met->estate_id = $this->estate_id;
                $met->status = 2;
                $met->save();

                // dump('created need kct meter:256');

                $kct_token1 = $kct_tokens[0];
                $kct_token2 = $kct_tokens[1];

                $data2['kct_token1'] = $kct_tokens[0];
                $data2['kct_token2'] = $kct_tokens[1];

                send_kct_email_token($email, $token, $vending_amount, $kct_token1, $kct_token2);

                // dump('sent kct meter:267');
            }

            Transaction::where('trx_id', $trx_id)->update(['status' => '2']);

        });
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
