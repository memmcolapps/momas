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
];
