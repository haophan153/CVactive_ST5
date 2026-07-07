<?php

namespace Database\Seeders;

use GdImage;

/**
 * Render CV thumbnail PNG dùng GD extension (luôn có sẵn trong PHP).
 *
 * Layouts:
 *   - classic : Header trên + 2 cột (sidebar trái + main phải)
 *   - modern  : Full màu ở header, các section rõ ràng
 *   - minimal : Clean white, header line, typography-focused
 */
class CvThumbnailRenderer
{
    private int $width  = 900;
    private int $height = 1200;

    /**
     * Bảng màu cho từng palette.
     * Format: [label, hex]
     */
    private array $palettes = [
        'indigo'  => ['primary' => '#4F46E5', 'soft' => '#EEF2FF', 'accent' => '#3730A3', 'ink' => '#1E1B4B', 'white' => '#FFFFFF'],
        'slate'   => ['primary' => '#1E293B', 'soft' => '#F1F5F9', 'accent' => '#475569', 'ink' => '#0F172A', 'white' => '#FFFFFF'],
        'emerald' => ['primary' => '#10B981', 'soft' => '#ECFDF5', 'accent' => '#047857', 'ink' => '#064E3B', 'white' => '#FFFFFF'],
        'teal'    => ['primary' => '#0F766E', 'soft' => '#F0FDFA', 'accent' => '#115E59', 'ink' => '#134E4A', 'white' => '#FFFFFF'],
        'amber'   => ['primary' => '#D97706', 'soft' => '#FFFBEB', 'accent' => '#92400E', 'ink' => '#78350F', 'white' => '#FFFFFF'],
        'rose'    => ['primary' => '#E11D48', 'soft' => '#FFF1F2', 'accent' => '#9F1239', 'ink' => '#881337', 'white' => '#FFFFFF'],
        'violet'  => ['primary' => '#7C3AED', 'soft' => '#F5F3FF', 'accent' => '#5B21B6', 'ink' => '#4C1D95', 'white' => '#FFFFFF'],
        'sky'     => ['primary' => '#0369A1', 'soft' => '#F0F9FF', 'accent' => '#075985', 'ink' => '#0C4A6E', 'white' => '#FFFFFF'],
        'cyan'    => ['primary' => '#0891B2', 'soft' => '#ECFEFF', 'accent' => '#155E75', 'ink' => '#164E63', 'white' => '#FFFFFF'],
    ];

    public function render(string $slug, array $spec): GdImage
    {
        $palette = $this->palettes[$spec['palette']] ?? $this->palettes['indigo'];
        $layout  = $spec['layout'] ?? 'classic';
        $name    = $spec['name'] ?? 'YOUR NAME';
        $role    = $spec['role'] ?? 'Professional';

        return match ($layout) {
            'modern'  => $this->renderModern($palette, $name, $role),
            'minimal' => $this->renderMinimal($palette, $name, $role),
            default   => $this->renderClassic($palette, $name, $role),
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

    // ── Primitives ───────────────────────────────────────────────────────

    private function newCanvas(): GdImage
    {
        $img = imagecreatetruecolor($this->width, $this->height);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $this->width, $this->height, $white);
        return $img;
    }

    /**
     * Allocate GD color from hex string.
     */
    private function color(GdImage $img, string $hex): int
    {
        $h = ltrim($hex, '#');
        $r = hexdec(substr($h, 0, 2));
        $g = hexdec(substr($h, 2, 2));
        $b = hexdec(substr($h, 4, 2));
        return imagecolorallocate($img, (int)$r, (int)$g, (int)$b);
    }

    /**
     * TTF text — fallback sang built-in nếu font ttf không có.
     */
    private function text(GdImage $img, int $size, int $x, int $y, string $str, int $color, string $weight = 'normal'): void
    {
        $font = 1; // built-in
        $w = $weight === 'bold' ? 5 : (int) max(1, min(5, ceil($size / 7)));
        imagestring($img, $w, $x, $y, $str, $color);
    }

    private function line(GdImage $img, int $x1, int $y1, int $x2, int $y2, int $color, int $thickness = 1): void
    {
        if ($thickness === 1) {
            imageline($img, $x1, $y1, $x2, $y2, $color);
        } else {
            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color);
        }
    }

