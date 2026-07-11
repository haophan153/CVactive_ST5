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
     * @return Collection<JobMatchLog>
     */
    public function matchForWidget(User $user, int $limit = 5): Collection
    {
        $profile = UserSkillProfile::where('user_id', $user->id)->first();
        if (!$profile) {
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
        })->filter(fn($log) => $log->rule_score >= self::RULE_THRESHOLD_DEFAULT)
          ->sortByDesc('rule_score')
          ->take($limit)
          ->values();
    }

    /**
     * @return Collection<JobPost>
     */
    private function getCandidateJobs(JobAlert $alert, UserSkillProfile $profile): Collection
    {
        $query = JobPost::published()
            ->whereDoesntHave('matchLogs', function ($q) {
                // Exclude jobs already sent within last 7 days
                $q->whereNotNull('sent_at')
                  ->where('sent_at', '>=', now()->subDays(7));
            });

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
