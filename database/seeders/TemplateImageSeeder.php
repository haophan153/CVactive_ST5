<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Generate thumbnail CV mockup thật (SVG) cho từng template.
 * Mỗi template có 1 layout + 1 tone màu riêng → render PNG-like preview.
 *
 * Chạy: php artisan db:seed --class=TemplateImageSeeder
 *
 * Idempotent: skip nếu template đã có thumbnail.
 */
class TemplateImageSeeder extends Seeder
{
    /**
     * Spec từng template: layout + palette + style.
     */
    private array $specs = [
        // ── Công nghệ ──
        'tech-pro'           => ['layout' => 'classic', 'palette' => 'indigo',  'name' => 'NGUYỄN VĂN AN',     'role' => 'Software Engineer'],
        'developer-dark'     => ['layout' => 'modern',  'palette' => 'slate',   'name' => 'TRẦN MINH QUÂN',   'role' => 'Full-stack Developer'],
        'code-minimal'       => ['layout' => 'minimal', 'palette' => 'emerald', 'name' => 'LÊ HOÀNG NAM',     'role' => 'Backend Developer'],

        // ── Kinh doanh ──
        'business-executive' => ['layout' => 'classic', 'palette' => 'teal',    'name' => 'PHẠM THỊ LAN',     'role' => 'Business Manager'],
        'sales-star'         => ['layout' => 'modern',  'palette' => 'amber',   'name' => 'ĐỖ MINH TÚ',       'role' => 'Sales Executive'],

        // ── Sáng tạo ──
        'creative-pulse'     => ['layout' => 'minimal', 'palette' => 'rose',    'name' => 'NGUYỄN THỊ MAI',   'role' => 'UI/UX Designer'],
        'design-portfolio'   => ['layout' => 'modern',  'palette' => 'violet',  'name' => 'BÙI QUANG VINH',   'role' => 'Graphic Designer'],

        // ── Hành chính ──
        'admin-clean'        => ['layout' => 'classic', 'palette' => 'sky',     'name' => 'HOÀNG THỊ HƯƠNG',  'role' => 'HR Specialist'],
        'hr-manager'         => ['layout' => 'modern',  'palette' => 'cyan',    'name' => 'LÊ THỊ HỒNG',      'role' => 'HR Manager'],

        // ── Chuyên nghiệp ──
        'professional-classic' => ['layout' => 'classic', 'palette' => 'slate', 'name' => 'NGUYỄN MINH ĐỨC',  'role' => 'Project Manager'],
        'modern-standard'    => ['layout' => 'minimal', 'palette' => 'indigo',  'name' => 'TRẦN QUỐC BẢO',   'role' => 'Marketing Lead'],
        'elite-executive'    => ['layout' => 'modern',  'palette' => 'amber',   'name' => 'PHẠM ĐÌNH THẮNG', 'role' => 'Chief Executive'],
    ];

    public function run(): void
    {
        Storage::disk('public')->makeDirectory('templates');

        $renderer = new CvThumbnailRenderer();
        $success = 0;
        $skipped = 0;
        $failed  = 0;

        foreach (Template::with('category')->get() as $tpl) {
            if ($tpl->thumbnail) {
                $skipped++;
                continue;
            }

            $spec = $this->specs[$tpl->slug] ?? [
                'layout'  => 'classic',
                'palette' => 'indigo',
                'name'    => Str::upper($tpl->name),
                'role'    => 'Professional',
            ];

            try {
                $png  = $renderer->toPng($renderer->render($tpl->slug, $spec));
                $path = 'templates/' . Str::slug($tpl->slug) . '.png';
                Storage::disk('public')->put($path, $png);
                $tpl->thumbnail = $path;
                $tpl->save();

                $success++;
                $this->command->info("✓ {$tpl->name} → {$path}");
            } catch (\Throwable $e) {
                $failed++;
                $this->command->error("✗ {$tpl->name}: {$e->getMessage()}");
            }
        }

        $this->command->newLine();
        $this->command->info("Done. success={$success}, skipped={$skipped}, failed={$failed}");
    }
}
