<?php

return [
    'paystack_payment_endpoint' => 'https://api.paystack.co/transaction/initialize',
    'flutterwave_payment_endpoint' => 'https://api.flutterwave.com/v3/payments',
    'remita_payment_endpoint' => env(
        'REMITA_PAYMENT_ENDPOINT',
        // 'https://remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit'
        'https://demo.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit',
    ),

    'remita_status_endpoint' => env(
        'REMITA_STATUS_ENDPOINT',
        'https://remitademo.net/remita/exapp/api/v1/send/api/echannelsvc'
    ),

    'remita_payment_page_url' => env(
        'REMITA_PAYMENT_PAGE_URL',
        'https://demo.remita.net/remita/onepage/api/v1/so.spa'
    ),

    'remita_rrr_generate_endpoint' => 'https://standardpay.remita.net/api/rrr/GenerateRRR',

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

    'momas_minimum_vend' => env('MOMAS_MINIMUM_VEND', 100),
    'simulate_failed_token_gen' => env('SIMULATE_FAILED_TOKEN', false),
    'token_retry_deployment_date' => env('TOKEN_RETRY_DEPLOYMENT_DATE', '2026-08-11'),
];
