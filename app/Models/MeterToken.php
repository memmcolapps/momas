<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterToken extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'integer',
        'status' => 'integer'
    ];

    protected $fillable = [
        'trx_id',
        'user_id',
        'meterNo',
        'token',
        'kct_tokens',
        'amount',
        'vat',
        'estate_id',
        'status',
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

}
