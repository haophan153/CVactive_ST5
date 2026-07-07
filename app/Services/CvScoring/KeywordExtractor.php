<?php

namespace App\Services\CvScoring;

use App\Models\JobPost;

/**
 * Trích xuất danh sách từ khóa / kỹ năng từ JobPost (title + category + description)
 * để làm anchor cho bước skill-matching local và đưa vào prompt GPT.
 *
 * Pipeline:
 *   1. Ghép các field liên quan thành 1 đoạn văn bản
 *   2. Lowercase + bỏ dấu tiếng Việt (để match được cả CV không dấu)
 *   3. Tách theo ký tự không phải chữ/số
 *   4. Loại bỏ stopwords + token quá ngắn
 *   5. Dedup, trả về danh sách đã sắp xếp theo tần suất giảm dần
 */
class KeywordExtractor
{
    /**
     * Danh sách stopwords tiếng Việt + tiếng Anh thường gặp trong JD.
     * Giữ nhỏ gọn, không cần đầy đủ — mục đích chính là loại bỏ các từ nhiễu.
     */
    private const STOPWORDS = [
        // Tiếng Việt
        'và', 'với', 'của', 'cho', 'từ', 'trong', 'ngoài', 'trên', 'dưới',
        'là', 'có', 'được', 'sẽ', 'đã', 'đang', 'cần', 'phải', 'nên',
        'một', 'hai', 'ba', 'các', 'những', 'này', 'kia', 'đó', 'đây',
        'khi', 'nếu', 'thì', 'mà', 'như', 'bởi', 'vì', 'do', 'nên',
        'tại', 'theo', 'về', 'để', 'đến', 'qua', 'giữa',
        'ứng', 'viên', 'công', 'việc', 'ty', 'công ty',
        'kinh', 'nghiệm', 'năm', 'tháng', 'tuần', 'ngày',
        'trở', 'lên', 'xuống', 'trên', 'dưới',
        'tốt', 'tối', 'thiểu', 'đa', 'cao', 'thấp',
        'mới', 'cũ', 'lớn', 'nhỏ', 'nhiều', 'ít',
        // Tiếng Anh
        'and', 'or', 'with', 'of', 'for', 'from', 'in', 'out', 'on', 'at',
        'is', 'are', 'was', 'were', 'be', 'been', 'being',
        'have', 'has', 'had', 'do', 'does', 'did',
        'will', 'would', 'should', 'could', 'can', 'may', 'might', 'must',
        'the', 'a', 'an', 'this', 'that', 'these', 'those',
        'if', 'then', 'else', 'when', 'where', 'why', 'how', 'what', 'which', 'who', 'whom',
        'to', 'by', 'as', 'so', 'than', 'too', 'very',
        'you', 'your', 'we', 'our', 'they', 'their', 'it', 'its',
        'i', 'me', 'my', 'he', 'she', 'his', 'her',
        'not', 'no', 'yes', 'any', 'all', 'some', 'most', 'more', 'less',
        'job', 'work', 'team', 'company', 'candidate', 'applicant',
        'experience', 'year', 'years', 'month', 'months',
        'required', 'plus', 'must', 'good', 'great', 'strong', 'familiar',
    ];

    private const MIN_TOKEN_LENGTH = 2;
    private const MAX_KEYWORDS = 25;

    /**
     * Trả về danh sách keyword đã normalize (lowercase, không dấu), kèm dạng gốc (có dấu)
     * để tiện hiển thị và lookup cả hai dạng CV.
     *
     * @return array<int, array{key: string, original: string}>
     */
    public function extract(JobPost $jobPost): array
    {
        $text = $this->buildSourceText($jobPost);

        // Tokenize thô theo ký tự không phải chữ/số (giữ cả tiếng Việt)
        $rawTokens = preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $stats = [];
        foreach ($rawTokens as $token) {
            $lower = mb_strtolower($token);
            if (mb_strlen($lower) < self::MIN_TOKEN_LENGTH) {
                continue;
            }
            if (in_array($lower, self::STOPWORDS, true)) {
                continue;
            }
            // Bỏ các token là số thuần (năm kinh nghiệm v.v.) để không tính là kỹ năng
            if (preg_match('/^\d+$/u', $lower)) {
                continue;
            }

            $key = $this->removeDiacritics($lower);

            // Gom theo key không dấu, giữ lại original đẹp nhất (lần đầu xuất hiện)
            if (!isset($stats[$key])) {
                $stats[$key] = ['original' => $lower, 'count' => 0];
            }
            $stats[$key]['count']++;
        }

        // Sắp xếp theo tần suất giảm dần
        uasort($stats, fn ($a, $b) => $b['count'] <=> $a['count']);

        $result = [];
        foreach (array_slice($stats, 0, self::MAX_KEYWORDS, true) as $key => $info) {
            $result[] = [
                'key'      => $key,            // không dấu
                'original' => $info['original'] // có dấu
            ];
        }

        return $result;
    }

    /**
     * Trả về danh sách "key" không dấu (dùng cho SkillMatcher).
     *
     * @return array<int, string>
     */
    public function extractKeys(JobPost $jobPost): array
    {
        return array_map(fn ($k) => $k['key'], $this->extract($jobPost));
    }

    /**
     * Ghép các field liên quan của JobPost thành 1 đoạn text.
     */
    private function buildSourceText(JobPost $jobPost): string
    {
        $parts = [
            (string) $jobPost->title,
            (string) ($jobPost->category ?? ''),
            (string) ($jobPost->company_name ?? ''),
            (string) ($jobPost->description ?? ''),
        ];

        return implode("\n", array_filter($parts, fn ($p) => trim($p) !== ''));
    }

    /**
     * Bỏ dấu tiếng Việt (NFD + remove combining marks).
     */
    private function removeDiacritics(string $str): string
    {
        // Dùng 'de' transliteration ID của intl — fallback an toàn nếu extension intl chưa bật
        if (function_exists('transliterator_create')) {
            $t = @transliterator_create('Any-Latin; Latin-ASCII; [-翿] remove');
            if ($t !== null) {
                $normalized = transliterator_transliterate($t, $str);
                if ($normalized !== false) {
                    return strtolower($normalized);
                }
            }
        }

        // Fallback: NFD + bỏ combining marks thủ công
        $normalized = \Normalizer::normalize($str, \Normalizer::FORM_D);
        if ($normalized === false) {
            return strtolower($str);
        }
        $ascii = preg_replace('/\p{Mn}+/u', '', $normalized);
        return strtolower($ascii ?? $str);
    }
}