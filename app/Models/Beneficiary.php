<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'estate_id',
        'line_items_id',
        'beneficiary_name',
        'beneficiary_account',
        'bank_code',
        'deduct_fee_from',
        'status',
    ];

    protected $casts = [
        'bank_code' => 'string',
        'line_items_id' => 'string',
        'beneficiary_account' => 'string',
        'deduct_fee_from' => 'boolean',
        'status' => 'integer',
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_code', 'remita_code');
    }
}