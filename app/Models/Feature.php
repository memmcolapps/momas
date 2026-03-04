<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'momas_meter',
        'other_meter',
        'momas_tamper_token',
        'print_token',
        'access_token',
        'services',
        'bill_payment',
        'support',
        'analysis',

    ];


    protected $casts = [

    "id" => 'integer',
    "momas_meter" => 'integer',
    "momas_tamper_token" => 'integer',
    "other_meter" => 'integer',
    "print_token"=> 'integer',
    "access_token"=> 'integer',
    "services"=> 'integer',
    "bill_payment"=> 'integer',
    "support"=> 'integer',
    "top_up"=> 'integer',
    "analysis"=> 'integer',
    "exchange"=> 'integer',
    "ticket"=> 'integer',
    "v_card"=> 'integer',
    "pos_transfer"=> 'integer',
    "api_service"=> 'integer',

    ];
}
