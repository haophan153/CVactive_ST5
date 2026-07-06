<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Download ảnh minh hoạ cho từng blog post (1 ảnh/bài),
 * lưu vào storage/app/public/blog/ rồi cập nhật featured_image.
 *
 * Chạy: php artisan db:seed --class=BlogImageSeeder
 *
 * Idempotent: nếu post đã có featured_image thì bỏ qua.
 */
class BlogImageSeeder extends Seeder
{
    /**
     * Danh sách URL ảnh (Unsplash featured, chọn cụ thể theo chủ đề CV/career).
     * Mỗi bài viết map với 1 URL tương ứng.
     */
    private array $postImages = [
        '10-meo-viet-cv-giup-ban-duoc-goi-phong-van-ngay'
            => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=1200&q=80&auto=format&fit=crop',
        'cach-tra-loi-cau-hoi-diem-yeu-cua-ban-la-gi'
            => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=1200&q=80&auto=format&fit=crop',
        'dam-phan-luong-khi-nao-nen-nhan-khi-nao-nen-tu-choi'
            => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&q=80&auto=format&fit=crop',
        '5-chien-luoc-tim-viec-hieu-qua-trong-nam-2026'
            => 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=1200&q=80&auto=format&fit=crop',
        'lo-trinh-thang-tien-cho-nguoi-di-lam-3-5-nam'
            => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1200&q=80&auto=format&fit=crop',
        '7-ky-nang-mem-nha-tuyen-dung-tim-kiem-nhieu-nhat'
            => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1200&q=80&auto=format&fit=crop',
        'ats-la-gi-cach-viet-cv-vuot-qua-he-thong-loc-tu-dong'
            => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1200&q=80&auto=format&fit=crop',
        'cau-hoi-phong-van-tieng-anh-thuong-gap-va-cach-tra-loi'
            => 'https://images.unsplash.com/photo-1543269664-7eef42226a21?w=1200&q=80&auto=format&fit=crop',
        'phong-van-online-8-dieu-can-chuan-bi-ky-luong'
            => 'https://images.unsplash.com/photo-1588196749597-9ff075ee6b5b?w=1200&q=80&auto=format&fit=crop',
        'cach-viet-thu-xin-viec-cover-letter-gay-an-tuong'
            => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=1200&q=80&auto=format&fit=crop',
        'linkedin-profile-ho-so-vang-cho-nguoi-tim-viec'
            => 'https://images.unsplash.com/photo-1633332755192-727a05c4013d?w=1200&q=80&auto=format&fit=crop',
        'trac-nghiem-tinh-cach-mbti-co-that-su-huu-ich'
            => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=1200&q=80&auto=format&fit=crop',
    ];

    /**
     * Fallback chung theo category (nếu slug không khớp).
     */
    private array $categoryFallback = [
        'viet-cv'           => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?w=1200&q=80&auto=format&fit=crop',
        'phong-van'         => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=1200&q=80&auto=format&fit=crop',
        'dam-phan-luong'    => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&q=80&auto=format&fit=crop',
        'tim-viec'          => 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=1200&q=80&auto=format&fit=crop',
        'phat-trien-nghe-nghiep' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1200&q=80&auto=format&fit=crop',
        'ky-nang-mem'       => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1200&q=80&auto=format&fit=crop',
    ];

    public function run(): void
    {
        Storage::disk('public')->makeDirectory('blog');

        $posts = BlogPost::with('category')->get();
        $success = 0;
        $skipped = 0;
        $failed  = 0;

        foreach ($posts as $post) {
            if ($post->featured_image) {
                $skipped++;
                continue;
            }

            $url = $this->postImages[$post->slug]
                ?? ($this->categoryFallback[$post->category?->slug] ?? null);

            if (! $url) {
                $this->command->warn("⚠ Không tìm được ảnh cho: {$post->title}");
                $failed++;
                continue;
            }

            try {
                $response = Http::timeout(20)->get($url);
                if (! $response->successful()) {
                    throw new \RuntimeException("HTTP {$response->status()}");
                }

                $ext      = $this->extensionFromUrl($url);
                $filename = Str::slug($post->slug ?: $post->id) . '-' . substr(md5($post->id), 0, 6) . $ext;
                $path     = 'blog/' . $filename;

                Storage::disk('public')->put($path, $response->body());
                $post->featured_image = $path;
                $post->save();

                $success++;
                $this->command->info("✓ {$post->title} → {$path}");
            } catch (\Throwable $e) {
                $failed++;
                $this->command->error("✗ {$post->title}: {$e->getMessage()}");
            }
        }

        $this->command->newLine();
        $this->command->info("Done. success={$success}, skipped={$skipped}, failed={$failed}");
    }

    private function extensionFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) ? ".{$ext}" : '.jpg';
    }
}
