<?php

return [
    'paystack_payment_endpoint' => 'https://api.paystack.co/transaction/initialize',
    'flutterwave_payment_endpoint' => 'https://api.flutterwave.com/v3/payments',

    'status' => [  //Uninversal status and status code for uniformity and easy refactoring in case of future change
        'scs' => 'success',
        'fail' => 'failed',
        'pnd' => 'pending',
        'abnd' => 'abandoned',
        'ong' => 'ongoing',
        'proc' => 'processing'
    ],

    'status_code' => [
        'pnd' => 0,
        'scs' => 2,
        'retry' => 3,
    ],

    'service' => [
        'credit_token' => 'CREDIT TOKEN PURCHASE'
    ],

    'app_update_data' => [
        'app_minimum_version' => env('APP_MINIMUM_VERSION'),
        'app_latest_version' => env('APP_LATEST_VERSION'),
        'app_last_update_date' => env('APP_LAST_UPDATE_DATE'),
        'app_size' => env('APP_SIZE'),
        'app_playstore_url' => env('APP_PLAYSTORE_URL'),
        'app_appstore_url' => env('APP_APPSTORE_URL'),
        'app_update_description' => env('APP_UPDATE_DESCRIPTION'),
    ],
];
