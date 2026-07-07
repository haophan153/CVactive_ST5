<?php

namespace Database\Seeders;

use App\Models\JobPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Generate logo PNG cho từng công ty trong bảng job_posts.
 *
 * - Đếm các công ty unique (theo company_name)
 * - Render 1 logo PNG / công ty, lưu storage/app/public/logos/<slug>.png
 * - Gán company_logo = logos/<slug>.png cho tất cả jobs thuộc công ty đó
 *
 * Chạy: php artisan db:seed --class=CompanyLogoSeeder
 *
 * Idempotent: bỏ qua công ty đã có logo (và các jobs đã gán logo khớp).
 */
class CompanyLogoSeeder extends Seeder
{
    public function run(): void
    {
        $renderer = new CompanyLogoRenderer();
        Storage::disk('public')->makeDirectory('logos');

        // Lấy danh sách công ty unique (theo company_name)
        $companies = JobPost::whereNotNull('company_name')
            ->select('company_name')
            ->distinct()
            ->orderBy('company_name')
            ->pluck('company_name');

        if ($companies->isEmpty()) {
            $this->command->warn('Không có job_post nào có company_name.');
            return;
        }

        $success = 0;
        $skipped = 0;

        foreach ($companies as $companyName) {
            $name = trim($companyName);
            if (! $name) {
                $skipped++;
                continue;
            }

            $slug = CompanyLogoRenderer::slugFor($name);
            $path = 'logos/' . $slug . '.png';

            // Nếu đã có file + đã gán cho jobs thì skip
            $alreadyAssigned = JobPost::where('company_name', $name)
                ->where('company_logo', $path)
                ->exists();

            if ($alreadyAssigned && Storage::disk('public')->exists($path)) {
                $skipped++;
                continue;
            }

            try {
                // Render & lưu
                $png = $renderer->toPng($renderer->render($slug, $name));
                Storage::disk('public')->put($path, $png);

                // Assign cho toàn bộ jobs của công ty này
                $updated = JobPost::where('company_name', $name)->update(['company_logo' => $path]);

                $success++;
                $this->command->info("✓ {$name} → {$path} ({$updated} job(s))");
            } catch (\Throwable $e) {
                $this->command->error("✗ {$name}: {$e->getMessage()}");
            }
        }

        $this->command->newLine();
        $this->command->info("Done. success={$success}, skipped={$skipped}");
    }
}