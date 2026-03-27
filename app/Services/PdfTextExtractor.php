<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class PdfTextExtractor
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Trích xuất text từ file PDF
     * Hỗ trợ cả public disk (legacy) và local disk (private storage)
     *
     * @param string $filePath Đường dẫn file (không bao gồm prefix disk)
     * @param string $disk 'public' hoặc 'local' (default: 'local' cho bảo mật)
     * @return string
     */
    public function extractFromFile(string $filePath, string $disk = 'local'): string
    {
        // Nếu là local disk (private), đường dẫn đã bao gồm 'private/'
        if ($disk === 'local') {
            $fullPath = storage_path('app/private/' . $filePath);
        } else {
            $fullPath = Storage::disk('public')->path($filePath);
        }

        if (!file_exists($fullPath)) {
            return '';
        }

        try {
            $pdf = $this->parser->parseFile($fullPath);
            $text = $pdf->getText();
            return $this->normalizeText($text);
        } catch (\Exception $e) {
            return '';
        }
    }

    public function extractFromContent(string $content): string
    {
        try {
            $pdf = $this->parser->parseContent($content);
            $text = $pdf->getText();
            return $this->normalizeText($text);
        } catch (\Exception $e) {
            return '';
        }
    }

    private function normalizeText(string $text): string
    {
        // Remove excessive whitespace and normalize line breaks
        $text = preg_replace('/\s+/', ' ', $text);
        // Trim whitespace
        $text = trim($text);
        return $text;
    }
}
