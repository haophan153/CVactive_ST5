<?php

return [
    'tmn_code'    => env('VNPAY_TMN_CODE', ''),
    'hash_secret' => env('VNPAY_HASH_SECRET', ''),
    'url'         => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url'  => env('APP_URL') . '/payment/vnpay/return',
    'ipn_url'     => env('APP_URL') . '/payment/vnpay/ipn',
];
