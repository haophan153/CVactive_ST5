<?php

namespace App\Services\CvScoring;

/**
 * So khớp CV text với danh sách keyword từ JobPost, trả về:
 *   - match_ratio: tỉ lệ keyword xuất hiện trong CV (0..1)
 *   - matched: danh sách keyword khớp
 *   - missing: danh sách keyword còn thiếu
 *
 * Matching dựa trên dạng không dấu để chịu được CV viết không dấu.
 * Dùng word-boundary để tránh match ngẫu nhiên (ví dụ "go" không khớp với "google"
 * nếu "google" đứng riêng — tuy nhiên ta dùng chiến lược substring có kiểm soát).
 */
class SkillMatcher
{
    /**
     * @param string $cvText Văn bản CV đã được PdfTextExtractor trích xuất
     * @param array<int, string> $keywordKeys Danh sách keyword (dạng không dấu)
     * @param array<int, array{key:string,original:string}> $keywordMap Optional map đầy đủ để trả original
     * @return array{
     *     match_ratio: float,
     *     matched: array<int, string>,
     *     missing: array<int, string>,
     *     matched_original: array<int, string>,
     *     missing_original: array<int, string>
     * }
     */
    public function match(string $cvText, array $keywordKeys, array $keywordMap = []): array
    {
        if (empty($keywordKeys)) {
            return [
                'match_ratio'       => 0.0,
                'matched'           => [],
                'missing'           => [],
                'matched_original'  => [],
                'missing_original'  => [],
            ];
        }

        // Chuẩn hóa CV: lowercase + bỏ dấu
        $normalizedCv = $this->normalize($cvText);
        if ($normalizedCv === '') {
            return [
                'match_ratio'       => 0.0,
                'matched'           => [],
                'missing'           => array_values($keywordKeys),
                'matched_original'  => [],
                'missing_original'  => $this->originalsFor(array_values($keywordKeys), $keywordMap),
            ];
        }

        // Build map: key → original (lần đầu tiên gặp)
        $originalByKey = [];
        foreach ($keywordMap as $kw) {
            $originalByKey[$kw['key']] = $kw['original'];
        }
        foreach ($keywordKeys as $key) {
            $originalByKey[$key] = $originalByKey[$key] ?? $key;
        }

        $matched = [];
        $missing = [];

        foreach ($keywordKeys as $key) {
            if ($this->containsToken($normalizedCv, $key)) {
                $matched[] = $key;
            } else {
                $missing[] = $key;
            }
        }

        return [
            'match_ratio'      => count($keywordKeys) > 0 ? round(count($matched) / count($keywordKeys), 4) : 0.0,
            'matched'          => $matched,
            'missing'          => $missing,
            'matched_original' => $this->originalsFor($matched, $originalByKey),
            'missing_original' => $this->originalsFor($missing, $originalByKey),
        ];
    }

    /**
     * Chuẩn hóa CV text: lowercase + bỏ dấu + bỏ ký tự đặc biệt (giữ chữ/số/khoảng trắng)
     */
    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        if (function_exists('transliterator_create')) {
            $t = @transliterator_create('Any-Latin; Latin-ASCII; [-翿] remove');
            if ($t !== null) {
                $normalized = transliterator_transliterate($t, $text);
                if ($normalized !== false) {
                    $text = $normalized;
                }
            }
        } else {
            $n = \Normalizer::normalize($text, \Normalizer::FORM_D);
            if ($n !== false) {
                $text = preg_replace('/\p{Mn}+/u', '', $n) ?? $text;
            }
        }
        // Chuyển mọi ký tự không phải chữ/số thành khoảng trắng để tách từ
        $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text) ?? $text;
        return trim($text);
    }

    /**
     * Kiểm tra token có xuất hiện trong text không, dùng whole-word matching.
     * Đối với keyword ngắn (<= 2 ký tự) thì dùng substring match để tránh miss.
     * Có fallback: nếu cả 2 phía đều dài >=4, thử prefix match có whole-word cuối
     * để chịu được "react" khớp "reactjs", "react-native", "react.js".
     */
    private function containsToken(string $normalizedText, string $token): bool
    {
        $token = trim($token);
        if ($token === '') {
            return false;
        }

        // Whole-word match với Unicode boundary
        $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($token, '/') . '(?![\p{L}\p{N}])/u';
        if (preg_match($pattern, $normalizedText)) {
            return true;
        }

        // Fallback cho keyword có dấu "-" hoặc "." như "node.js": thử cả dạng đã được bỏ dấu nối
        $alt = preg_replace('/[._-]+/', '', $token);
        if ($alt !== $token && $alt !== '' && preg_match($pattern, $normalizedText)) {
            return true;
        }

        // Prefix fallback: nếu token đủ dài (>=4), match khi keyword là phần đầu của một từ dài hơn
        // trong CV (cho phép "react" khớp "reactjs", "reactjs" khớp "reactjs" và "react")
        if (mb_strlen($token) >= 4) {
            // Match token xuất hiện ở đầu một từ dài hơn (vd: "reactjs" trong CV khớp token "react")
            $prefixPattern = '/(?<![\p{L}\p{N}])' . preg_quote($token, '/') . '[\p{L}\p{N}]/u';
            if (preg_match($prefixPattern, $normalizedText)) {
                return true;
            }
            // Ngược lại: token trong CV xuất hiện ở đầu một từ dài hơn token (vd: "react" trong CV khớp token "reactjs")
            foreach ($this->extractWords($normalizedText) as $word) {
                if (mb_strlen($word) > mb_strlen($token) && str_starts_with($word, $token)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Tách text thành các từ (đã chuẩn hoá) để duyệt.
     * @return array<int, string>
     */
    private function extractWords(string $normalizedText): array
    {
        $parts = preg_split('/\s+/u', trim($normalizedText)) ?: [];
        return array_values(array_filter($parts, fn($p) => $p !== ''));
    }

    /**
     * @param array<int, string> $keys
     * @param array<string, string>|array<int, array{key:string,original:string}> $mapOrKeys
     * @return array<int, string>
     */
    private function originalsFor(array $keys, array $mapOrKeys): array
    {
        $map = [];
        if (!empty($mapOrKeys) && array_is_list($mapOrKeys)) {
            // dạng [['key' => ..., 'original' => ...], ...]
            foreach ($mapOrKeys as $kw) {
                $map[$kw['key']] = $kw['original'];
            }
        } else {
            $map = $mapOrKeys;
        }

        return array_values(array_map(fn ($k) => $map[$k] ?? $k, $keys));
    }
}