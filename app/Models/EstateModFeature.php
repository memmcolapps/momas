<?php

namespace App\Models;

use App\Models\Estate;
use App\Models\ModFeature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstateModFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'estate_id',
        'mod_feature_id',
        'status',
    ];


    protected const AVAILABLE_STATUS = 1;
    protected const UNAVAILABLE_STATUS = 0;
    protected const TEMPORARY_DOWNTIME_STATUS = 2;

    public function isAvailable() {
        return $this->status === self::AVAILABLE_STATUS;
    }

    public function isNotAvailable() {
        return $this->status === self::UNAVAILABLE_STATUS;
    }

    public function isDown() {
        return $this->status === self::TEMPORARY_DOWNTIME_STATUS;
    }


    public function estate() {
        return $this->belongsTo(Estate::class, 'estate_id');
    }

    public function modFeature() {
        return $this->belongsTo(ModFeature::class, 'mod_feature_id');
    }

    public function scopeByUser($query, $user) {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('estate_id', $user->estate_id);
    }
}
