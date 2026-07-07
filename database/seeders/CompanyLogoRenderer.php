<?php

namespace Database\Seeders;

use GdImage;

/**
 * Render logo công ty dạng PNG dùng GD extension.
 *
 * Mỗi logo gồm:
 *   - Background tròn / vuông bo góc với gradient 2 màu
 *   - Chữ cái đầu của tên công ty (white, bold)
 *   - Vài công ty tech có shape phụ (square) để tạo cảm giác "logo thật"
 *
 * Output: 256x256 PNG, lưu disk public/logos/
 */
class CompanyLogoRenderer
{
    private int $size = 256;

    /**
     * Map slug → [bg1, bg2, textStyle]
     *   bg1, bg2: 2 màu gradient
     *   textStyle: 'plain' | 'square' | 'underline' | 'dot'
     */
    private array $themes = [
        // ── Big tech style: gradient + initials bold ──
        'hth'            => ['#4F46E5', '#7C3AED', 'plain'],          // HTH - Tech đa quốc gia
        'techvn-solutions' => ['#0EA5E9', '#0369A1', 'plain'],       // TechVN
        'cloudscale-asia'  => ['#06B6D4', '#0E7490', 'square'],      // Cloud infra
        'unityxh'        => ['#10B981', '#059669', 'plain'],          // Unity - Gaming
        'contentlab-agency' => ['#F59E0B', '#D97706', 'plain'],      // Content

        // ── Agency / Design style: vibrant ──
        'digital-agency-abc' => ['#EC4899', '#BE185D', 'dot'],       // Creative
        'brandup-vietnam'  => ['#8B5CF6', '#6D28D9', 'plain'],        // Branding
        'creative-studio-vn' => ['#EF4444', '#B91C1C', 'underline'], // Studio

        // ── Finance / Corporate: deep, trustworthy ──
        'tap-doan-tai-chinh-abc' => ['#1E3A8A', '#1E40AF', 'plain'],  // Finance
        'smart-sales-co'    => ['#0891B2', '#0E7490', 'plain'],        // B2B sales

        // ── HR / People style: warm ──
        'people-first-corp' => ['#F97316', '#EA580C', 'plain'],        // HR
        'startuphub-vn'     => ['#84CC16', '#4D7C0F', 'square'],      // Startup
    ];

    public function render(string $slug, string $companyName): GdImage
    {
        $theme = $this->themes[$slug] ?? ['#4F46E5', '#7C3AED', 'plain'];
        [$bg1, $bg2, $style] = $theme;

        $img = $this->newCanvas();
        $this->drawGradientCircle($img, $bg1, $bg2);
        $this->drawInitials($img, $companyName);

        return match ($style) {
            'square'   => $this->addSquareAccent($img),
            'dot'      => $this->addDotAccent($img),
            'underline'=> $this->addUnderlineAccent($img),
            default    => $img,
        };
    }

    public function toPng(GdImage $img): string
    {
        ob_start();
        imagepng($img, null, 9);
        $data = ob_get_clean();
        imagedestroy($img);
        return $data;
    }

    // ── Primitives ─────────────────────────────────────────────

    private function newCanvas(): GdImage
    {
        $img = imagecreatetruecolor($this->size, $this->size);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefilledrectangle($img, 0, 0, $this->size, $this->size, $transparent);
        return $img;
    }

    private function color(GdImage $img, string $hex): int
    {
        $h = ltrim($hex, '#');
        $r = hexdec(substr($h, 0, 2));
        $g = hexdec(substr($h, 2, 2));
        $b = hexdec(substr($h, 4, 2));
        return imagecolorallocate($img, (int)$r, (int)$g, (int)$b);
    }

