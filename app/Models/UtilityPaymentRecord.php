<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UtilityPaymentRecord extends Model
{
    protected $table = 'utility_payment_records';

    protected $fillable = [
        'user_utility_id',
        'utility_id',
        'user_id',
        'estate_id',
        'utility_amount',
        'amount_paid',
        'trx_id',
        'status',
        'created_at',
    ];

    protected $casts = [
        'utility_amount' => 'double',
        'amount_paid' => 'double',
        'status' => 'integer',
    ];

    public function userUtility()
    {
        return $this->belongsTo(UserUtility::class, 'user_utility_id');
    }

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
