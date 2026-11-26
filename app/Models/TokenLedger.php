<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenLedger extends Model
{
    use HasFactory;

    public const TYPE_CREDIT_TOKEN = 'credit_token';
    public const TYPE_CREDIT_TOKEN_OTHERS = 'credit_token_others';
    public const TYPE_TAMPER_TOKEN = 'tamper_token';
    public const TYPE_CLEAR_CREDIT_TOKEN = 'clear_credit_token';
    public const TYPE_KCT_TOKEN = 'kct_token';

    protected $fillable = [
        'trx_id',
        'user_id',
        'meterNo',
        'credit_token_id',
        'trx_amount',
        'expected_fee',
        'token_type',
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

    public function getReceiptTypeAttribute(): string
    {
        return match ($this->token_type) {
            self::TYPE_TAMPER_TOKEN => 'tamper',
            self::TYPE_CLEAR_CREDIT_TOKEN => 'clear_credit',
            self::TYPE_KCT_TOKEN => 'kct_token',
            default => 'credit_token',
        };
    }
}
