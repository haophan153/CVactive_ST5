<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Delete test-created CVs (keep first 3 original ones)
$user = App\Models\User::where('email', 'user@gmail.com')->first();
$count = $user->cvs()->count();
echo "User has $count CVs\n";
if ($count > 3) {
    $deleted = $user->cvs()->orderBy('id', 'desc')->limit($count - 3)->delete();
    echo "Deleted $deleted test CVs\n";
}
$countAfter = $user->cvs()->count();
echo "After: $countAfter CVs\n";