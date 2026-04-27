<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecretToken extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'token_hash',
        'expires_at',
        'attempts',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'attempts' => 'integer',
    ];
}
