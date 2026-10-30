<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    protected $table = 'alert_groups';

    protected $fillable = [
        'alert_id',
        'all_users',
        'estates',
        'users',
        'meter_numbers',
    ];

    protected $casts = [
        'all_users' => 'boolean',
        'estates' => 'array',
        'users' => 'array',
        'meter_numbers' => 'array',
    ];

    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }
}
