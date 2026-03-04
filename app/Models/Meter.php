<?php

namespace App\Models;

use App\Services\PaystackPaymentService;
use App\Services\TokenGenerationService;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Meter extends Model
{
    use HasFactory;

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
        DB::transaction(function () use ($tariff_id, $unit, $trx_id, $vat, $vending_amount, $email, $verify) {
            // dump('getNewToken', $this->user_id, $this);
            $user = User::where('id', $this->user_id)->firstOrFail();
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


            // dump($token_gen);


            Transaction::where('trx_id', $trx_id)->update([
                'service' => "CREDIT TOKEN PURCHASE",
                'service_type' => "meter",
                'tariff_id' => $tariff_id,
                'unit_amount' => $vending_amount,
            ]);


            if ( ! $token_gen['success']) {
                Transaction::where('trx_id', $trx_id)->update([
                    'note' => 'kct generation failed',
                    'status' => 3,
                ]);
                User::where('id', $this->user_id)->first()->creditWallet($vending_amount);


                return response()->json([

                    'status' => false,
                    'message' => "Vending server not connected, Retry again on transaction history",
                ], 422);
            }


            // dump("got here meter:188");
            $token = $token_gen['data']['token'];
            // dump("got here meter:190");

            // $cdt = new CreditToken();
            // $cdt->user_id = $request->user_id;
            // $cdt->trx_id = $trx_id;
            // $cdt->meterNo = $request->meterNo;
            // $cdt->amount = $amount;
            // $cdt->amount_charged = $request->amount;
            // $cdt->customer_email = $customer_email;
            // $cdt->fee = $fee;
            // $cdt->vat = $request->vat;
            // $cdt->estate_name = Estate::where('id', $request->estate_name)->first()->title;;
            // $cdt->estate_id = $estate_id;
            // $cdt->tariff_id = $request->t_index;     //Tariff index used
            // $cdt->tariff_amount = $request->tariff_amount;
            // $cdt->vatAmount = $request->vatAmount;
            // $cdt->costOfUnit = $request->costOfUnit;
            // $cdt->unitkwh = $request->unit;
            // $cdt->tariffPerKWatt = $request->tariffPerKWatt;


            $cdt = CreditToken::updateOrCreate([
                'trx_id' => $trx_id,
                'user_id' => $this->user_id,
                'meterNo' => $this->meterNo,
            ],
                [
                'amount' => $vending_amount,
                'amount_charged' => $vending_amount,
                'customer_email' => $email,
                'unitkwh' => $unit,
                'vat' => $vat,
                'estate_id' => $this->estate_id,
                'estate_name' => $user->estate_name,
                'token' => $token,
                'status' => 2
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

}
