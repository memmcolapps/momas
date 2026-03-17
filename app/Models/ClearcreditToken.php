<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearcreditToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trx_id',
        'meterNo',
        'amount',
        'amount_charged',
        'fee',
        'vat',
        'estate_name',
        'estate_id',
        'tariff_id',
        'tariff_amount',
        'vatAmount',
        'costOfUnit',
        'unitkwh',
        'tariffPerKWatt',
        'token',
        'status',
    ];
}
