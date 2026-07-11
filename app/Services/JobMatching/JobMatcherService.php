<?php

namespace App\Services\JobMatching;

use App\Models\JobAlert;
use App\Models\JobMatchLog;
use App\Models\JobPost;
use App\Models\User;
use App\Models\UserSkillProfile;
use Illuminate\Support\Collection;

/**
 * JobMatcherService — Orchestrator cho Smart Job Matcher.
 *
 * Flow:
 *  1. Get active alerts + skill profiles for users
 *  2. Rule-based pre-filter: score >= threshold → top 20 candidates
 *  3. AI re-rank top 20 → top N (default 5)
 *  4. Store match logs + return results for email
 */
class JobMatcherService
{
    private const DEFAULT_LIMIT = 5;
    private const PRE_FILTER_LIMIT = 20;
    private const RULE_THRESHOLD_DEFAULT = 40;

    public function __construct(
        private RuleBasedMatcher $ruleBasedMatcher,
        private AiMatcher $aiMatcher,
    ) {}

    /**
     * Find matching jobs for one user alert.
     *
     * @return Collection<JobMatchLog>
     */
    public function matchForAlert(JobAlert $alert): Collection
    {
        $profile = $alert->skillProfile;
        if (!$profile) {
            return collect();
        }

        $jobs = $this->getCandidateJobs($alert, $profile);
        if ($jobs->isEmpty()) {
            return collect();
        }

        // Rule-based pre-filter + scoring
        $scored = $jobs->map(function (JobPost $job) use ($alert, $profile) {
            $ruleResult = $this->ruleBasedMatcher->match($profile, $job);

            return [
                'job'        => $job,
                'rule_score' => $ruleResult['score'],
                'matched'    => $ruleResult['matched'],
                'missing'    => $ruleResult['missing'],
                'signals'    => $ruleResult['signals'],
            ];
        })->filter(fn($item) => $item['rule_score'] >= ($alert->match_threshold ?? self::RULE_THRESHOLD_DEFAULT));

        if ($scored->isEmpty()) {
            return collect();
        }

        // Sort by rule score descending, take top N for AI re-rank
        $topCandidates = $scored->sortByDesc('rule_score')->take(self::PRE_FILTER_LIMIT);

        // AI re-rank
        $aiScored = $topCandidates->map(function ($item) use ($profile) {
            $ruleResult = [
                'score'   => $item['rule_score'],
                'matched' => $item['matched'],
                'missing' => $item['missing'],
            ];

            $aiResult = $this->aiMatcher->match($profile, $item['job'], $ruleResult);

            return [
                'job'        => $item['job'],
                'rule_score' => $item['rule_score'],
                'ai_score'   => $aiResult['ai_score'] ?? null,
                'matched'    => $aiResult['matched'] ?? $item['matched'],
                'missing'    => $aiResult['missing'] ?? $item['missing'],
                'signals'    => $item['signals'],
            ];
        });

        // Final sort: AI score > rule score > rule score
        $finalSorted = $aiScored->sortByDesc(function ($item) {
            return $item['ai_score'] ?? $item['rule_score'];
        })->take(self::DEFAULT_LIMIT);

        // Persist match logs
        return $finalSorted->map(function ($item) use ($alert) {
            return $this->createOrUpdateMatchLog($alert->user_id, $item);
        });
    }

    /**
     * Get matching jobs for API widget (fast, rule-based only, no AI).
     *
     * Contract:
     *  - Returns Collection<JobMatchLog> (kept stable for controller payload)
     *  - Returns empty if user has no profile OR no active JobAlert
     *  - When alert is inactive, returns empty (matches "toggled off" expectation)
     *  - When matches exist, includes them even if below threshold (UI will render
     *    score badges; we only require score > 0 so we don't surface nonsense jobs)
     *
     * @return Collection<JobMatchLog>
     */
    public function matchForWidget(User $user, int $limit = 5): Collection
    {
        $profile = UserSkillProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return collect();
        }

