<?php

namespace App\Models;

use App\Enums\AlertMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'mode',
        'text',
        'priority',
        'displays_at',
        'expires_at',
    ];

    protected $casts = [
        'mode' => AlertMode::class,
        'displays_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public const TYPE_TEXT_NOTICE = 'text_notice';

    public const TYPE_IMAGE_NOTICE = 'image_notice';

    public const TYPE_FULL_NOTICE = 'full_notice';

    public const TYPES = [
        self::TYPE_TEXT_NOTICE => 'Text Notice',
        self::TYPE_IMAGE_NOTICE => 'Image Notice',
        self::TYPE_FULL_NOTICE => 'Full Notice',
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

    public function buttons()
    {
        return $this->hasMany(AlertButton::class, 'alert_id');
    }

    public function button(): HasOne
    {
        return $this->hasOne(AlertButton::class, 'alert_id');
    }

    public function alertImage(): HasOne
    {
        return $this->hasOne(AlertImage::class);
    }

    public function image(): HasOne
    {
        return $this->hasOne(AlertImage::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        $now = now();

        return ($this->displays_at === null || $this->displays_at->lessThanOrEqualTo($now))
            && ($this->expires_at === null || $this->expires_at->greaterThanOrEqualTo($now));
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }

        if ($this->displays_at !== null && $this->displays_at->isFuture()) {
            return 'Upcoming';
        }

        return 'Active';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_label) {
            'Expired' => 'secondary',
            'Upcoming' => 'warning',
            default => 'success',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', (string) $this->type));
    }

    public function getModeLabelAttribute(): string
    {
        return $this->mode?->label() ?? ucfirst((string) $this->mode);
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match (strtolower($this->priority)) {
            self::PRIORITY_URGENT => 'danger',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_MEDIUM => 'info',
            default => 'secondary',
        };
    }
}
