<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Plan;
use App\Models\BlogCategory;
use App\Models\Faq;

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

        // ── Template categories & templates ──────────────────────────────────────
        $this->call(TemplateSeeder::class);

        // ── Job Posts ──────────────────────────────────────────────────────────
        $this->call(JobPostSeeder::class);

        // ── Blog categories ────────────────────────────────────────────────
        $blogCats = [
            ['name' => 'Kinh nghiệm viết CV',  'slug' => 'viet-cv',    'color' => 'indigo', 'description' => 'Mẹo viết CV ấn tượng'],
            ['name' => 'Tìm việc làm',         'slug' => 'tim-viec',   'color' => 'emerald', 'description' => 'Kinh nghiệm tìm việc hiệu quả'],
            ['name' => 'Phỏng vấn',            'slug' => 'phong-van',  'color' => 'amber', 'description' => 'Chuẩn bị phỏng vấn thành công'],
            ['name' => 'Phát triển sự nghiệp',  'slug' => 'career',     'color' => 'sky', 'description' => 'Lộ trình thăng tiến sự nghiệp'],
        ];

        foreach ($blogCats as $bc) {
            BlogCategory::updateOrCreate(['slug' => $bc['slug']], $bc);
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
            Faq::firstOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
