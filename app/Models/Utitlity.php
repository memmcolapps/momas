<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Utitlity extends Model
{

    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'title',
        'amount',
        'duration',
        'estate_id'
    ];

    protected $casts = [
        'amount' => 'double',
    ];

    public function customAudit()
    {
        return $this->audit()->create([
            'estate_id' => $this->estate_id,
        ]);
    }
}
