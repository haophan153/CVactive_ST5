<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::where('email','user@gmail.com')->first();
$apps = App\Models\JobApplication::where('user_id', $u->id)->get();
echo "Apps: " . $apps->count() . "\n";
foreach ($apps as $a) {
    echo " - {$a->id} for job {$a->job_post_id} ({$a->email}) - status: {$a->status}\n";
}

// Check all jobs and their status
echo "\nAll jobs:\n";
$jobs = App\Models\JobPost::all();
foreach ($jobs as $j) {
    echo " - Job {$j->id}: {$j->title} - status: {$j->status}\n";
}