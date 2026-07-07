<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Công nghệ',        'slug' => 'cong-nghe',       'icon' => 'code',        'color' => 'indigo', 'description' => 'Template cho lập trình viên, kỹ sư phần mềm'],
            ['name' => 'Kinh doanh',        'slug' => 'kinh-doanh',      'icon' => 'briefcase',   'color' => 'emerald', 'description' => 'Template cho marketing, bán hàng, quản lý'],
            ['name' => 'Sáng tạo',          'slug' => 'sang-tao',        'icon' => 'brush',       'color' => 'rose',    'description' => 'Template cho designer, artist, sáng tạo nội dung'],
            ['name' => 'Hành chính',        'slug' => 'hanh-chinh',      'icon' => 'office',      'color' => 'sky',     'description' => 'Template cho nhân sự, kế toán, hành chính'],
            ['name' => 'Chuyên nghiệp',      'slug' => 'chuyen-nghiep',   'icon' => 'badge',       'color' => 'slate',   'description' => 'Template đa năng, phù hợp mọi ngành nghề'],
        ];

        foreach ($categories as $cat) {
            TemplateCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $catMap = TemplateCategory::pluck('id', 'slug');

        $templates = [
            // ── Công nghệ ──────────────────────────────────
            [
                'name'   => 'Tech Pro', 'slug' => 'tech-pro',
                'category' => 'cong-nghe', 'is_premium' => false,
                'theme_color' => '#4F46E5', 'color' => 'indigo',
                'blade_view' => 'cv-templates.classic-blue',
                'usage' => 3421,
            ],
            [
                'name'   => 'Developer Dark', 'slug' => 'developer-dark',
                'category' => 'cong-nghe', 'is_premium' => false,
                'theme_color' => '#1e293b', 'color' => 'slate',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 2890,
            ],
            [
                'name'   => 'Code Minimal', 'slug' => 'code-minimal',
                'category' => 'cong-nghe', 'is_premium' => true,
                'theme_color' => '#10b981', 'color' => 'emerald',
                'blade_view' => 'cv-templates.minimal-white',
                'usage' => 1567,
            ],
            // ── Kinh doanh ─────────────────────────────────
            [
                'name'   => 'Business Executive', 'slug' => 'business-executive',
                'category' => 'kinh-doanh', 'is_premium' => false,
                'theme_color' => '#0f766e', 'color' => 'teal',
                'blade_view' => 'cv-templates.classic-blue',
                'usage' => 2103,
            ],
            [
                'name'   => 'Sales Star', 'slug' => 'sales-star',
                'category' => 'kinh-doanh', 'is_premium' => true,
                'theme_color' => '#f59e0b', 'color' => 'amber',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 1342,
            ],
            // ── Sáng tạo ───────────────────────────────────
            [
                'name'   => 'Creative Pulse', 'slug' => 'creative-pulse',
                'category' => 'sang-tao', 'is_premium' => false,
                'theme_color' => '#e11d48', 'color' => 'rose',
                'blade_view' => 'cv-templates.minimal-white',
                'usage' => 987,
            ],
            [
                'name'   => 'Design Portfolio', 'slug' => 'design-portfolio',
                'category' => 'sang-tao', 'is_premium' => true,
                'theme_color' => '#7c3aed', 'color' => 'violet',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 876,
            ],
            // ── Hành chính ─────────────────────────────────
            [
                'name'   => 'Admin Clean', 'slug' => 'admin-clean',
                'category' => 'hanh-chinh', 'is_premium' => false,
                'theme_color' => '#0369a1', 'color' => 'sky',
                'blade_view' => 'cv-templates.classic-blue',
                'usage' => 654,
            ],
            [
                'name'   => 'HR Manager', 'slug' => 'hr-manager',
                'category' => 'hanh-chinh', 'is_premium' => true,
                'theme_color' => '#0891b2', 'color' => 'teal',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 543,
            ],
            // ── Chuyên nghiệp ───────────────────────────────
            [
                'name'   => 'Professional Classic', 'slug' => 'professional-classic',
                'category' => 'chuyen-nghiep', 'is_premium' => false,
                'theme_color' => '#1f2937', 'color' => 'slate',
                'blade_view' => 'cv-templates.classic-blue',
                'usage' => 4532,
            ],
            [
                'name'   => 'Modern Standard', 'slug' => 'modern-standard',
                'category' => 'chuyen-nghiep', 'is_premium' => false,
                'theme_color' => '#4338ca', 'color' => 'indigo',
                'blade_view' => 'cv-templates.minimal-white',
                'usage' => 3210,
            ],
            [
                'name'   => 'Elite Executive', 'slug' => 'elite-executive',
                'category' => 'chuyen-nghiep', 'is_premium' => true,
                'theme_color' => '#ca8a04', 'color' => 'amber',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 2109,
            ],
        ];

        foreach ($templates as $t) {
            Template::updateOrCreate(
                ['slug' => $t['slug']],
                [
                    'category_id'   => $catMap[$t['category']] ?? null,
                    'name'          => $t['name'],
                    'blade_view'    => $t['blade_view'],
                    'theme_color'   => $t['theme_color'],
                    'color'         => $t['color'],
                    'is_premium'    => $t['is_premium'],
                    'is_active'     => true,
                    'usage_count'   => $t['usage'],
                    'thumbnail'     => null,
                ]
            );
        }
    }
}
