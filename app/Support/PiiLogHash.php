<?php

namespace App\Support;

/**
 * L-6: Hash PII cho log — che email trước khi ghi vào cv-access-*.log
 * vì log có thể bị leak qua backup / cloud sync / SSH kdump.
 * GDPR: yêu cầu right-to-erasure; hash giữ traceability mà không leak raw PII.
 */
class PiiLogHash
{
    public static function email(?string $email): ?string
    {
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        [$local, $domain] = explode('@', $email, 2);
        // Giữ local hash + domain nguyên (domain không phải PII nặng)
        return substr(hash('sha256', $local), 0, 8) . '@' . $domain;
    }

    public static function phone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }
        // Chỉ giữ last 3 digits (cho forensics)
        $clean = preg_replace('/\D+/', '', $phone);
        if (mb_strlen($clean) < 3) {
            return null;
        }
        return str_repeat('X', mb_strlen($clean) - 3) . substr($clean, -3);
    }
}
