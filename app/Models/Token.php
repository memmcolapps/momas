<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function scopeByUser($query, $user) {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('estate_id', $user->estate_id);
    }


}
