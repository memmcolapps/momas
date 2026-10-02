<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KctToken extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
