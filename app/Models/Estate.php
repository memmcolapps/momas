<?php

namespace App\Models;

use App\Services\ConfigManagementService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    protected $fillable = [
        'legacy_buid',
        'paystack_subaccount',
        'account_no',
        'account_name',
        'bank',
        'bank_id',
        'title',
        'address',
        'state',
        'status',
        'min_pur',
        'max_pur',
        'fee_accumulation_period',
        'minimum_vend_per_transaction',
        'estate_meter_vending_type',
        'transaction_fee_type',
        'transaction_fee',
        'emergency_token_max',
        'created_at',
        'updated_at',
    ];

    use HasFactory;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function transformer()
    {
        return $this->hasMany(Transformer::class);
    }

    public function token()
    {
        return $this->hasMany(Token::class);
    }

    public function meter_token()
    {
        return $this->hasMany(MeterToken::class);
    }

    public function kct_token()
    {
        return $this->hasMany(KctMeterToken::class);
    }

    public function estate_service()
    {
        return $this->hasMany(EstateService::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function credit_token()
    {
        return $this->hasMany(CreditToken::class);
    }

    public function utilities()
    {
        return $this->hasMany(UtilitiesPayment::class);
    }

    public function scopeByUser($query, $user)
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('estate_id', $user->estate_id);
    }

    public function estateModFeature()
    {
        return $this->hasMany(EstateModFeature::class, 'estate_id');
    }

    public function getAdminFee()
    {
        return $this->admin_fee;
    }

    public function isActive()
    {
        return true;
    }

    public function isAcive()
    {
        return $this->isActive();
    }

    protected $casts = [
        'status' => 'integer',
        'minimum_vend_per_transaction' => 'boolean',
        'estate_meter_vending_type' => 'integer',
        'transaction_fee' => 'decimal:2',
        'emergency_token_max' => 'integer',
    ];

    public function getUserMinPur($user_id)
    {
        $min_pur = $this->min_pur;

        if (! $this->minimum_vend_per_transaction) {
            $existing_month_transaction = Transaction::where('user_id', $user_id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->exists();

            if ($existing_month_transaction) {
                $min_pur = config('constants.momas_minimum_vend');
            }
        }

        return $min_pur;
    }

    public function getBank() {
        return Bank::firstWhere('id', $this->bank_id);
    }

    /**
     * The maximum amount, in Naira, that an emergency token may be
     * generated for within this estate.
     *
     * The estate level value takes precedence. When it is missing or zero
     * the global configured maximum is used instead so estates that have
     * not set a limit keep behaving as before.
     */
    public function getEmergencyTokenMax(): int
    {
        if ($this->emergency_token_max) {
            return (int) $this->emergency_token_max;
        }

        return (int) (app(ConfigManagementService::class)
            ->getConfig('momas-max-emergency-token') ?? config('constants.momas_max_emergency_token'));
    }
}
