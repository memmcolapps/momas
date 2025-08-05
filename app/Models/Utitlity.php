<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Utitlity extends Model implements AuditableContract
{
    use Auditable;

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
