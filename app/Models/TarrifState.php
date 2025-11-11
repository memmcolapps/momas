<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class TarrifState extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'status', 'estate_id', 't_index', 'fixed_charge',
    ];

    protected $casts = [
        'amount' => 'double',
        'vat' => 'double',
        'fixed_charge' => 'double',
    ];

    public function customAudit()
    {
        return $this->audit()->create([
            'estate_id' => $this->estate_id, // custom field
        ]);
    }
}
