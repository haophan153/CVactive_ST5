<?php

namespace App\Services\JobMatching;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

/**
 * Trích xuất text thô từ file CV upload (PDF hoặc TXT).
 * Trả về text thuần để đưa vào SkillExtractor.
 */
class CvTextExtractor
{
    public const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    public const MAX_TEXT_LENGTH = 50000;

    public const ALLOWED_MIMES = [
        'application/pdf',
        'text/plain',
    ];

    public const ALLOWED_EXTENSIONS = ['pdf', 'txt'];

    /**
     * Trả về text thuần từ file upload. Trả về null nếu không đọc được.
     */
    public function extract(string $absolutePath, string $mime): ?string
    {
        try {
            $text = match (true) {
                str_contains($mime, 'pdf')  => $this->extractPdf($absolutePath),
                str_contains($mime, 'text') || str_contains($mime, 'plain') => $this->extractTxt($absolutePath),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::warning('CvTextExtractor: failed', ['error' => $e->getMessage(), 'mime' => $mime]);
            return null;
        }

        if ($text === null) {
            return null;
        }

        $text = $this->normalize($text);
        if (mb_strlen($text) > self::MAX_TEXT_LENGTH) {
            $text = mb_substr($text, 0, self::MAX_TEXT_LENGTH);
        }

        return $text;
    }

    private function extractPdf(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText();
    }

    private function extractTxt(string $path): string
    {
        $content = file_get_contents($path);
        return is_string($content) ? $content : '';
    }

    private function normalize(string $text): string
    {
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? $text;
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text;
        return trim($text);
    }
}