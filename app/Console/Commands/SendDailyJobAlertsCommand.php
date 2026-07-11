<?php

namespace App\Console\Commands;

use App\Jobs\SendDailyJobAlerts;
use Illuminate\Console\Command;

class SendDailyJobAlertsCommand extends Command
{
    protected $signature = 'job-alerts:send-daily {--user= : Gửi cho user_id cụ thể (test)}';

    protected $description = 'Quét job matches và gửi email cho users đang bật Smart Job Matcher.';

    public function handle(): int
    {
        $this->info('Đang quét job matches...');

        if ($userId = $this->option('user')) {
            $uid = (int) $userId;
            $this->info("Test mode: chỉ gửi cho user_id = {$uid}");
            app(\App\Services\JobMatching\JobMatcherService::class);
            app(\App\Services\JobMatching\SkillExtractor::class);
            $alert = \App\Models\JobAlert::where('user_id', $uid)->where('is_active', true)->first();
            if (!$alert) {
                $this->warn("User {$uid} chưa bật Smart Job Matcher.");
                return self::SUCCESS;
            }
            $matcher  = app(\App\Services\JobMatching\JobMatcherService::class);
            $extractor = app(\App\Services\JobMatching\SkillExtractor::class);
            $extractor->extractAndSave($uid);
            $matches = $matcher->matchForAlert($alert);
            $this->info("Tìm thấy {$matches->count()} job match.");
            if ($matches->isNotEmpty()) {
                foreach ($matches as $m) {
                    $m->update(['sent_at' => now()]);
                }
                $user = \App\Models\User::find($uid);
                $user->notify(new \App\Notifications\JobMatchAlert($matches->all()));
                $alert->markSent();
                $this->info('✓ Đã gửi mail cho user ' . $uid);
            }
        } else {
            (new SendDailyJobAlerts())->handle(
                app(\App\Services\JobMatching\JobMatcherService::class),
                app(\App\Services\JobMatching\SkillExtractor::class),
            );
        }

        $this->info('✓ Hoàn tất.');
        return self::SUCCESS;
    }
}