<?php

namespace App\Models;

use App\Constants\UserUtilityStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Utility extends Model implements AuditableContract
{
    use Auditable;

    protected $table = 'utilities';

    protected $fillable = [
        'estate_id',
        'user_id',
        'type',
        'title',
        'amount',
        'duration',
        'status',
        'start_date',
        'mode_of_payment',
        'payment_amount',
        'activated',
        'operator_id',
        'percent_payment',
        'payment_months',
        'monthly_end_date',
    ];

    protected $casts = [
        'amount' => 'double',
        'payment_amount' => 'double',
        'percent_payment' => 'double',
        'activated' => 'boolean',
        'operator_id' => 'integer',
        'payment_months' => 'integer',
        'monthly_end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Utility $utility) {
            UserUtility::where('utility_id', $utility->id)
                ->update([
                    'status' => UserUtilityStatus::DEACTIVATED,
                    'activated' => false,
                ]);
        });
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function userUtilities()
    {
        return $this->hasMany(UserUtility::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeDebt(Builder $query): Builder
    {
        return $query->where('type', 'debt');
    }

    public function scopeServiceCharge(Builder $query): Builder
    {
        return $query->where('type', 'service_charge')->whereNull('user_id');
    }

    public function customAudit()
    {
        return $this->audit()->create([
            'estate_id' => $this->estate_id,
        ]);
    }
}