    private function rect(GdImage $img, int $x, int $y, int $w, int $h, int $color, bool $filled = false): void
    {
        if ($filled) {
            imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $color);
        } else {
            imagerectangle($img, $x, $y, $x + $w, $y + $h, $color);
        }
    }

    private function ellipse(GdImage $img, int $cx, int $cy, int $w, int $h, int $color, bool $filled = false): void
    {
        if ($filled) {
            imagefilledellipse($img, $cx, $cy, $w, $h, $color);
        } else {
            imageellipse($img, $cx, $cy, $w, $h, $color);
        }
    }

    // ── CLASSIC LAYOUT ───────────────────────────────────────────────────

    private function renderClassic(array $p, string $name, string $role): GdImage
    {
        $img = $this->newCanvas();
        $W = $this->width; $H = $this->height;

        $cPrimary = $this->color($img, $p['primary']);
        $cSoft    = $this->color($img, $p['soft']);
        $cAccent  = $this->color($img, $p['accent']);
        $cInk     = $this->color($img, $p['ink']);
        $cGray    = $this->color($img, '#6B7280');
        $cGrayL   = $this->color($img, '#9CA3AF');
        $cDivider = $this->color($img, '#E5E7EB');

        // Top color band
        $this->rect($img, 0, 0, $W, 18, $cPrimary, true);

        // Avatar circle
        $this->ellipse($img, 80, 80, 70, 70, $cSoft, true);
        // border simulation: ring of small rects around circle
        for ($a = 0; $a < 360; $a += 2) {
            $rad = deg2rad($a);
            $x = (int)(80 + 36 * cos($rad));
            $y = (int)(80 + 36 * sin($rad));
            imagesetpixel($img, $x, $y, $cPrimary);
            imagesetpixel($img, $x, $y - 1, $cPrimary);
        }
        // Initial letter
        $this->text($img, 18, 73, 70, 'A', $cPrimary, 'bold');

        // Name + role
        $this->text($img, 14, 130, 70,  $name, $cInk, 'bold');
        $this->text($img, 11, 130, 92,  $role, $cAccent);

        // Header divider
        $this->line($img, 60, 120, $W - 60, 120, $cDivider);

        // Two columns
        // LEFT column
        $lx = 60; $ly = 150;
        $this->text($img, 11, $lx,       $ly,       'CONTACT', $cPrimary, 'bold');
        $this->rect($img, $lx, $ly + 8,  28, 3, $cPrimary, true);
        $this->text($img, 10, $lx,       $ly + 30,  'email@example.com', $cGray);
        $this->text($img, 10, $lx,       $ly + 50,  '+84 123 456 789', $cGray);
        $this->text($img, 10, $lx,       $ly + 70,  'Ho Chi Minh, VN', $cGray);

        $this->text($img, 11, $lx,       $ly + 110, 'SKILLS', $cPrimary, 'bold');
        $this->rect($img, $lx, $ly + 118, 28, 3, $cPrimary, true);

        // Skill bars
        $skills = [
            ['JavaScript / React',  220],
            ['Node.js',             170],
            ['Database / SQL',      190],
            ['AWS',                 140],
        ];
        foreach ($skills as $i => [$label, $w]) {
            $sy = $ly + 138 + $i * 30;
            $this->rect($img, $lx, $sy, 220, 20, $cSoft, true);
            $this->rect($img, $lx, $sy, $w, 20, $cPrimary, true);
            $this->text($img, 10, $lx + 8, $sy + 5, $label, $cAccent);
        }

        $this->text($img, 11, $lx, $ly + 290, 'LANGUAGES', $cPrimary, 'bold');
        $this->rect($img, $lx, $ly + 298, 28, 3, $cPrimary, true);
        $this->text($img, 10, $lx, $ly + 320, 'Vietnamese - Native', $cGray);
        $this->text($img, 10, $lx, $ly + 340, 'English - Fluent', $cGray);

        // Vertical divider
        $colSplit = 410;
        $this->line($img, $colSplit, $ly, $colSplit, $H - 60, $cDivider);

        // RIGHT column
        $rx = $colSplit + 30;
        $ry = $ly;
        $this->text($img, 11, $rx, $ry,         'PROFILE', $cPrimary, 'bold');
        $this->rect($img, $rx, $ry + 8, 28, 3, $cPrimary, true);
        $this->text($img, 10, $rx, $ry + 30,    'Đam me xay dung san pham', $cGray);
        $this->text($img, 10, $rx, $ry + 50,    'chat luong cao, 5 nam KN.', $cGray);

        $this->text($img, 11, $rx, $ry + 95,    'EXPERIENCE', $cPrimary, 'bold');
        $this->rect($img, $rx, $ry + 103, 28, 3, $cPrimary, true);

        // Item 1
        $this->ellipse($img, $rx + 8, $ry + 130, 8, 8, $cPrimary, true);
        $this->text($img, 11, $rx + 22, $ry + 125, 'Senior Developer', $cInk, 'bold');
        $this->text($img, 10, $rx + 22, $ry + 145, 'FPT Software  -  2022-Now', $cGray);
        $this->text($img, 10, $rx + 22, $ry + 165, 'Lead team 6 nguoi, microservices.', $cGray);

        // Item 2
        $this->ellipse($img, $rx + 8, $ry + 205, 8, 8, $cPrimary, true);
        $this->text($img, 11, $rx + 22, $ry + 200, 'Full-stack Developer', $cInk, 'bold');
        $this->text($img, 10, $rx + 22, $ry + 220, 'VNG Corp  -  2020-2022', $cGray);
        $this->text($img, 10, $rx + 22, $ry + 240, 'Xay dung core platform.', $cGray);

        // Education
        $this->text($img, 11, $rx, $ry + 290, 'EDUCATION', $cPrimary, 'bold');
        $this->rect($img, $rx, $ry + 298, 28, 3, $cPrimary, true);
        $this->text($img, 11, $rx, $ry + 320, 'DH Bach Khoa HCM', $cInk, 'bold');
        $this->text($img, 10, $rx, $ry + 340, 'Ky su CNTT  -  2016-2020', $cGray);

        // Page footer
        $this->text($img, 9, $W - 110, $H - 30, 'CV - Page 1/1', $cGrayL);

        return $img;
    }

    // ── MODERN LAYOUT ────────────────────────────────────────────────────

    private function renderModern(array $p, string $name, string $role): GdImage
    {
        $img = $this->newCanvas();
        $W = $this->width; $H = $this->height;

        $cPrimary = $this->color($img, $p['primary']);
        $cAccent  = $this->color($img, $p['accent']);
        $cSoft    = $this->color($img, $p['soft']);
        $cInk     = $this->color($img, '#1F2937');
        $cGray    = $this->color($img, '#6B7280');

        // Full-color header
        $headerH = 240;
        $this->rect($img, 0, 0, $W, $headerH, $cPrimary, true);
        // Accent corner circle (clipped)
        for ($i = $W - 200; $i < $W + 100; $i += 6) {
            for ($j = -100; $j < 200; $j += 6) {
                $d = sqrt(pow($i - $W, 2) + pow($j - 0, 2));
                if ($d < 220 && $i < $W && $i > 0 && $j > 0 && $j < $headerH) {
                    imagesetpixel($img, $i, $j, $cAccent);
                }
            }
        }

        // Avatar
        $this->ellipse($img, 100, 110, 80, 80, imagecolorallocate($img, 255, 255, 255), true);
        $this->text($img, 20, 90, 100, 'A', $cPrimary, 'bold');

        // Name + role on color
        $white = imagecolorallocate($img, 255, 255, 255);
        $this->text($img, 18, 170, 95,  $name, $white, 'bold');
        $this->text($img, 12, 170, 125, $role, $cSoft);
        $this->text($img, 10, 170, 155, 'email@example.com  -  +84 123 456 789', $cSoft);

        // ABOUT section
        $y = 280;
        $this->text($img, 12, 60, $y, 'ABOUT', $cPrimary, 'bold');
        $this->rect($img, 60, $y + 8, 50, 4, $cPrimary, true);
        $this->text($img, 11, 60, $y + 28, 'Chuyen gia voi hon 7 nam kinh nghiem trong', $cGray);
        $this->text($img, 11, 60, $y + 48, 'linh vuc, dam me tao ra gia tri cho doanh nghiep.', $cGray);

        $y = 380;
        $this->text($img, 12, 60, $y, 'WORK EXPERIENCE', $cPrimary, 'bold');
        $this->rect($img, 60, $y + 8, 50, 4, $cPrimary, true);

        // Item 1
        $this->ellipse($img, 75, $y + 50, 12, 12, $cPrimary, true);
        $this->text($img, 12, 95, $y + 40, 'Senior ' . $role, $cInk, 'bold');
        $this->text($img, 11, 95, $y + 60, 'Tech Company  -  2022 - Present', $cGray);
        $this->text($img, 11, 95, $y + 80, '- Dan dat team 8 nguoi, tang 30%', $cGray);
        $this->text($img, 11, 95, $y + 98, '- Trieu khai 1M+ users', $cGray);

        $this->ellipse($img, 75, $y + 160, 12, 12, $cPrimary, true);
        $this->text($img, 12, 95, $y + 150, $role, $cInk, 'bold');
        $this->text($img, 11, 95, $y + 170, 'Startup XYZ  -  2019 - 2022', $cGray);
        $this->text($img, 11, 95, $y + 190, '- MVP ra mat trong 3 thang', $cGray);

        // Skills
        $y = 660;
        $this->text($img, 12, 60, $y, 'SKILLS', $cPrimary, 'bold');
        $this->rect($img, 60, $y + 8, 50, 4, $cPrimary, true);

        $chips = [
            ['Leadership', 0, 0],
            ['Strategy',   130, 0],
            ['Analytics',  240, 0],
            ['English',    0, 38],
            ['Vietnamese', 100, 38],
        ];
        foreach ($chips as [$label, $px, $py]) {
            $cx = 60 + $px; $cy = $y + 30 + $py;
            $w = strlen($label) * 7 + 20;
            $this->rect($img, $cx, $cy, $w, 28, $cSoft, true);
            $this->text($img, 11, $cx + 10, $cy + 8, $label, $cAccent);
        }

        return $img;
    }

    // ── MINIMAL LAYOUT ───────────────────────────────────────────────────

    private function renderMinimal(array $p, string $name, string $role): GdImage
    {
        $img = $this->newCanvas();
        $W = $this->width; $H = $this->height;

        $cPrimary = $this->color($img, $p['primary']);
        $cAccent  = $this->color($img, $p['accent']);
        $cInk     = $this->color($img, '#111827');
        $cGray    = $this->color($img, '#374151');
        $cGrayL   = $this->color($img, '#9CA3AF');
        $cGrayM   = $this->color($img, '#6B7280');

        // Big serif name
        $this->text($img, 26, 70, 95, $name, $cInk, 'bold');
        $this->text($img, 11, 72, 130, strtoupper($role), $cPrimary);

        // Thin line under header
        $this->line($img, 70, 170, $W - 70, 170, $cInk, 1);

        // LEFT
        $lx = 70; $ly = 200;
        $this->text($img, 9, $lx,       $ly,       'CONTACT', $cGrayL, 'bold');
        $this->text($img, 11, $lx,      $ly + 24,  'email@example.com', $cGray);
        $this->text($img, 11, $lx,      $ly + 44,  '+84 123 456 789', $cGray);
        $this->text($img, 11, $lx,      $ly + 64,  'linkedin.com/in/yourname', $cGray);
        $this->text($img, 11, $lx,      $ly + 102, 'Ho Chi Minh City', $cGray);

        $this->text($img, 9, $lx, $ly + 150, 'SKILLS', $cGrayL, 'bold');
        $items = ['Communication', 'Problem solving', 'Project management', 'Team leadership', 'Strategic planning'];
        foreach ($items as $i => $label) {
            $this->text($img, 11, $lx, $ly + 175 + $i * 22, '- ' . $label, $cInk);
        }

        // RIGHT
        $rx = 470; $ry = 200;
        $this->text($img, 9, $rx, $ry,     'EXPERIENCE', $cPrimary, 'bold');
        $this->rect($img, $rx, $ry + 7, 30, 3, $cPrimary, true);

        $this->text($img, 12, $rx, $ry + 35, 'Senior ' . $role, $cInk, 'bold');
        $this->text($img, 10, $rx, $ry + 55, 'Company Name  -  2022 - Present', $cGrayM);
        $this->text($img, 11, $rx, $ry + 80, 'Dan dat doi ngu xay dung san pham', $cGray);
        $this->text($img, 11, $rx, $ry + 100,'chien luoc, dat tang truong 200%.', $cGray);

        $this->text($img, 12, $rx, $ry + 150, $role, $cInk, 'bold');
        $this->text($img, 10, $rx, $ry + 170, 'Previous Company  -  2019 - 2022', $cGrayM);
        $this->text($img, 11, $rx, $ry + 195, 'Quan ly du an cross-functional.', $cGray);

        $this->text($img, 9, $rx, $ry + 250, 'EDUCATION', $cPrimary, 'bold');
        $this->rect($img, $rx, $ry + 257, 30, 3, $cPrimary, true);
        $this->text($img, 12, $rx, $ry + 285, 'Dai hoc Quoc gia', $cInk, 'bold');
        $this->text($img, 10, $rx, $ry + 305, 'Cu nhan  -  2015 - 2019', $cGrayM);

        return $img;
    }
}
