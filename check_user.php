<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::where('email', 'user@gmail.com')->first();
if ($u) {
    echo json_encode([
        'id' => $u->id,
        'email' => $u->email,
        'name' => $u->name,
        'role' => $u->role ?? null,
        'is_admin' => $u->is_admin ?? null,
        'is_hr' => method_exists($u, 'isHr') ? $u->isHr() : null,
        'plan' => $u->plan_id ?? null,
        'email_verified' => $u->email_verified_at ? 'yes' : 'no',
    ], JSON_PRETTY_PRINT);
} else {
    echo "NOT_FOUND";
}