<?php

namespace App\Models;

// use Illuminate\Contracts\auth\MustVerifyEmail;

use App\Services\PaystackPaymentService;
use Exception;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'meterNo',
        'meterType',
        'address',
        'city',
        'lga',
        'state',
        'estate',
        'phone',
        'state',
        'estate',
        'phone',
        'estate_name',
        'estate_id',
        'status',
        'google2fa_secret',
        'nepa_source',
        'gen_source',
        'tariffidnepa',
        'tariffidgen',
        'gen_source_amount',
        'nepa_source_amount',


    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    const SUPER_ADMIN = 0;
    const CUSTOMER = 2;
    const ESTATE_STAFF = 4;
    const ESTATE_ADMIN = 3;

    public function isSuperAdmin()
    {
        return $this->role == self::SUPER_ADMIN;
    }

    public function isCustomer()
    {
        return $this->role == self::CUSTOMER;
    }

    public function isEstateStaff() {
        return $this->role == self::ESTATE_STAFF;
    }

    public function isEstateAdmin() {
        return $this->role == self::ESTATE_ADMIN;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'type' => 'integer',
        'is_phone_verified'=> 'integer',
        'is_email_verified' => 'integer',
        'is_bvn_verified' => 'integer',
        'is_active' => 'integer',
        'is_identification_verified' => 'integer',
        'is_kyc_verified' => 'integer',
        'main_wallet' => 'integer',
        'bonus_wallet' => 'integer',
        'status' => 'integer',
        'role' => 'integer',
        'code' => 'integer',




    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function terminal()
    {
        return $this->hasMany(Terminal::class);
    }

    public function meter()
    {
        return $this->hasMany(Meter::class);
    }

    public function estate()
    {
        return $this->hasMany(Estate::class);
    }


    public function token()
    {
        return $this->hasMany(Token::class);
    }


    public function estate_service()
    {
        return $this->hasMany(EstateService::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function credit_token()
    {
        return $this->hasMany(CreditToken::class);
    }

    public function utilities()
    {
        return $this->hasMany(UtilitiesPayment::class);
    }


    public function virtual_account_trasnaction()
    {
        return $this->hasMany(VirtualAccountTransaction::class);
    }

    public function compensation_token()
    {
        return $this->hasMany(CompensationToken::class);
    }

    public function tamper_token()
    {
        return $this->hasMany(TamperToken::class);
    }

    public function audit()
    {
        return $this->hasMany(Audit::class);
    }

    public function creditWallet($amount) {
        if (! is_numeric($amount)) {
            throw new Exception('Non numeric values cannot be credited to wallet ' . $amount);
        }

        if ($amount <= 0) {
            throw new Exception('Cannot credit value less than or equal to 0 to wallet');
        }

        $amount = (double) $amount;

        $this->main_wallet += $amount;
        $this->save();
    }

    public function debitWallet($amount) {
        if (! is_numeric($amount)) {
            throw new Exception('Non numeric values cannot be debited to wallet ' . $amount);
        }

        if ($amount <= 0) {
            throw new Exception('Cannot debit value less than or equal to 0 to wallet');
        }

        if ($amount > $this->main_wallet) {
            throw new Exception('Insufficient Funds');
        }

        $this->main_wallet -= $amount;
        $this->save();
    }

    public function payWithWallet($transaction_id)
    {
        $payment_engine = app(PaystackPaymentService::class);
        $verify = $payment_engine->verifyTranscation($transaction_id);

        if (! $verify['is_successful']) {
            throw new Exception("Payment failed cannot be used for transaction");
        }

        return $verify['data']['amount'];
    }
}
