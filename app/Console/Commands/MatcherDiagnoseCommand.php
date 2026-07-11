<?php

namespace App\Console\Commands;

use App\Models\JobAlert;
use App\Models\JobMatchLog;
use App\Models\JobPost;
use App\Models\UserSkillProfile;
use App\Services\JobMatching\JobMatcherService;
use App\Services\JobMatching\RuleBasedMatcher;
use Illuminate\Console\Command;

/**
 * Pipeline diagnostic cho Smart Job Matcher — không gọi AI.
 *
 * Usage:
 *   php artisan matcher:diagnose           ← scan tất cả user đang bật alert
 *   php artisan matcher:diagnose --user=13 ← chi tiết 1 user
 *   php artisan matcher:diagnose --user=13 --relax  ← giảm filter để test
 *   php artisan matcher:diagnose --fix     ← tự sửa alert filter nếu đang quá chặt
 */
class MatcherDiagnoseCommand extends Command
{
    protected $signature = 'matcher:diagnose
        {--user= : User ID cụ thể}
        {--relax : Loại bỏ filter category/location để test pipeline}
        {--fix : Tự relax alert của user để dễ match}';

    protected $description = 'Chẩn đoán pipeline Smart Job Matcher (không gọi AI).';

    public function handle(JobMatcherService $matcher, RuleBasedMatcher $rule): int
    {
        $this->line('═══════════════════════════════════════════════════════');
        $this->info('  SMART JOB MATCHER — Pipeline Diagnostic');
        $this->line('═══════════════════════════════════════════════════════');
        $this->newLine();

        // ── Step 1: DB overview ───────────────────────────────────
        $totalJobs     = JobPost::count();
        $publishedJobs = JobPost::published()->count();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total JobPosts',           $totalJobs],
                ['Published + not expired',  $publishedJobs],
                ['UserSkillProfiles',        UserSkillProfile::count()],
                ['Active JobAlerts',         JobAlert::where('is_active', true)->count()],
                ['JobMatchLogs (total)',     JobMatchLog::count()],
                ['JobMatchLogs (sent_at set)', JobMatchLog::whereNotNull('sent_at')->count()],
            ]
        );

        // ── Step 2: Per-user detail ───────────────────────────────
        $query = JobAlert::where('is_active', true)->with('user');
        if ($uid = $this->option('user')) {
            $query->where('user_id', (int) $uid);
        }
        $alerts = $query->get();

        if ($alerts->isEmpty()) {
            $this->warn('Không có alert active nào.');
            return self::SUCCESS;
        }

        foreach ($alerts as $alert) {
            $this->diagnoseUser($alert, $rule, $matcher);
        }

        // ── Step 3: Fix mode ──────────────────────────────────────
        if ($this->option('fix')) {
            $this->fixAlerts($alerts);
        }

        return self::SUCCESS;
    }

    private function diagnoseUser(JobAlert $alert, RuleBasedMatcher $rule, JobMatcherService $matcher): void
    {
        $uid = $alert->user_id;
        $this->line("───────────────────────────────────────────────────────");
        $this->info("USER #{$uid} — {$alert->user->email}");

        // A) Profile check
        $profile = UserSkillProfile::where('user_id', $uid)->first();
        if (!$profile) {
            $this->error('  ❌ NO SKILL PROFILE → matchForAlert returns empty');
            return;
        }

        $this->line("  ✓ Profile: " . count($profile->skills ?? []) . ' skills, level=' . ($profile->experience_level ?? 'null'));
        $this->line('    Skills: ' . implode(', ', array_slice($profile->skills ?? [], 0, 8)));

        // B) Alert config
        $threshold = $alert->match_threshold ?? 60;
        $this->line("  ⚙ Threshold: {$threshold}%");
        $this->line('    Preferred categories: ' . json_encode($alert->preferred_categories));
        $this->line('    Preferred job_types:  ' . json_encode($alert->preferred_job_types));
        $this->line('    Preferred locations:  ' . json_encode($alert->preferred_locations));

        // C) Candidate jobs after filtering
        $query = JobPost::published()
            ->whereDoesntHave('matchLogs', function ($q) use ($uid) {
                $q->where('user_id', $uid)
                  ->whereNotNull('sent_at')
                  ->where('sent_at', '>=', now()->subDays(7));
            });

        $cats = $alert->preferred_categories ?? $profile->preferred_categories ?? [];
        $types = $alert->preferred_job_types ?? $profile->preferred_job_types ?? [];
        $locs = $alert->preferred_locations ?? [];

        if ($this->option('relax')) {
            $cats = $types = $locs = [];
            $this->warn('  [RELAX MODE] Bỏ filter category/job_type/location');
        }

        if (!empty($cats))  $query->whereIn('category', $cats);
        if (!empty($types)) $query->whereIn('job_type', $types);
        if (!empty($locs)) {
            $query->where(function ($q) use ($locs) {
                foreach ($locs as $loc) {
                    $q->orWhere('location', 'like', "%{$loc}%");
                }
            });
        }

        $candidateCount = (clone $query)->count();
        $this->line("  📋 Candidates sau filter: {$candidateCount} job");

        if ($candidateCount === 0) {
            $this->error('  ❌ ZERO CANDIDATES — bị filter chặn hết');
            $this->showSampleJobs();
            return;
        }

        // D) Rule-based scoring on top 5
        $this->line('  📊 Rule-based scores (top 5):');
        $samples = $query->orderByDesc('published_at')->limit(5)->get();
        foreach ($samples as $job) {
            $r = $rule->match($profile, $job);
            $pass = $r['score'] >= $threshold ? '✓' : '✗';
            $this->line(sprintf(
                "    %s #%d score=%2d  %s — %s | matched=%s",
                $pass,
                $job->id,
                $r['score'],
                mb_substr($job->title ?? '', 0, 35),
                mb_substr($job->location ?? '', 0, 15),
                implode(',', array_slice($r['matched'], 0, 4))
            ));
        }

        // E) Run actual pipeline
        $matches = $matcher->matchForAlert($alert);
        $this->line("  → matchForAlert result: " . $matches->count() . ' matches');

        if ($matches->count() === 0 && $candidateCount > 0) {
            $this->warn('  💡 Có jobs nhưng KHÔNG match. Nguyên nhân: threshold quá cao hoặc skills profile lệch với job data.');
        }
    }

    private function showSampleJobs(): void
    {
        $samples = JobPost::published()->limit(3)->get(['id', 'title', 'category', 'job_type', 'location']);
        $this->line('    Sample jobs có trong DB:');
        foreach ($samples as $j) {
            $this->line("      #{$j->id} {$j->title} | cat={$j->category} | type={$j->job_type} | loc={$j->location}");
        }
    }

    private function fixAlerts($alerts): void
    {
        $this->newLine();
        $this->line('───────────────────────────────────────────────────────');
        $this->info('FIX MODE — đang relax alert filter');

        foreach ($alerts as $alert) {
            $alert->update([
                'match_threshold'        => 30,
                'preferred_categories'    => null,
                'preferred_job_types'     => null,
                'preferred_locations'     => null,
            ]);
            $this->line("  ✓ User #{$alert->user_id}: threshold=30, bỏ preferred_*");
        }

        $this->newLine();
        $this->info('Chạy lại matcher:diagnose --user=<id> để xem kết quả mới.');
    }
}