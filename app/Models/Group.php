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
        'users',
    ];

    protected $casts = [
        'all_users' => 'boolean',
        'users' => 'array',
    ];

    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }
}
