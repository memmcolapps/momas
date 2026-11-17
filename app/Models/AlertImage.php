<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_id',
        'path',
        'url',
        'width',
        'height',
        'fit',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->path ? url('/storage/'.$this->path) : $value,
        );
    }

    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }
}
