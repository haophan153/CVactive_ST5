<?php

namespace App\Jobs;

use App\Models\JobAlert;
use App\Models\JobMatchLog;
use App\Models\User;
use App\Notifications\JobMatchAlert;
use App\Services\JobMatching\JobMatcherService;
use App\Services\JobMatching\SkillExtractor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Gửi email thông báo việc làm phù hợp cho user mỗi ngày.
 *
 * Flow:
 *  1. Lấy tất cả user có alert active + frequency = daily
 *  2. Với mỗi user: extract/update skill profile
 *  3. Run job matching (rule-based → AI re-rank)
 *  4. Nếu có matches: mark sent + gửi email notification
 *  5. Log kết quả
 */
class SendDailyJobAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function handle(JobMatcherService $matcher, SkillExtractor $extractor): void
    {
        Log::info('SendDailyJobAlerts: starting');

        $alerts = JobAlert::where('is_active', true)
            ->where('notification_frequency', 'daily')
            ->where(function ($q) {
                $q->whereNull('last_sent_at')
                  ->orWhereDate('last_sent_at', '<', now()->toDateString());
            })
            ->with('user')
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($alerts as $alert) {
            $user = $alert->user;

            // Skip if user has no CV
            if ($user->cvs()->count() === 0) {
                $skipped++;
                continue;
            }

            // Skip if user has no email
            if (!$user->email || !$user->hasVerifiedEmail()) {
                $skipped++;
                continue;
            }

            // Extract or refresh skill profile
            $this->refreshSkillProfile($user->id, $extractor);

            // Run matching
            $matches = $matcher->matchForAlert($alert);

            if ($matches->isEmpty()) {
                $skipped++;
                continue;
            }

            // Mark matches as sent
            foreach ($matches as $match) {
                $match->update(['sent_at' => now()]);
            }

            // Send notification
            try {
                Notification::send($user, new JobMatchAlert($matches->all()));
                $alert->markSent();
                $sent++;

                Log::info('SendDailyJobAlerts: sent to user', [
                    'user_id' => $user->id,
                    'match_count' => $matches->count(),
                ]);
            } catch (\Throwable $e) {
                Log::error('SendDailyJobAlerts: failed to send', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('SendDailyJobAlerts: done', [
            'alerts_checked' => $alerts->count(),
            'sent' => $sent,
            'skipped' => $skipped,
        ]);
    }

    private function refreshSkillProfile(int $userId, SkillExtractor $extractor): void
    {
        $profile = \App\Models\UserSkillProfile::where('user_id', $userId)->first();

        if (!$profile || $profile->isStale()) {
            try {
                $extractor->extractAndSave($userId);
                Log::debug('SendDailyJobAlerts: refreshed skill profile', ['user_id' => $userId]);
            } catch (\Throwable $e) {
                Log::warning('SendDailyJobAlerts: failed to extract skills', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
