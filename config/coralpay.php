<?php

// config/coralpay.php

return [

    /*
    |--------------------------------------------------------------------------
    | CoralPay VAS API
    |--------------------------------------------------------------------------
    |
    | Credentials for the Value Added Services (VAS) API — airtime, data,
    | bills. Sandbox: https://vas.coralpay.com:9443/cgateproxy/api
    |
    */

    'vas_base_url'   => env('CORALPAY_VAS_BASE_URL', 'https://vas.coralpay.com:9443/cgateproxy/api'),
    'vas_username'   => env('CORALPAY_VAS_USERNAME'),
    'vas_password'   => env('CORALPAY_VAS_PASSWORD'),
    'vas_terminal_id'=> env('CORALPAY_VAS_TERMINAL_ID'),
    'vas_channel'    => env('CORALPAY_VAS_CHANNEL', 'WEB'),  // WEB | MOBILE | USSD | API

    /*
    |--------------------------------------------------------------------------
    | CoralPay Payment Gateway (C'Gate) — separate from VAS
    |--------------------------------------------------------------------------
    */
    'gateway_base_url'  => env('CORALPAY_GW_BASE_URL', 'https://testdev.coralpay.com:5000/GwApi/api/v1'),
    'gateway_username'  => env('CORALPAY_GW_USERNAME'),
    'gateway_password'  => env('CORALPAY_GW_PASSWORD'),
    'gateway_merchant_id'=> env('CORALPAY_GW_MERCHANT_ID'),

];
