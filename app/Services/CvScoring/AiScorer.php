<?php

namespace App\Services\CvScoring;

use App\Models\JobPost;
use App\Support\PiiRedactor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gọi OpenAI Chat Completions API (gpt-4o-mini) để lấy điểm 0-100 và tóm tắt
 * lý do đánh giá bằng tiếng Việt.
 *
 * Retry tối đa 1 lần nếu parse JSON lỗi. Trả về mảng ['score' => int, 'summary' => string].
 * Khi hoàn toàn thất bại: trả về null để caller fallback về match_ratio local.
 */
class AiScorer
{
    private const API_URL = 'https://api.openai.com/v1/chat/completions';
    private const MAX_RETRIES = 1;
    private const DESCRIPTION_LIMIT = 1500;
    private const CV_TEXT_LIMIT = 2500;

    public function isConfigured(): bool
    {
        $key = config('services.openai.key');
        return is_string($key) && trim($key) !== '';
    }

    /**
     * @param array{
     *   match_ratio: float,
     *   matched: array<int, string>,
     *   missing: array<int, string>
     * } $matchResult
     */
    public function score(
        JobPost $jobPost,
        string $cvText,
        array $matchResult,
        array $keywordOriginals = []
    ): ?array {
        if (!$this->isConfigured()) {
            Log::warning('AiScorer: OPENAI_API_KEY is missing');
            return null;
        }

        $payload = $this->buildPayload($jobPost, $cvText, $matchResult, $keywordOriginals);

        $attempts = 0;
        $lastError = null;

        while ($attempts <= self::MAX_RETRIES) {
            $attempts++;
            try {
                $response = Http::withToken((string) config('services.openai.key'))
                    ->timeout((int) config('services.openai.timeout', 15))
                    ->acceptJson()
                    ->asJson()
                    ->post(self::API_URL, $payload);

                if ($response->status() === 429 || $response->status() >= 500) {
                    $lastError = 'transient HTTP ' . $response->status();
                    Log::warning('AiScorer: transient error, will retry', [
                        'attempt' => $attempts,
                        'status'  => $response->status(),
                    ]);
                    continue;
                }

                if (!$response->successful()) {
                    Log::warning('AiScorer: OpenAI returned error', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                    return null;
                }

                $parsed = $this->extractJson($response->json());
                if ($parsed !== null) {
                    return $parsed;
                }

                $lastError = 'json_parse_failed';
                Log::warning('AiScorer: failed to parse JSON, retrying', [
                    'attempt' => $attempts,
                    'body'    => $response->body(),
                ]);
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning('AiScorer: exception', [
                    'attempt' => $attempts,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        Log::warning('AiScorer: gave up after retries', ['last_error' => $lastError]);
        return null;
    }

    /**
     * Build payload gửi sang OpenAI.
     */
    private function buildPayload(
        JobPost $jobPost,
        string $cvText,
        array $matchResult,
        array $keywordOriginals
    ): array {
        $model = (string) config('services.openai.model', 'gpt-4o-mini');

        $matchedCsv = implode(', ', $matchResult['matched_original'] ?? $matchResult['matched'] ?? []);
        $missingCsv = implode(', ', $matchResult['missing_original'] ?? $matchResult['missing'] ?? []);
        $keywordCsv = !empty($keywordOriginals)
            ? implode(', ', $keywordOriginals)
            : implode(', ', $matchResult['matched_original'] ?? []);

        $systemPrompt = 'You are a recruiter assistant. Compare a candidate CV against a job description and output strict JSON only — no prose, no markdown.';

            // M-6: redact PII trước khi gửi sang OpenAI — giảm thiểu
            // data leakage nếu OpenAI bị compromise hoặc key bị lộ.
            $sanitizedCvText = PiiRedactor::redact($cvText, self::CV_TEXT_LIMIT);
            $sanitizedDescription = PiiRedactor::redact(
                (string) ($jobPost->description ?? ''),
                self::DESCRIPTION_LIMIT
            );

            $userPrompt = sprintf(
                "Bài đăng: %s\nMô tả: %s\nKỹ năng JD yêu cầu: %s\nCV trích xuất: %s\nKỹ năng ứng viên khớp: %s\nKỹ năng ứng viên còn thiếu: %s\nPrior (match_ratio 0-1): %.4f\nTrả về JSON: {\"score\": <int 0-100>, \"summary\": \"<1 câu tiếng Việt ≤ 120 ký tự>\"}",
                PiiRedactor::redact($jobPost->title, 200),
                $sanitizedDescription,
                $keywordCsv,
                $sanitizedCvText,
                $matchedCsv,
                $missingCsv,
                (float) $matchResult['match_ratio']
            );

        return [
            'model'       => $model,
            'temperature' => 0.2,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ];
    }

    /**
     * Trích JSON { score, summary } từ response OpenAI.
     * Trả về null nếu không parse được.
     */
    private function extractJson($json): ?array
    {
        if (!is_array($json)) {
            return null;
        }

        $content = data_get($json, 'choices.0.message.content');
        if (!is_string($content) || trim($content) === '') {
            return null;
        }

        $content = trim($content);
        // Loại bỏ ```json ... ``` nếu model lỡ trả về markdown
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $content) ?? $content;
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return null;
        }

        $score = isset($decoded['score']) ? (int) round((float) $decoded['score']) : null;
        $summary = isset($decoded['summary']) ? trim((string) $decoded['summary']) : null;

        if ($score === null || $summary === null || $summary === '') {
            return null;
        }

        // Clamp + giới hạn độ dài summary
        $score = max(0, min(100, $score));
        if (mb_strlen($summary) > 200) {
            $summary = mb_substr($summary, 0, 200) . '…';
        }

        return ['score' => $score, 'summary' => $summary];
    }

    private function truncate(string $text, int $limit): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return mb_substr($text, 0, $limit) . '…';
    }
}