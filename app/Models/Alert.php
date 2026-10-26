<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_url',
        'priority',
        'displays_at',
        'is_urgent',
        'expires_at',
    ];

    protected $casts = [
        'displays_at' => 'datetime',
        'is_urgent' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_MEDIUM = 'medium';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITY_URGENT = 'urgent';

    public const PRIORITY_WEIGHTS = [
        self::PRIORITY_LOW => 1,
        self::PRIORITY_MEDIUM => 2,
        self::PRIORITY_HIGH => 3,
        self::PRIORITY_URGENT => 4,
    ];

    public function group(): HasOne
    {
        return $this->hasOne(Group::class);
    }

    public function alertUsers(): HasMany
    {
        return $this->hasMany(AlertUser::class);
    }
}
