<?php

namespace App\Support;

/**
 * M-6: Strip PII (personally identifiable information) từ CV text
 * trước khi gửi sang OpenAI để giảm thiểu:
 *   - PII leakage nếu OpenAI bị compromise
 *   - GDPR exposure (CV applicant text chứa email/phone/SSN)
 *   - Prompt injection qua JD content (replace-by-OpenAI chỉ thấy data đã clean)
 *
 * Strategy:
 *   - Email → email_pattern_generic@example.invalid
 *   - Phone (10-15 digits, có thể có +/dấu cách) → +84-XXX-XXX-XXX
 *   - CMND/CCCD (9-12 digits liên tiếp) → redacted
 *   - Số tài khoản ngân hàng (8-16 digits liên tiếp) → redacted
 *   - URL http/https → link_redacted
 */
class PiiRedactor
{
    private const REDACTED_EMAIL = '[REDACTED-EMAIL]';
    private const REDACTED_PHONE = '[REDACTED-PHONE]';
    private const REDACTED_ID    = '[REDACTED-ID]';
    private const REDACTED_BANK  = '[REDACTED-BANK]';
    private const REDACTED_URL   = '[REDACTED-URL]';

    public static function redact(string $text, int $limit = 2500): string
    {
        // 1. Email
        $text = preg_replace(
            '/[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}/',
            self::REDACTED_EMAIL,
            $text
        );

        // 2. URL
        $text = preg_replace(
            '/https?:\/\/[^\s<>\)]+/',
            self::REDACTED_URL,
            $text
        );

        // 3. Phone (Vietnam + international)
        $text = preg_replace(
            '/(?:\+?84|0)[\s\.\-]?\d{2,3}[\s\.\-]?\d{3}[\s\.\-]?\d{3,4}\b/',
            self::REDACTED_PHONE,
            $text
        );

        // 4. CMND/CCCD (9-12 digits) — chỉ khi có keyword CMND/CCCD đi kèm
        // tránh match nhầm số năm kinh nghiệm, số lượng project
        $text = preg_replace(
            '/(CMND|CCCD|CMTND|CMT|SSN|Social Security|Passport)\s*[:#]?\s*\d{9,12}\b/i',
            '$1 ' . self::REDACTED_ID,
            $text
        );

        // 5. Bank account (10-16 digits liên tiếp — chỉ khi keyword ngân hàng/TK)
        $text = preg_replace(
            '/(STK|TK|tài khoản|account|ACN)\s*[:#]?\s*\d{8,16}\b/i',
            '$1 ' . self::REDACTED_BANK,
            $text
        );

        // 6. Truncate
        if (mb_strlen($text) > $limit) {
            $text = mb_substr($text, 0, $limit) . '…';
        }

        return $text;
    }
}
