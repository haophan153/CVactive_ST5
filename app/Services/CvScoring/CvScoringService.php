<?php

namespace App\Services\CvScoring;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrator: kết hợp KeywordExtractor → SkillMatcher → AiScorer
 * để ra điểm cuối cùng và persist vào JobApplication.
 *
 * Final score = 0.7 * local_score + 0.3 * gpt_score
 *   - local_score = match_ratio * 100  (deterministic, chịu được khi GPT fail)
 *   - gpt_score   = OpenAI trả về (0-100)
 * Nếu GPT fail, fallback về local_score thuần (× 100).
 */
class CvScoringService
{
    private const LOCAL_WEIGHT = 0.7;
    private const GPT_WEIGHT   = 0.3;

    public function __construct(
        private KeywordExtractor $keywords,
        private SkillMatcher $matcher,
        private AiScorer $ai
    ) {}

    /**
     * Chấm điểm một JobApplication và lưu DB.
     *
     * @return array{
     *   score: int,
     *   summary: string,
     *   breakdown: array<string, mixed>
     * }
     */
    public function scoreAndStore(JobApplication $application): array
    {
        $jobPost = $application->jobPost;
        if (!$jobPost) {
            return $this->emptyResult('Không tìm thấy JobPost');
        }

        $cvText = (string) ($application->cv_text ?? '');
        if (trim($cvText) === '') {
            $result = [
                'score'   => 0,
                'summary' => 'CV chưa có text để chấm điểm (PDF có thể là ảnh scan).',
                'breakdown' => [
                    'match_ratio'      => 0.0,
                    'matched_keywords' => [],
                    'missing_keywords' => [],
                    'gpt_score'        => null,
                    'final_score'      => 0,
                    'model'            => config('services.openai.model', 'gpt-4o-mini'),
                    'source'           => 'local_only',
                ],
            ];
            $this->persist($application, $result);
            return $result;
        }

        $keywordMap = $this->keywords->extract($jobPost);
        $keywordKeys = array_map(fn ($k) => $k['key'], $keywordMap);
        $keywordOriginals = array_map(fn ($k) => $k['original'], $keywordMap);

        $match = $this->matcher->match($cvText, $keywordKeys, $keywordMap);
        $localScore = (int) round($match['match_ratio'] * 100);

        $aiResult = null;
        if ($this->ai->isConfigured()) {
            $aiResult = $this->ai->score($jobPost, $cvText, $match, $keywordOriginals);
        }

        if ($aiResult !== null) {
            $finalScore = (int) round(
                self::LOCAL_WEIGHT * $localScore + self::GPT_WEIGHT * $aiResult['score']
            );
            $finalScore = max(0, min(100, $finalScore));

            $result = [
                'score'   => $finalScore,
                'summary' => $aiResult['summary'],
                'breakdown' => [
                    'match_ratio'      => $match['match_ratio'],
                    'local_score'      => $localScore,
                    'gpt_score'        => $aiResult['score'],
                    'final_score'      => $finalScore,
                    'matched_keywords' => $match['matched_original'],
                    'missing_keywords' => $match['missing_original'],
                    'model'            => config('services.openai.model', 'gpt-4o-mini'),
                    'source'           => 'hybrid',
                ],
            ];
        } else {
            $result = [
                'score'   => $localScore,
                'summary' => $localScore >= 70
                    ? 'Ứng viên khớp nhiều kỹ năng được yêu cầu.'
                    : ($localScore >= 40
                        ? 'Ứng viên khớp một phần kỹ năng, cần đánh giá thêm.'
                        : 'Ứng viên còn thiếu nhiều kỹ năng cốt lõi của JD.'),
                'breakdown' => [
                    'match_ratio'      => $match['match_ratio'],
                    'local_score'      => $localScore,
                    'gpt_score'        => null,
                    'final_score'      => $localScore,
                    'matched_keywords' => $match['matched_original'],
                    'missing_keywords' => $match['missing_original'],
                    'model'            => config('services.openai.model', 'gpt-4o-mini'),
                    'source'           => 'local_only',
                ],
            ];
        }

        $this->persist($application, $result);
        return $result;
    }

    /**
     * Lưu kết quả vào JobApplication.
     */
    private function persist(JobApplication $application, array $result): void
    {
        try {
            $application->forceFill([
                'ai_score'     => $result['score'],
                'ai_summary'   => $result['summary'],
                'ai_breakdown' => $result['breakdown'],
                'ai_scored_at' => now(),
            ])->save();
        } catch (\Throwable $e) {
            Log::error('CvScoringService: failed to persist ai_score', [
                'application_id' => $application->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    private function emptyResult(string $message): array
    {
        return [
            'score'   => 0,
            'summary' => $message,
            'breakdown' => [
                'match_ratio'      => 0.0,
                'matched_keywords' => [],
                'missing_keywords' => [],
                'gpt_score'        => null,
                'final_score'      => 0,
                'model'            => config('services.openai.model', 'gpt-4o-mini'),
                'source'           => 'local_only',
            ],
        ];
    }
}