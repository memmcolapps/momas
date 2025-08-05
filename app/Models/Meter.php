<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meter extends Model
{
    use HasFactory;

    protected $fillable = [

        'user_id',
        'meterNo',
        'meterType',
        'payType',
        'meterModel',
        'AccountNo',
        'estate_id',
        'TransformerID',
        'isDualTariff',
        'NewSGC',
        'OldSGC',
        'NewTariffID',
        'NewTariffDualID',
        'OldTariffDualID',
        'OldTariffID',
        'NewSGCDual',
        'OldSGCDual',
        'NewTariffDual',
        'OldTariffDual',
        'KRN1',
        'KRN2',
        'NeedKCT',
        'CreditTypeID',
        'AddedBy'







    ];

    protected $casts = [
        'user_id'=> 'integer',
        'debit' => 'integer',
        'credit' => 'integer',
        'balance' => 'integer',
        'amount' => 'integer',
        'fee' => 'integer',
        'from_user_id' => 'integer',
        'main_wallet' => 'integer',
        'status' => 'integer',
        'e_charges' => 'integer',
        'charge' => 'integer',
        'resolve' => 'integer',
    ];

//    public function user()
//    {
//        return $this->belongsTo(User::class);
//    }
//
//
//    public function estate()
//    {
//        return $this->belongsTo(Estate::class);
//    }

    public function transformer()
    {
        return $this->belongsTo(Transformer::class);
    }

    public function credit_token()
    {
        return $this->belongsTo(CreditToken::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class, 'estate_id', 'id');
    }

}
