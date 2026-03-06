<?php

return [
    'partner_code' => env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529'),
    'access_key'   => env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j'),
    'secret_key'   => env('MOMO_SECRET_KEY', 'at67qH6FK8NKRIzvkQxPbIEaUuyG6gKP'),
    'endpoint'     => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn'),
    'return_url'   => env('APP_URL') . '/payment/momo/return',
    'notify_url'   => env('APP_URL') . '/payment/momo/ipn',
];
