<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Auditlog extends Model
{
    use HasFactory;

    protected $fillable = [
        'estate_id',
        'user_id',
        'user_type',
        'user_name',
        'action',
        'action_identifier',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'user_id' => 'string',
        'entity_id' => 'string',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function estate(): BelongsTo
    {
        return $this->belongsTo(Estate::class, 'estate_id');
    }
}
