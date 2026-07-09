<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::where('email', 'user@gmail.com')->first();
if ($u && !$u->email_verified_at) {
    $u->email_verified_at = now();
    $u->save();
    echo "VERIFIED: " . $u->email . "\n";
} else {
    echo "ALREADY_VERIFIED_OR_NOT_FOUND\n";
}