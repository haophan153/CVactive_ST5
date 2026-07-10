<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class CleanupStalePayments extends Command
{
    protected $signature = 'payments:cleanup
        {--minutes=30 : Xoá payment pending cũ hơn N phút}
        {--days=90    : Xoá payment failed cũ hơn N ngày}
        {--dry-run    : Chỉ liệt kê, không xóa}';

    protected $description = 'Xoá các payment pending quá hạn (user bỏ giữa chừng) và failed cũ.';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $days    = (int) $this->option('days');
        $dryRun  = (bool) $this->option('dry-run');

        $stalePending = Payment::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($minutes));

        $oldFailed = Payment::where('status', 'failed')
            ->where('created_at', '<', now()->subDays($days));

        $pendingCount = (clone $stalePending)->count();
        $failedCount  = (clone $oldFailed)->count();

        $this->info("Tìm thấy:");
        $this->line("  - {$pendingCount} payment pending > {$minutes} phút");
        $this->line("  - {$failedCount} payment failed > {$days} ngày");

        if ($dryRun) {
            $this->warn('--dry-run: KHÔNG xóa.');
            return self::SUCCESS;
        }

        if (! $this->confirm('Xóa các payment này?', true)) {
            $this->info('Đã hủy.');
            return self::SUCCESS;
        }

        // SECURITY (defense in depth): Hard cap mỗi lần xóa để chống
        // runaway script. Tổng số vẫn đúng nhờ loop đến khi hết.
        $batchSize = 1000;
        $deletedPending = 0;
        do {
            $deleted = (clone $stalePending)->limit($batchSize)->delete();
            $deletedPending += $deleted;
        } while ($deleted > 0);

        $deletedFailed = 0;
        do {
            $deleted = (clone $oldFailed)->limit($batchSize)->delete();
            $deletedFailed += $deleted;
        } while ($deleted > 0);

        $this->info("Đã xóa {$deletedPending} pending, {$deletedFailed} failed.");
        return self::SUCCESS;
    }
}
