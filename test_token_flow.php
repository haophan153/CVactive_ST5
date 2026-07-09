<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Str;

$token = 'pay_' . Str::uuid()->toString();
echo "Token: $token" . PHP_EOL;

try {
    $url = app(\App\Services\VNPayService::class)->createPaymentUrl(
        orderId:   $token,
        amount:    99000,
        orderDesc: "Thanh toan goi Test - CVactive",
        ipAddr:    '127.0.0.1',
    );
    echo "VNPay OK: " . substr($url, 0, 120) . "..." . PHP_EOL;
} catch (\Throwable $e) {
    echo "VNPay FAIL: " . $e->getMessage() . PHP_EOL;
}

try {
    $result = app(\App\Services\MoMoService::class)->createPaymentUrl(
        orderId:   $token,
        amount:    99000,
        orderDesc: "Thanh toan goi Test - CVactive",
    );
    echo "MoMo result: " . json_encode((array) $result) . PHP_EOL;
} catch (\Throwable $e) {
    echo "MoMo FAIL: " . $e->getMessage() . PHP_EOL;
}
