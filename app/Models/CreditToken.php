<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditToken extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'float',
        'vatAmount' => 'float',
        'unitkwh' => 'float',
        'fee' => 'float',
    ];

    protected $fillable = [
        'trx_id',
        'user_id',
        'meterNo',
        'amount',
        'vat',
        'estate_id',
        'token',
        'status',
        'amount_charged',
        'customer_email',
        'tariff_id',
        'estate_name',
        'unitkwh',
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'trx_id', 'trx_id');
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }
}
