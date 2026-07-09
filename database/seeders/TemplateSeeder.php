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
            // Tech Pro
            [
                'name'   => 'Tech Pro', 'slug' => 'tech-pro',
                'category' => 'cong-nghe', 'is_premium' => false,
                'theme_color' => '#4F46E5', 'font_family' => 'Inter', 'color' => 'indigo',
                'blade_view' => 'cv-templates.classic-blue',
                'usage' => 3421,
                'image' => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=900&q=80&auto=format&fit=crop',
            ],
            // Developer Dark
            [
                'name'   => 'Developer Dark', 'slug' => 'developer-dark',
                'category' => 'cong-nghe', 'is_premium' => false,
                'theme_color' => '#1e293b', 'font_family' => 'JetBrains Mono', 'color' => 'slate',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 2890,
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=900&q=80&auto=format&fit=crop',
            ],
            // Code Minimal
            [
                'name'   => 'Code Minimal', 'slug' => 'code-minimal',
                'category' => 'cong-nghe', 'is_premium' => true,
                'theme_color' => '#10b981', 'font_family' => 'Fira Sans', 'color' => 'emerald',
                'blade_view' => 'cv-templates.minimal-white',
                'usage' => 1567,
                'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=900&q=80&auto=format&fit=crop',
            ],
            // Business Executive
            [
                'name'   => 'Business Executive', 'slug' => 'business-executive',
                'category' => 'kinh-doanh', 'is_premium' => false,
                'theme_color' => '#0f766e', 'font_family' => 'Montserrat', 'color' => 'teal',
                'blade_view' => 'cv-templates.classic-blue',
                'usage' => 2103,
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=900&q=80&auto=format&fit=crop',
            ],
            // Sales Star
            [
                'name'   => 'Sales Star', 'slug' => 'sales-star',
                'category' => 'kinh-doanh', 'is_premium' => true,
                'theme_color' => '#f59e0b', 'font_family' => 'Poppins', 'color' => 'amber',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 1342,
                'image' => 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=900&q=80&auto=format&fit=crop',
            ],
            // Creative Pulse
            [
                'name'   => 'Creative Pulse', 'slug' => 'creative-pulse',
                'category' => 'sang-tao', 'is_premium' => false,
                'theme_color' => '#e11d48', 'font_family' => 'Poppins', 'color' => 'rose',
                'blade_view' => 'cv-templates.minimal-white',
                'usage' => 987,
                'image' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=900&q=80&auto=format&fit=crop',
            ],
            // Design Portfolio
            [
                'name'   => 'Design Portfolio', 'slug' => 'design-portfolio',
                'category' => 'sang-tao', 'is_premium' => true,
                'theme_color' => '#7c3aed', 'font_family' => 'Playfair Display', 'color' => 'violet',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 876,
                'image' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=900&q=80&auto=format&fit=crop',
            ],
            // Admin Clean
            [
                'name'   => 'Admin Clean', 'slug' => 'admin-clean',
                'category' => 'hanh-chinh', 'is_premium' => false,
                'theme_color' => '#0369a1', 'font_family' => 'Open Sans', 'color' => 'sky',
                'blade_view' => 'cv-templates.classic-blue',
                'usage' => 654,
                'image' => 'https://images.unsplash.com/photo-1499914485622-a88fac536970?w=900&q=80&auto=format&fit=crop',
            ],
            // HR Manager
            [
                'name'   => 'HR Manager', 'slug' => 'hr-manager',
                'category' => 'hanh-chinh', 'is_premium' => true,
                'theme_color' => '#0891b2', 'font_family' => 'Lato', 'color' => 'teal',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 543,
                'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=900&q=80&auto=format&fit=crop',
            ],
            // Professional Classic
            [
                'name'   => 'Professional Classic', 'slug' => 'professional-classic',
                'category' => 'chuyen-nghiep', 'is_premium' => false,
                'theme_color' => '#1f2937', 'font_family' => 'Merriweather', 'color' => 'slate',
                'blade_view' => 'cv-templates.classic-blue',
                'usage' => 4532,
                'image' => 'https://images.unsplash.com/photo-1589994965851-a8f479c573a9?w=900&q=80&auto=format&fit=crop',
            ],
            // Modern Standard
            [
                'name'   => 'Modern Standard', 'slug' => 'modern-standard',
                'category' => 'chuyen-nghiep', 'is_premium' => false,
                'theme_color' => '#4338ca', 'font_family' => 'Nunito', 'color' => 'indigo',
                'blade_view' => 'cv-templates.minimal-white',
                'usage' => 3210,
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=900&q=80&auto=format&fit=crop',
            ],
            // Elite Executive
            [
                'name'   => 'Elite Executive', 'slug' => 'elite-executive',
                'category' => 'chuyen-nghiep', 'is_premium' => true,
                'theme_color' => '#ca8a04', 'font_family' => 'Playfair Display', 'color' => 'amber',
                'blade_view' => 'cv-templates.modern-dark',
                'usage' => 2109,
                'image' => 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=900&q=80&auto=format&fit=crop',
            ],
        ];

        foreach ($templates as $t) {
            $imagePath = null;

            if (! empty($t['image'])) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(15)->get($t['image']);
                    if ($response->successful()) {
                        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('templates');
                        $ext = strtolower(pathinfo(parse_url($t['image'], PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                            $ext = 'jpg';
                        }
                        $filename = $t['slug'] . '-' . substr(md5($t['slug']), 0, 6) . '.' . $ext;
                        \Illuminate\Support\Facades\Storage::disk('public')->put('templates/' . $filename, $response->body());
                        $imagePath = 'templates/' . $filename;
                    }
                } catch (\Throwable $e) {
                    // ignore – leave null
                }
            }

            Template::updateOrCreate(
                ['slug' => $t['slug']],
                [
                    'category_id'   => $catMap[$t['category']] ?? null,
                    'name'          => $t['name'],
                    'blade_view'    => $t['blade_view'],
                    'theme_color'   => $t['theme_color'],
                    'font_family'   => $t['font_family'] ?? null,
                    'is_premium'    => $t['is_premium'],
                    'is_active'     => true,
                    'usage_count'   => $t['usage'],
                    'thumbnail'     => $imagePath,
                ]
            );
        }
    }
}
