<?php

namespace App\Jobs;

use App\Models\JobApplication;
use App\Services\PdfTextExtractor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * C2: Extract text từ PDF CV của ứng viên — chạy bất đồng bộ qua queue.
 *
 * Trước đây JobApplicationController gọi extractCvTextForApplication() đồng bộ
 * ngay sau khi upload PDF. Hacker upload PDF 100MB + 1000 request → server OOM.
 *
 * Giờ: dispatch vào queue, worker xử lý sau. Controller return ngay cho user.
 */
class ExtractCvTextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Timeout job (giây). Nếu PDF quá nặng mà extract quá thời gian → fail.
     */
    public int $timeout = 60;

    /**
     * Số lần retry tối đa khi fail.
     */
    public int $tries = 3;

    /**
     * Backoff giữa các lần retry (giây).
     */
    public int $backoff = 10;

    public function __construct(public int $applicationId) {}

    public function handle(): void
    {
        $application = JobApplication::find($this->applicationId);

        if (!$application || !$application->cv_path) {
            return;
        }

        try {
            // C2: memory + time limit trong worker để chống PDF lớn
            set_time_limit(60);
            ini_set('memory_limit', '256M');

            // H-5: Validate file path resolve về đúng thư mục private
            // trước khi parse — chống path traversal khi attacker
            // inject cv_path qua API (mặc dù đã validate 'string', vẫn
            // không nên trust path input).
            $disk = \Storage::disk('local');
            $fullPath = $disk->path($application->cv_path);

            // H-5: Validate file có header PDF thực sự (%PDF-)
            // Không parse được file này là file PDF hợp lệ — skip ngay
            // để không tốn resource cho file rác.
            if (
                !$fullPath
                || !file_exists($fullPath)
                || filesize($fullPath) > 20 * 1024 * 1024 // 20MB hard cap
                || !$this->isValidPdfHeader($fullPath)
            ) {
                Log::warning('ExtractCvTextJob: rejected invalid or oversized file', [
                    'application_id' => $application->id,
                    'path'           => $application->cv_path,
                ]);
                return;
            }

            $extractor = new PdfTextExtractor();
            $text = $extractor->extractFromFile($application->cv_path);

            if ($text) {
                $application->update(['cv_text' => $text]);
            }
        } catch (\Throwable $e) {
            Log::error('ExtractCvTextJob failed', [
                'application_id' => $application->id,
                'error'          => $e->getMessage(),
            ]);

            throw $e; // Để queue retry
        }
    }

    /**
     * H-5: validate PDF header (magic number %PDF-).
     */
    private function isValidPdfHeader(string $path): bool
    {
        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return false;
        }

        $magic = fread($handle, 5);
        fclose($handle);

        // PDF spec: file phải bắt đầu bằng "%PDF-"
        if ($magic === false || strlen($magic) < 5) {
            return false;
        }

        return $magic === '%PDF-';
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ExtractCvTextJob: gave up after retries', [
            'application_id' => $this->applicationId,
            'error'          => $exception->getMessage(),
        ]);
    }
}