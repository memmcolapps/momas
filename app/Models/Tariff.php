<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $fillable = [
        'legacy_tariffid',
        'legacy_buid',
        'title',
        'tariff_index',
        'estate_id',
        'status',
        'created_at',
        'updated_at',
    ];
    use HasFactory;

    public const POWER_SOURCE = [
        'Grid',
        'Off Grid'
    ];


    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }


}
