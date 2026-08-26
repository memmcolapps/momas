<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'paybeta' => [
        'key' => env('PAYBETA_API_KEY'),
        'base_url' => 'https://api.paybeta.ng/v2',
    ],

    'remita' => [
        'merchant_id' => env('REMITA_MERCHANT_ID'),
        'api_key' => env('REMITA_API_KEY'),
        'service_type_id' => env('REMITA_SERVICE_TYPE_ID'),
        'test_merchant_id' => env('REMITA_TEST_MERCHANT_ID'),
        'test_api_key' => env('REMITA_TEST_API_KEY'),
        'test_service_type_id' => env('REMITA_TEST_SERVICE_TYPE_ID'),
    ],

];