        $alert = JobAlert::where('user_id', $user->id)->first();
        if (!$alert || !$alert->is_active) {
            return collect();
        }

        $jobs = JobPost::published()
            ->whereDoesntHave('matchLogs', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereNotNull('viewed_at');
            })
            ->orderByDesc('is_hot')
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();

        return $jobs->map(function (JobPost $job) use ($profile, $user) {
            $ruleResult = $this->ruleBasedMatcher->match($profile, $job);

            $log = JobMatchLog::updateOrCreate(
                ['user_id' => $user->id, 'job_post_id' => $job->id],
                [
                    'rule_score' => $ruleResult['score'],
                    'matched_skills' => $ruleResult['matched'],
                    'missing_skills' => $ruleResult['missing'],
                ]
            );

            return $log;
        })->filter(fn($log) => $log->rule_score > 0)
          ->sortByDesc('rule_score')
          ->take($limit)
          ->values();
    }

    /**
     * Diagnostic state for the dashboard widget — lets the UI render the
     * correct empty-state copy ("chưa bật", "chưa có CV", "chưa có match phù hợp").
     *
     * One of:
     *   - "no_profile"   : user has no UserSkillProfile (need to upload CV first)
     *   - "no_alert"     : user has never opened settings page (no JobAlert row)
     *   - "inactive"     : user toggled Smart Matcher OFF
     *   - "no_matches"   : alert is on, but no published job scored > 0
     *   - "ok"           : matches available (caller will render the list)
     */
    public function widgetState(User $user): string
    {
        if (!UserSkillProfile::where('user_id', $user->id)->exists()) {
            return 'no_profile';
        }

        $alert = JobAlert::where('user_id', $user->id)->first();
        if (!$alert) {
            return 'no_alert';
        }
        if (!$alert->is_active) {
            return 'inactive';
        }

        // Probe whether any published job would score > 0 for this profile
        $profile = UserSkillProfile::where('user_id', $user->id)->first();
        $hasMatch = JobPost::published()
            ->limit(20)
            ->get()
            ->contains(function (JobPost $job) use ($profile) {
                return $this->ruleBasedMatcher->match($profile, $job)['score'] > 0;
            });

        return $hasMatch ? 'ok' : 'no_matches';
    }

    /**
     * @return Collection<JobPost>
     */
    private function getCandidateJobs(JobAlert $alert, UserSkillProfile $profile): Collection
    {
        // Lấy tất cả job published. Không loại trừ job đã sent gần đây vì
        // một job có thể vẫn phù hợp trong các ngày tiếp theo — việc "gửi
        // lại" là hợp lý khi vẫn nằm trong top matches. Match mới sẽ
        // ghi đè JobMatchLog cũ qua updateOrCreate.
        $query = JobPost::published();

        // Category filter
        $cats = $alert->preferred_categories ?? $profile->preferred_categories ?? [];
        if (!empty($cats)) {
            $query->whereIn('category', $cats);
        }

        // Job type filter
        $types = $alert->preferred_job_types ?? $profile->preferred_job_types ?? [];
        if (!empty($types)) {
            $query->whereIn('job_type', $types);
        }

        // Location filter
        $locs = $alert->preferred_locations ?? [];
        if (!empty($locs)) {
            $query->where(function ($q) use ($locs) {
                foreach ($locs as $loc) {
                    $q->orWhere('location', 'like', "%{$loc}%");
                }
            });
        }

        // Prefer recent jobs
        return $query->orderByDesc('published_at')
            ->orderByDesc('is_hot')
            ->limit(100)
            ->get();
    }

    private function createOrUpdateMatchLog(int $userId, array $item): JobMatchLog
    {
        return JobMatchLog::updateOrCreate(
            ['user_id' => $userId, 'job_post_id' => $item['job']->id],
            [
                'rule_score'    => $item['rule_score'],
                'ai_score'      => $item['ai_score'],
                'matched_skills' => $item['matched'],
                'missing_skills' => $item['missing'],
            ]
        );
    }
}
