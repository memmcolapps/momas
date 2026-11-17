<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosLog extends Model
{
    use HasFactory;

    protected $casts = [
        'accountBalance' => 'string',
        'amount' => 'string',
        'log_status' => 'string',
        'status' => 'integer',
    ];

    protected $guarded = [];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
