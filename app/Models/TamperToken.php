<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TamperToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trx_id',
        'token',
        'status',
        'estate_id',
        'meterNo',
        'amount',
    ];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
