<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use OwenIt\Auditing\Contracts\Auditable;


class UtilitiesPayment extends Model implements Auditable

{
    use \OwenIt\Auditing\Auditable;





    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    protected $fillable = ['type', 'amount', 'duration', 'next_due_date', 'estate_id', 'total_amount', 'customer_id', 'estate_id'];


    protected $casts = [
        'amount' => 'double',
        'total_amount' => 'double'
    ];

    public function transformAudit(array $data): array
    {
        return array_merge($data, [
            'customer_id' => Request::get('customer_id'),
            'estate_id'   => $this->estate_id,
        ]);
    }

}
