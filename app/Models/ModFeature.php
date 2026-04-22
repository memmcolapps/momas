<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'description',
        'slug',
    ];

    public const AVAILABLE_STATUS = 1;
    public const UNAVAILABLE_STATUS = 0;
    public const TEMPORARY_DOWNTIME_STATUS = 2;

    public const HANDLED_STATUS = [
        self::UNAVAILABLE_STATUS => 'UNAVAILABLE',
        self::AVAILABLE_STATUS => 'AVAILABLE',
        self::TEMPORARY_DOWNTIME_STATUS => 'DOWN'
    ];

    public function isAvailable() {
        return $this->status === self::AVAILABLE_STATUS;
    }

    public function isNotAvailable() {
        return $this->status === self::UNAVAILABLE_STATUS;
    }

    public function isDown() {
        return $this->status === self::TEMPORARY_DOWNTIME_STATUS;
    }

    public function estateModFeature() {
        return $this->hasMany(EstateModFeature::class, 'mod_feature_id');
    }

    public function scopeByStatus($query, $status = self::AVAILABLE_STATUS) {
        return $query->where('status', $status);
    }

    public function scopeVisibleToUser($query, $user) {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereHas('estateModFeature', function ($q) use ($user) {
            $q->where('estate_id', $user->estate_id);
        });
    }
}
