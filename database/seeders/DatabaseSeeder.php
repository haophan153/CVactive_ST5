<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Plan;
use App\Models\TemplateCategory;
use App\Models\Template;
use App\Models\Faq;
use App\Models\BlogCategory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Plans ─────────────────────────────────────────────────────────
        $free = Plan::firstOrCreate(['slug' => 'free'], [
            'name'     => 'Free',
            'price'    => 0,
            'cv_limit' => 2,
            'features' => [
                '2 CV miễn phí',
                'Mẫu CV cơ bản',
                'Xuất PDF',
                'Chia sẻ link online',
            ],
            'is_active' => true,
        ]);

        $pro = Plan::firstOrCreate(['slug' => 'pro'], [
            'name'     => 'Pro',
            'price'    => 99000,
            'cv_limit' => 20,
            'features' => [
                'Không giới hạn CV',
                'Tất cả mẫu CV Premium',
                'Xuất PDF & PNG chất lượng cao',
                'Chia sẻ link online',
                'Tùy chỉnh màu sắc & font chữ',
                'Ưu tiên hỗ trợ',
            ],
            'is_active' => true,
        ]);

        Plan::firstOrCreate(['slug' => 'business'], [
            'name'     => 'Business',
            'price'    => 199000,
            'cv_limit' => 999,
            'features' => [
                'Không giới hạn CV',
                'Tất cả tính năng Pro',
                'Tạo CV cho nhóm / công ty',
                'Quản lý thành viên nhóm',
                'Hỗ trợ ưu tiên 24/7',
            ],
            'is_active' => true,
        ]);

        // ── Admin user ─────────────────────────────────────────────────────
        User::firstOrCreate(['email' => 'admin@cvactive.vn'], [
            'name'     => 'Admin',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'plan_id'  => $pro->id,
        ]);

        // ── HR user ──────────────────────────────────────────────────────────
        User::firstOrCreate(['email' => 'hr@cvactive.vn'], [
            'name'     => 'HR Manager',
            'password' => Hash::make('password'),
            'role'     => 'hr',
            'plan_id'  => $pro->id,
        ]);

        // ── Template categories ────────────────────────────────────────────
        $cats = [
            ['name' => 'Chuyên nghiệp', 'slug' => 'professional'],
            ['name' => 'Sáng tạo',      'slug' => 'creative'],
            ['name' => 'Đơn giản',      'slug' => 'simple'],
            ['name' => 'Hiện đại',      'slug' => 'modern'],
            ['name' => 'Kỹ thuật',      'slug' => 'technical'],
            ['name' => 'Màu sắc',       'slug' => 'colorful'],
            ['name' => 'Timeline',       'slug' => 'timeline'],
            ['name' => 'Không ảnh',      'slug' => 'no-photo'],
        ];

        foreach ($cats as $cat) {
            TemplateCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $professional = TemplateCategory::where('slug', 'professional')->first();
        $creative     = TemplateCategory::where('slug', 'creative')->first();
        $simple       = TemplateCategory::where('slug', 'simple')->first();
        $modern       = TemplateCategory::where('slug', 'modern')->first();
        $technical    = TemplateCategory::where('slug', 'technical')->first();
        $colorful     = TemplateCategory::where('slug', 'colorful')->first();
        $timeline     = TemplateCategory::where('slug', 'timeline')->first();
        $noPhoto      = TemplateCategory::where('slug', 'no-photo')->first();

        // ── Templates ──────────────────────────────────────────────────────
        $templates = [
            // Original templates
            [
                'name'        => 'Classic Blue',
                'slug'        => 'classic-blue',
                'blade_view'  => 'cv-templates.classic-blue',
                'thumbnail'   => 'https://placehold.co/420x594/2563EB/FFFFFF?text=Classic+Blue',
                'category_id' => $professional->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 1250,
            ],
            [
                'name'        => 'Modern Dark',
                'slug'        => 'modern-dark',
                'blade_view'  => 'cv-templates.modern-dark',
                'thumbnail'   => 'https://placehold.co/420x594/1F2937/FFFFFF?text=Modern+Dark',
                'category_id' => $modern->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 980,
            ],
            [
                'name'        => 'Minimal White',
                'slug'        => 'minimal-white',
                'blade_view'  => 'cv-templates.minimal-white',
                'thumbnail'   => 'https://placehold.co/420x594/F3F4F6/374151?text=Minimal+White',
                'category_id' => $simple->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 2100,
            ],
            [
                'name'        => 'Creative Designer',
                'slug'        => 'creative-designer',
                'blade_view'  => 'cv-templates.creative-designer',
                'thumbnail'   => 'https://placehold.co/420x594/7C3AED/FFFFFF?text=Creative+Designer',
                'category_id' => $creative->id,
                'is_premium'  => true,
                'is_active'   => true,
                'usage_count' => 560,
            ],
            [
                'name'        => 'Tech Engineer',
                'slug'        => 'tech-engineer',
                'blade_view'  => 'cv-templates.tech-engineer',
                'thumbnail'   => 'https://placehold.co/420x594/059669/FFFFFF?text=Tech+Engineer',
                'category_id' => $technical->id,
                'is_premium'  => true,
                'is_active'   => true,
                'usage_count' => 720,
            ],
            [
                'name'        => 'Executive Pro',
                'slug'        => 'executive-pro',
                'blade_view'  => 'cv-templates.executive-pro',
                'thumbnail'   => 'https://placehold.co/420x594/000000/FFFFFF?text=Executive+Pro',
                'category_id' => $professional->id,
                'is_premium'  => true,
                'is_active'   => true,
                'usage_count' => 430,
            ],
            // New VietCV-style templates
            [
                'name'        => 'Elegant',
                'slug'        => 'elegant',
                'blade_view'  => 'cv-templates.elegant',
                'thumbnail'   => 'https://placehold.co/420x594/4B5563/FFFFFF?text=Elegant',
                'category_id' => $professional->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 27356,
            ],
            [
                'name'        => 'Abstraction',
                'slug'        => 'abstraction',
                'blade_view'  => 'cv-templates.abstraction',
                'thumbnail'   => 'https://placehold.co/420x594/8B5CF6/FFFFFF?text=Abstraction',
                'category_id' => $colorful->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 18500,
            ],
            [
                'name'        => 'Circum',
                'slug'        => 'circum',
                'blade_view'  => 'cv-templates.circum',
                'thumbnail'   => 'https://placehold.co/420x594/06B6D4/FFFFFF?text=Circum',
                'category_id' => $modern->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 8906,
            ],
            [
                'name'        => 'Deluxe',
                'slug'        => 'deluxe',
                'blade_view'  => 'cv-templates.deluxe',
                'thumbnail'   => 'https://placehold.co/420x594/92400E/FFFFFF?text=Deluxe',
                'category_id' => $professional->id,
                'is_premium'  => true,
                'is_active'   => true,
                'usage_count' => 4443,
            ],
            [
                'name'        => 'Minimalism',
                'slug'        => 'minimalism',
                'blade_view'  => 'cv-templates.minimalism',
                'thumbnail'   => 'https://placehold.co/420x594/9CA3AF/FFFFFF?text=Minimalism',
                'category_id' => $noPhoto->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 1752,
            ],
            [
                'name'        => 'Cloudy',
                'slug'        => 'cloudy',
                'blade_view'  => 'cv-templates.cloudy',
                'thumbnail'   => 'https://placehold.co/420x594/60A5FA/FFFFFF?text=Cloudy',
                'category_id' => $creative->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 10729,
            ],
            [
                'name'        => 'Cerulean Blue',
                'slug'        => 'cerulean-blue',
                'blade_view'  => 'cv-templates.cerulean-blue',
                'thumbnail'   => 'https://placehold.co/420x594/0EA5E9/FFFFFF?text=Cerulean+Blue',
                'category_id' => $professional->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 3566,
            ],
            [
                'name'        => 'Pastel',
                'slug'        => 'pastel',
                'blade_view'  => 'cv-templates.pastel',
                'thumbnail'   => 'https://placehold.co/420x594/F9A8D4/FFFFFF?text=Pastel',
                'category_id' => $colorful->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 8049,
            ],
            [
                'name'        => 'Graphite',
                'slug'        => 'graphite',
                'blade_view'  => 'cv-templates.graphite',
                'thumbnail'   => 'https://placehold.co/420x594/374151/FFFFFF?text=Graphite',
                'category_id' => $simple->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 4972,
            ],
            [
                'name'        => 'Bordeaux',
                'slug'        => 'bordeaux',
                'blade_view'  => 'cv-templates.bordeaux',
                'thumbnail'   => 'https://placehold.co/420x594/7F1D1D/FFFFFF?text=Bordeaux',
                'category_id' => $professional->id,
                'is_premium'  => true,
                'is_active'   => true,
                'usage_count' => 4663,
            ],
        ];

        foreach ($templates as $tpl) {
            Template::updateOrCreate(['slug' => $tpl['slug']], $tpl);
        }

        // ── Blog categories ────────────────────────────────────────────────
        $blogCats = [
            ['name' => 'Kinh nghiệm viết CV',  'slug' => 'cv-tips'],
            ['name' => 'Tìm việc làm',         'slug' => 'job-hunting'],
            ['name' => 'Phỏng vấn',            'slug' => 'interview'],
            ['name' => 'Phát triển sự nghiệp', 'slug' => 'career'],
        ];

        foreach ($blogCats as $bc) {
            BlogCategory::firstOrCreate(['slug' => $bc['slug']], $bc);
        }

        // ── FAQs ───────────────────────────────────────────────────────────
        $faqs = [
            [
                'question'   => 'CVactive có miễn phí không?',
                'answer'     => 'Có! Bạn có thể dùng CVactive miễn phí để tạo tối đa 2 CV với các mẫu cơ bản. Nâng cấp lên Pro để mở khóa tất cả tính năng.',
                'sort_order' => 1,
                'is_active'  => true,
            ],
            [
                'question'   => 'Tôi có thể xuất CV ra file PDF không?',
                'answer'     => 'Có, tất cả tài khoản đều có thể xuất CV dưới dạng PDF chất lượng cao. Tài khoản Pro còn hỗ trợ xuất PNG và chất lượng in ấn cao hơn.',
                'sort_order' => 2,
                'is_active'  => true,
            ],
            [
                'question'   => 'Dữ liệu CV của tôi có an toàn không?',
                'answer'     => 'Dữ liệu của bạn được mã hóa và lưu trữ an toàn trên máy chủ. Chúng tôi không bao giờ chia sẻ thông tin cá nhân của bạn với bên thứ ba.',
                'sort_order' => 3,
                'is_active'  => true,
            ],
            [
                'question'   => 'Tôi có thể chia sẻ CV online không?',
                'answer'     => 'Có! Mỗi CV đều có thể tạo link chia sẻ riêng. Bạn có thể gửi link này cho nhà tuyển dụng thay vì đính kèm file.',
                'sort_order' => 4,
                'is_active'  => true,
            ],
            [
                'question'   => 'Có thể hủy gói Pro bất cứ lúc nào không?',
                'answer'     => 'Có, bạn có thể hủy gói Pro bất cứ lúc nào. Gói sẽ vẫn hoạt động cho đến hết chu kỳ thanh toán hiện tại.',
                'sort_order' => 5,
                'is_active'  => true,
            ],
            [
                'question'   => 'CVactive hỗ trợ ngôn ngữ nào?',
                'answer'     => 'Hiện tại CVactive hỗ trợ Tiếng Việt và Tiếng Anh. Bạn có thể viết nội dung CV bằng bất kỳ ngôn ngữ nào.',
                'sort_order' => 6,
                'is_active'  => true,
            ],
        ];

        foreach ($faqs as $faq) {
            \App\Models\Faq::firstOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
