<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$u = App\Models\User::where('email', 'user@gmail.com')->first();
if ($u) {
    $u->password = 'fang12345';
    $u->save();
    echo "RESET: " . $u->email . " -> fang12345\n";
}