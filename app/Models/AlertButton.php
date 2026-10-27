<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertButton extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'cta_url',
        'alert_id',
    ];

    public function alert() {
        return $this->belongsTo(Alert::class, 'alert_id');
    }
}
