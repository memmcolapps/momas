<?php

namespace App\Models;

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

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customAudit()
    {
        return $this->audit()->create([
            'estate_id' => $this->estate_id,
        ]);
    }
}
