<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'trx_id',
        'user_id',
        'meterNo',
        'credit_token_id',
        'trx_amount',
        'expected_fee',
        'paid_at',
        'receiver_id',
        'estate_id'
    ];

    protected $casts = [
        'trx_amount' => 'decimal:2',
        'expected_fee' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }
}
