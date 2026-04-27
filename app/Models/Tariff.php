<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    public const POWER_SOURCE = [
        'Grid',
        'Off Grid',
        'Middle Of Grid'
    ];


    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }


}
