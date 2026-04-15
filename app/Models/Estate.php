<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estate extends Model
{
    use HasFactory;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }


    public function transformer()
    {
        return $this->hasMany(Transformer::class);
    }

    public function token()
    {
        return $this->hasMany(Token::class);
    }

    public function meter_token()
    {
        return $this->hasMany(MeterToken::class);
    }

    public function kct_token()
    {
        return $this->hasMany(KctMeterToken::class);
    }




    public function estate_service()
    {
        return $this->hasMany(EstateService::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
    public function credit_token()
    {
        return $this->hasMany(CreditToken::class);
    }

    public function utilities()
    {
        return $this->hasMany(UtilitiesPayment::class);
    }

    public function scopeByUser($query, $user) {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('estate_id', $user->estate_id);
    }

    protected $casts = [
        'status' => 'integer',
        ];
}
