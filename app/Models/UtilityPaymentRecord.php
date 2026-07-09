<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityPaymentRecord extends Model
{
    protected $table = 'utility_payment_records';

    protected $fillable = [
        'utility_id',
        'user_id',
        'estate_id',
        'amount',
        'amount_paid',
        'activated',
        'status',
    ];

    protected $casts = [
        'amount' => 'double',
        'amount_paid' => 'double',
        'activated' => 'boolean',
        'status' => 'integer',
    ];

    public function utility()
    {
        return $this->belongsTo(Utility::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }
}