    /**
     * Vẽ hình tròn lớn chiếm full canvas với gradient từ bg1 (trên-trái) → bg2 (dưới-phải).
     * Approx gradient bằng cách vẽ nhiều ellipse lồng nhau với màu interpolated.
     */
    private function drawGradientCircle(GdImage $img, string $c1, string $c2): void
    {
        $cx = $cy = $this->size / 2;
        $radius = $this->size / 2 - 4;

        // Interp 16 vòng tròn từ trắng hồng → bg cuối
        $steps = 30;
        [$r1, $g1, $b1] = $this->hex2rgb($c1);
        [$r2, $g2, $b2] = $this->hex2rgb($c2);

        for ($i = $steps; $i >= 1; $i--) {
            $t = $i / $steps;
            $r = (int)($r1 * $t + $r2 * (1 - $t));
            $g = (int)($g1 * $t + $g2 * (1 - $t));
            $b = (int)($b1 * $t + $b2 * (1 - $t));
            $col = imagecolorallocate($img, $r, $g, $b);

            // Filled ellipse từng lớp — tạo cảm giác gradient
            $diam = (int)($radius * 2 * $t);
            imagefilledellipse($img, (int)$cx, (int)$cy, $diam, $diam, $col);
        }

        // Border nhẹ sáng hơn
        $borderCol = imagecolorallocatealpha(
            $img,
            min(255, $r1 + 30),
            min(255, $g1 + 30),
            min(255, $b1 + 30),
            60
        );
        imageellipse($img, (int)$cx, (int)$cy, (int)$radius * 2, (int)$radius * 2, $borderCol);
    }

    private function drawInitials(GdImage $img, string $companyName): void
    {
        $initials = $this->makeInitials($companyName);
        $white = imagecolorallocate($img, 255, 255, 255);

        // Font size approx từ length
        $fontSize = 5; // built-in largest
        $textWidth = imagefontwidth($fontSize) * strlen($initials);
        $textHeight = imagefontheight($fontSize);

        $cx = ($this->size - $textWidth) / 2;
        $cy = ($this->size - $textHeight) / 2;

        // Shadow nhẹ
        $shadow = imagecolorallocatealpha($img, 0, 0, 0, 100);
        imagestring($img, $fontSize, (int)$cx + 2, (int)$cy + 2, $initials, $shadow);
        imagestring($img, $fontSize, (int)$cx, (int)$cy, $initials, $white);
    }

    private function addSquareAccent(GdImage $img): GdImage
    {
        // Vẽ 1 ô vuông nhỏ ở góc dưới-phải (màu sáng)
        $white = imagecolorallocate($img, 255, 255, 255);
        $s = 30;
        imagefilledrectangle(
            $img,
            $this->size - $s - 20,
            $this->size - $s - 20,
            $this->size - 20,
            $this->size - 20,
            $white
        );
        return $img;
    }

    private function addDotAccent(GdImage $img): GdImage
    {
        // 1 dot trắng trên-trái
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefilledellipse($img, 30, 30, 16, 16, $white);
        return $img;
    }

    private function addUnderlineAccent(GdImage $img): GdImage
    {
        // Thanh ngang đậm dưới chữ
        $white = imagecolorallocate($img, 255, 255, 255);
        $y = (int)($this->size * 0.62);
        imagefilledrectangle($img, 70, $y, $this->size - 70, $y + 4, $white);
        return $img;
    }

    /**
     * Lấy 2 chữ cái đầu của tên (uppercase).
     * - "TechVN Solutions" → "TS"
     * - "BrandUp Vietnam" → "BV"
     */
    private function makeInitials(string $name): string
    {
        $name = trim($name);
        $words = preg_split('/\s+/', $name);
        $first = mb_substr($words[0] ?? '', 0, 1);
        if (count($words) >= 2) {
            $second = mb_substr($words[1], 0, 1);
        } else {
            $second = mb_substr($name, 1, 1);
        }
        return mb_strtoupper($first . $second);
    }

    private function hex2rgb(string $hex): array
    {
        $h = ltrim($hex, '#');
        return [
            hexdec(substr($h, 0, 2)),
            hexdec(substr($h, 2, 2)),
            hexdec(substr($h, 4, 2)),
        ];
    }

    /**
     * Convert tên công ty tự do → slug hợp lệ cho file.
     */
    public static function slugFor(string $name): string
    {
        // Bỏ dấu tiếng Việt cơ bản
        $vietnamese = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
            'À' => 'A', 'Á' => 'A', 'Ạ' => 'A', 'Ả' => 'A', 'Ã' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ậ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ặ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A',
            'È' => 'E', 'É' => 'E', 'Ẹ' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ệ' => 'E', 'Ể' => 'E', 'Ễ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ị' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ọ' => 'O', 'Ỏ' => 'O', 'Õ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ộ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ợ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ụ' => 'U', 'Ủ' => 'U', 'Ũ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ự' => 'U', 'Ử' => 'U', 'Ữ' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỵ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
            'Đ' => 'D',
        ];
        $name = strtr($name, $vietnamese);
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
}