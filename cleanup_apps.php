<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'user@gmail.com')->first();
$apps = App\Models\JobApplication::where('user_id', $user->id)->get();
echo "User has " . $apps->count() . " applications\n";
foreach ($apps as $app) {
    echo "  - App {$app->id} for job {$app->job_post_id}\n";
}
$deleted = App\Models\JobApplication::where('user_id', $user->id)->delete();
echo "Deleted $deleted applications\n";