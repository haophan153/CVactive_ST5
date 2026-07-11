<?php

namespace App\Services\JobMatching;

use App\Models\JobPost;
use App\Models\UserSkillProfile;

/**
 * Rule-based job matching bằng keyword overlap.
 *
 * Dùng TF-IDF-inspired scoring:
 *  - Skills overlap (50% weight): skills matched / total user skills
 *  - Category match (20% weight): 20 if same category, else 0
 *  - Experience level match (20% weight): 20 if same level, else 0
 *  - Location match (10% weight): 10 if user prefers location, else 0
 *
 * Trả về array [score, matched, missing]
 */
class RuleBasedMatcher
{
    /** @var float Maximum score from skills overlap */
    private const SKILL_WEIGHT = 0.50;

    /** @var float Weight for category match */
    private const CATEGORY_WEIGHT = 0.20;

    /** @var float Weight for experience level match */
    private const EXPERIENCE_WEIGHT = 0.20;

    /** @var float Weight for location match */
    private const LOCATION_WEIGHT = 0.10;

    /**
     * @return array{score: int, matched: array, missing: array, signals: array}
     */
    public function match(UserSkillProfile $profile, JobPost $job): array
    {
        $skills = $profile->skills ?? [];
        $jobText = $this->buildJobText($job);

        [$matched, $missing] = $this->scoreSkills($skills, $jobText);
        $skillScore = count($skills) > 0
            ? (count($matched) / count($skills)) * 100
            : 50; // neutral if no skills extracted

        $categoryScore = $this->scoreCategory($profile, $job);
        $experienceScore = $this->scoreExperience($profile, $job);
        $locationScore = $this->scoreLocation($profile, $job);

        $finalScore = (int) round(
            $skillScore * self::SKILL_WEIGHT
            + $categoryScore * self::CATEGORY_WEIGHT
            + $experienceScore * self::EXPERIENCE_WEIGHT
            + $locationScore * self::LOCATION_WEIGHT
        );

        return [
            'score'   => min(100, max(0, $finalScore)),
            'matched' => $matched,
            'missing' => $missing,
            'signals' => [
                'skill_score'      => round($skillScore),
                'category_score'  => $categoryScore,
                'experience_score' => $experienceScore,
                'location_score'  => $locationScore,
            ],
        ];
    }

    /**
     * @return array{array, array} [matched skills, missing skills]
     */
    private function scoreSkills(array $userSkills, string $jobText): array
    {
        $jobTextLower = mb_strtolower($jobText);
        $matched = [];
        $missing = [];

        foreach ($userSkills as $skill) {
            $skillLower = mb_strtolower($skill);
            if ($this->skillMatches($skillLower, $jobTextLower)) {
                $matched[] = $skill;
            } else {
                $missing[] = $skill;
            }
        }

        return [$matched, $missing];
    }

    /**
     * Smart skill matching: exact + variants (Laravel → PHP framework).
     */
    private function skillMatches(string $skill, string $jobText): bool
    {
        // Exact match
        if (preg_match('/\b' . preg_quote($skill, '/') . '\b/iu', $jobText)) {
            return true;
        }

        // Variant mapping: framework → language/ecosystem
        $variants = [
            'laravel' => ['php', 'blade', 'eloquent', 'homestead', 'forge', 'composer'],
            'vue.js'  => ['vue', 'vuejs', 'vuejs 3', 'nuxt', 'nuxtjs', 'pinia', 'vuex', 'vuetify'],
            'react'   => ['reactjs', 'react.js', 'nextjs', 'next.js', 'redux', 'react native'],
            'node.js' => ['nodejs', 'node', 'expressjs', 'express'],
            'postgresql' => ['postgres', 'postgre'],
            'javascript' => ['js', 'ecmascript'],
            'typescript' => ['ts'],
            'docker'  => ['container', 'containerization', 'dockerfile'],
            'aws'     => ['amazon web services', 'amazon'],
            'figma'   => ['ui/ux', 'ui design', 'ux design'],
        ];

        $variantMap = $variants[$skill] ?? [];
        foreach ($variantMap as $variant) {
            if (str_contains($jobText, $variant)) {
                return true;
            }
        }

        // Reverse: if job has "Laravel" and skill is "PHP"
        foreach ($variantMap as $variant) {
            if ($variant === $skill) {
                foreach (array_keys($variants, $skill, true) as $main) {
                    if (str_contains($jobText, $main)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function scoreCategory(UserSkillProfile $profile, JobPost $job): int
    {
        $preferred = $profile->preferred_categories ?? [];
        if (empty($preferred)) {
            return 50; // neutral
        }
        return in_array($job->category, $preferred, true) ? 100 : 0;
    }

    private function scoreExperience(UserSkillProfile $profile, JobPost $job): int
    {
        $userLevel = $profile->experience_level;
        if (!$userLevel) {
            return 50; // neutral
        }

        // Experience level ordering
        $levels = ['fresher' => 1, 'junior' => 2, 'middle' => 3, 'senior' => 4, 'lead' => 5];
        $userRank = $levels[$userLevel] ?? 3;
        $jobRank = $levels[$job->experience_level] ?? 3;

        $diff = abs($userRank - $jobRank);
        if ($diff === 0) return 100;
        if ($diff === 1) return 60;
        return 20;
    }

    private function scoreLocation(UserSkillProfile $profile, JobPost $job): int
    {
        $preferred = $profile->preferred_locations ?? [];
        if (empty($preferred)) {
            return 50; // neutral
        }

        $jobLocation = mb_strtolower($job->location ?? '');
        foreach ($preferred as $pref) {
            if (str_contains($jobLocation, mb_strtolower($pref))) {
                return 100;
            }
        }
        return 0;
    }

    private function buildJobText(JobPost $job): string
    {
        $parts = [
            $job->title ?? '',
            $job->description ?? '',
            $job->location ?? '',
            $job->company_name ?? '',
            $job->category ?? '',
        ];

        $typeInfo = $job->type_info;
        if (!empty($typeInfo['label'])) {
            $parts[] = $typeInfo['label'];
        }
        $catInfo = $job->category_info;
        if (!empty($catInfo['label'])) {
            $parts[] = $catInfo['label'];
        }

        return mb_strtolower(implode(' ', $parts));
    }
}
