<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

        protected $fillable = [
        'user_id',
        'estate_id',
        'pay_type',
        'service_type',
        'service',
        'utility_id',
        'utility_amount',
        'trx_id',
        'tariff_id',
        'payment_ref',
        'amount',
        'fee',
        'unit_amount',
        'status',
        'note',
    ];


    protected $casts = [
        'user_id'=> 'integer',
        'debit' => 'integer',
        'credit' => 'integer',
        'balance' => 'integer',
        'amount' => 'string',
        'fee' => 'integer',
        'from_user_id' => 'integer',
        'main_wallet' => 'integer',
        'status' => 'integer',
        'e_charges' => 'integer',
        'charge' => 'integer',
        'resolve' => 'integer',
    ];

    protected $hidden = [
        'note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function creditToken()
    {
        return $this->hasOne(CreditToken::class, 'trx_id', 'trx_id');
    }
}
