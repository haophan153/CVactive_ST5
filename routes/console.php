<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendDailyJobAlerts;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Smart Job Matcher daily email ─────────────────────────────────────────────
Schedule::job(new SendDailyJobAlerts)->dailyAt('08:00')
    ->onOneServer()
    ->withoutOverlapping(60); // 60 phút max, tránh overlap
