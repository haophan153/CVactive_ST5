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

        $deletedPending = (clone $stalePending)->delete();
        $deletedFailed  = (clone $oldFailed)->delete();

        $this->info("Đã xóa {$deletedPending} pending, {$deletedFailed} failed.");
        return self::SUCCESS;
    }
}
