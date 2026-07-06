<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first() ?? User::factory()->create([
            'name'  => 'Nguyễn Minh Anh',
            'email' => 'author@cvactive.vn',
        ]);

        $categories = [
            ['name' => 'Viết CV',          'slug' => 'viet-cv',           'color' => 'indigo',  'description' => 'Mẹo viết CV ấn tượng'],
            ['name' => 'Phỏng vấn',        'slug' => 'phong-van',         'color' => 'rose',    'description' => 'Bí kíp phỏng vấn thành công'],
            ['name' => 'Đàm phán lương',   'slug' => 'dam-phan-luong',    'color' => 'amber',   'description' => 'Thương lượng lương khéo léo'],
            ['name' => 'Tìm việc',         'slug' => 'tim-viec',          'color' => 'emerald', 'description' => 'Chiến lược tìm việc hiệu quả'],
            ['name' => 'Phát triển sự nghiệp', 'slug' => 'phat-trien-nghe-nghiep', 'color' => 'sky', 'description' => 'Xây dựng con đường sự nghiệp'],
            ['name' => 'Kỹ năng mềm',      'slug' => 'ky-nang-mem',       'color' => 'violet',  'description' => 'Phát triển kỹ năng cá nhân'],
        ];

        foreach ($categories as $cat) {
            BlogCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $catMap = BlogCategory::pluck('id', 'slug');

        $posts = [
            [
                'title'   => '10 mẹo viết CV giúp bạn được gọi phỏng vấn ngay',
                'category'=> 'viet-cv',
                'excerpt' => 'Bí quyết để CV của bạn nổi bật giữa hàng trăm hồ sơ ứng tuyển và gây ấn tượng với nhà tuyển dụng.',
                'featured'=> true,
                'views'   => 4521,
                'image'   => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'Cách trả lời câu hỏi "Điểm yếu của bạn là gì?"',
                'category'=> 'phong-van',
                'excerpt' => 'Câu hỏi kinh điển nhưng không kém phần khó nhằn. Học cách biến điểm yếu thành cơ hội.',
                'featured'=> true,
                'views'   => 3892,
                'image'   => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'Đàm phán lương: Khi nào nên nhận, khi nào nên từ chối?',
                'category'=> 'dam-phan-luong',
                'excerpt' => 'Hướng dẫn chi tiết cách thương lượng mức lương xứng đáng mà không đánh mất cơ hội.',
                'featured'=> false,
                'views'   => 2743,
                'image'   => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => '5 chiến lược tìm việc hiệu quả trong năm 2026',
                'category'=> 'tim-viec',
                'excerpt' => 'Thị trường lao động thay đổi từng ngày. Cập nhật những cách tìm việc mới nhất.',
                'featured'=> false,
                'views'   => 2105,
                'image'   => 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'Lộ trình thăng tiến cho người đi làm 3-5 năm',
                'category'=> 'phat-trien-nghe-nghiep',
                'excerpt' => 'Xây dựng kế hoạch phát triển sự nghiệp rõ ràng để đạt được vị trí mong muốn.',
                'featured'=> false,
                'views'   => 1856,
                'image'   => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => '7 kỹ năng mềm nhà tuyển dụng tìm kiếm nhiều nhất',
                'category'=> 'ky-nang-mem',
                'excerpt' => 'Không chỉ chuyên môn, các kỹ năng mềm này sẽ giúp bạn tỏa sáng trong mắt nhà tuyển dụng.',
                'featured'=> false,
                'views'   => 1623,
                'image'   => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'ATS là gì? Cách viết CV "vượt qua" hệ thống lọc tự động',
                'category'=> 'viet-cv',
                'excerpt' => 'Tìm hiểu cách hoạt động của ATS và làm sao để CV của bạn được hệ thống chấp nhận.',
                'featured'=> false,
                'views'   => 1432,
                'image'   => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'Câu hỏi phỏng vấn tiếng Anh thường gặp và cách trả lời',
                'category'=> 'phong-van',
                'excerpt' => 'Tổng hợp 20 câu hỏi phỏng vấn tiếng Anh phổ biến nhất cùng gợi ý trả lời.',
                'featured'=> false,
                'views'   => 1289,
                'image'   => 'https://images.unsplash.com/photo-1543269664-7eef42226a21?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'Phỏng vấn online: 8 điều cần chuẩn bị kỹ lưỡng',
                'category'=> 'phong-van',
                'excerpt' => 'Hướng dẫn toàn tập giúp bạn tỏa sáng trong các buổi phỏng vấn qua Zoom, Teams.',
                'featured'=> false,
                'views'   => 1156,
                'image'   => 'https://images.unsplash.com/photo-1588196749597-9ff075ee6b5b?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'Cách viết thư xin việc (Cover Letter) gây ấn tượng',
                'category'=> 'viet-cv',
                'excerpt' => 'Cover Letter là yếu tố tạo sự khác biệt. Học cách viết một bức thư xin việc chuyên nghiệp.',
                'featured'=> false,
                'views'   => 987,
                'image'   => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'LinkedIn profile: Hồ sơ vàng cho người tìm việc',
                'category'=> 'tim-viec',
                'excerpt' => 'Tối ưu hóa hồ sơ LinkedIn để tiếp cận nhà tuyển dụng và headhunter hàng đầu.',
                'featured'=> false,
                'views'   => 856,
                'image'   => 'https://images.unsplash.com/photo-1633332755192-727a05c4013d?w=1200&q=80&auto=format&fit=crop',
            ],
            [
                'title'   => 'Trắc nghiệm tính cách MBTI có thật sự hữu ích?',
                'category'=> 'phat-trien-nghe-nghiep',
                'excerpt' => 'Phân tích ưu nhược điểm của MBTI trong việc lựa chọn nghề nghiệp phù hợp.',
                'featured'=> false,
                'views'   => 743,
                'image'   => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=1200&q=80&auto=format&fit=crop',
            ],
        ];

        foreach ($posts as $i => $p) {
            $title = $p['title'];
            $slug  = Str::slug($title);
            $imagePath = null;

            if (! empty($p['image'])) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(15)->get($p['image']);
                    if ($response->successful()) {
                        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('blog');
                        $ext = strtolower(pathinfo(parse_url($p['image'], PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                            $ext = 'jpg';
                        }
                        $filename = $slug . '-' . substr(md5($slug), 0, 6) . '.' . $ext;
                        \Illuminate\Support\Facades\Storage::disk('public')->put('blog/' . $filename, $response->body());
                        $imagePath = 'blog/' . $filename;
                    }
                } catch (\Throwable $e) {
                    // ignore – leave null
                }
            }

            BlogPost::updateOrCreate(
                ['slug' => $slug],
                [
                    'author_id'      => $author->id,
                    'category_id'    => $catMap[$p['category']] ?? null,
                    'title'          => $title,
                    'excerpt'        => $p['excerpt'],
                    'content'        => $this->longContent($title, $p['excerpt']),
                    'featured_image' => $imagePath,
                    'status'         => 'published',
                    'is_featured'    => $p['featured'],
                    'views_count'    => $p['views'],
                    'reading_time'   => rand(4, 9),
                    'published_at'   => Carbon::now()->subDays($i * 2),
                ]
            );
        }
    }

    private function longContent(string $title, string $excerpt): string
    {
        return "{$excerpt}\n\nTrong bài viết này, chúng ta sẽ cùng nhau tìm hiểu chi tiết về \"{$title}\" - một trong những chủ đề được quan tâm nhiều nhất hiện nay.\n\n1. Hiểu rõ vấn đề\n\nNhiều người đi làm thường mắc phải những sai lầm phổ biến khi chưa nắm rõ bản chất của vấn đề. Điều này dẫn đến việc mất thời gian và công sức mà hiệu quả không cao.\n\n2. Các bước thực hiện\n\n- Bước 1: Xác định mục tiêu rõ ràng\n- Bước 2: Chuẩn bị kỹ lưỡng tài liệu cần thiết\n- Bước 3: Thực hành thường xuyên\n- Bước 4: Đánh giá và điều chỉnh\n\n3. Lời khuyên từ chuyên gia\n\nTheo kinh nghiệm của các chuyên gia trong ngành, việc áp dụng phương pháp đúng đắn sẽ giúp bạn tiết kiệm được rất nhiều thời gian và đạt được kết quả tốt hơn.\n\n4. Kết luận\n\nHãy bắt đầu áp dụng những kiến thức này ngay hôm nay để thấy được sự khác biệt trong công việc của bạn. Đừng quên chia sẻ bài viết nếu bạn thấy hữu ích!";
    }
}