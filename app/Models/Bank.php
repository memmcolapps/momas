<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'bankName',
        'code',
        'name',
        'slug',
        'paystack_code',
        'remita_code',
        'gateway',
        'supports_transfer',
        'active',
        'country',
        'currency',
        'type',
    ];

    public function estates()
    {
        return $this->hasMany(Estate::class);
    }
}
