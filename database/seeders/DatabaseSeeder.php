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

        // ── Template categories ────────────────────────────────────────────
        $cats = [
            ['name' => 'Chuyên nghiệp', 'slug' => 'professional'],
            ['name' => 'Sáng tạo',      'slug' => 'creative'],
            ['name' => 'Đơn giản',      'slug' => 'simple'],
            ['name' => 'Hiện đại',      'slug' => 'modern'],
            ['name' => 'Kỹ thuật',      'slug' => 'technical'],
        ];

        foreach ($cats as $cat) {
            TemplateCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $professional = TemplateCategory::where('slug', 'professional')->first();
        $creative     = TemplateCategory::where('slug', 'creative')->first();
        $simple       = TemplateCategory::where('slug', 'simple')->first();
        $modern       = TemplateCategory::where('slug', 'modern')->first();
        $technical    = TemplateCategory::where('slug', 'technical')->first();

        // ── Templates ──────────────────────────────────────────────────────
        $templates = [
            [
                'name'        => 'Classic Blue',
                'slug'        => 'classic-blue',
                'blade_view'  => 'cv-templates.classic-blue',
                'category_id' => $professional->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 1250,
            ],
            [
                'name'        => 'Modern Dark',
                'slug'        => 'modern-dark',
                'blade_view'  => 'cv-templates.modern-dark',
                'category_id' => $modern->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 980,
            ],
            [
                'name'        => 'Minimal White',
                'slug'        => 'minimal-white',
                'blade_view'  => 'cv-templates.minimal-white',
                'category_id' => $simple->id,
                'is_premium'  => false,
                'is_active'   => true,
                'usage_count' => 2100,
            ],
            [
                'name'        => 'Creative Designer',
                'slug'        => 'creative-designer',
                'blade_view'  => 'cv-templates.creative-designer',
                'category_id' => $creative->id,
                'is_premium'  => true,
                'is_active'   => true,
                'usage_count' => 560,
            ],
            [
                'name'        => 'Tech Engineer',
                'slug'        => 'tech-engineer',
                'blade_view'  => 'cv-templates.tech-engineer',
                'category_id' => $technical->id,
                'is_premium'  => true,
                'is_active'   => true,
                'usage_count' => 720,
            ],
            [
                'name'        => 'Executive Pro',
                'slug'        => 'executive-pro',
                'blade_view'  => 'cv-templates.executive-pro',
                'category_id' => $professional->id,
                'is_premium'  => true,
                'is_active'   => true,
                'usage_count' => 430,
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
