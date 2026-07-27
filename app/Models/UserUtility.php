<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserUtility extends Model
{
    protected $table = 'user_utilities';

    protected $fillable = [
        'utility_id',
        'user_id',
        'estate_id',
        'amount',
        'amount_paid',
        'activated',
        'status',
    ];

    protected $casts = [
        'amount' => 'double',
        'amount_paid' => 'double',
        'activated' => 'boolean',
        'status' => 'integer',
    ];

    public function utility()
    {
        return $this->belongsTo(Utility::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function paymentRecords()
    {
        return $this->hasMany(UtilityPaymentRecord::class, 'user_utility_id');
    }
}
