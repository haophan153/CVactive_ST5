<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $cv->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'dejavusans', sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        .cv-document { width: 100%; }
    </style>
</head>
<body>
    @php
        // ── Load Template ─────────────────────────────────────────────────────
        $templateModel = \App\Models\Template::find($cv->template_id);
        $bladeView = $templateModel ? $templateModel->blade_view : null;
        $actualView = $bladeView && \View::exists($bladeView) ? $bladeView : 'cv-templates.classic-blue';

        $templateHtml = view($actualView, ['cv' => $cv, 'preview' => false])->render();

        // ── Font: Map Google Fonts → DomPDF equivalents ─────────────────────
        $fontFamily = $cv->font_family ?? 'Inter';
        $fontMap = [
            'Inter'   => 'dejavusans', 'Roboto' => 'dejavusans',
            'Open Sans' => 'dejavusans', 'Lato' => 'dejavusans',
            'Montserrat' => 'dejavusans', 'Poppins' => 'dejavusans',
            'Nunito'  => 'dejavusans', 'Raleway' => 'dejavusans',
            'Fira Sans' => 'dejavusans', 'Ubuntu' => 'dejavusans',
            'Oswald'  => 'dejavusans', 'Archivo' => 'dejavusans',
            'Work Sans' => 'dejavusans',
            'Playfair Display' => 'serif', 'Merriweather' => 'serif',
            'Lora'    => 'serif', 'Source Serif Pro' => 'serif',
            'JetBrains Mono' => 'monospace', 'Fira Code' => 'monospace',
            'Source Code Pro' => 'monospace',
        ];
        $dompdfFont = $fontMap[$fontFamily] ?? 'dejavusans';

        $templateHtml = preg_replace(
            '/font-family\s*:\s*["\'][^"\']+["\']\s*[,;]/i',
            "font-family: '{$dompdfFont}', sans-serif;",
            $templateHtml
        );
        $templateHtml = preg_replace(
            '/font-family\s*:\s*["\'][^"\']+["\']\s*;/i',
            "font-family: '{$dompdfFont}';",
            $templateHtml
        );

        // ── Gradient: Preserve gradient in DomPDF ────────────────────────────
        // DomPDF 0.8+ supports linear-gradient. Replace with theme-aware gradient.
        $themeColor = $cv->theme_color ?? '#4F46E5';
        $r = hexdec(substr($themeColor, 1, 2));
        $g = hexdec(substr($themeColor, 3, 2));
        $b = hexdec(substr($themeColor, 5, 2));
        $darkColor = sprintf('#%02x%02x%02x',
            max(0, $r - 40), max(0, $g - 40), max(0, $b - 40));

        $templateHtml = preg_replace(
            '/background\s*:\s*linear-gradient\([^)]+\)/i',
            "background: linear-gradient(135deg, {$themeColor} 0%, {$darkColor} 100%)",
            $templateHtml
        );
        $templateHtml = preg_replace(
            '/background\s*:\s*[^;]*linear-gradient[^;]+;/i',
            "background: linear-gradient(135deg, {$themeColor} 0%, {$darkColor} 100%);",
            $templateHtml
        );

        // ── Image: Convert URLs to base64 for DomPDF ─────────────────────────
        $projectRoot = realpath(base_path());

        $templateHtml = preg_replace_callback(
            '/src=["\']([^"\']+)["\']/',
            function ($matches) use ($projectRoot) {
                $src = $matches[1];
                if (str_starts_with($src, 'data:')) return $matches[0];

                $findAvatarBase64 = function($filename) use ($projectRoot) {
                    $paths = [
                        $projectRoot . '/public/storage/avatars/' . $filename,
                        $projectRoot . '/storage/app/public/avatars/' . $filename,
                    ];
                    foreach ($paths as $path) {
                        if (file_exists($path)) {
                            $mime = 'image/webp';
                            if (preg_match('/\.jpe?g$/i', $path)) $mime = 'image/jpeg';
                            elseif (preg_match('/\.png$/i', $path)) $mime = 'image/png';
                            elseif (preg_match('/\.gif$/i', $path)) $mime = 'image/gif';
                            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                        }
                    }
                    return null;
                };

                if (preg_match('/^https?:\/\//i', $src)) {
                    $parsed = parse_url($src);
                    $path = $parsed['path'] ?? '';
                    $filename = null;
                    if (preg_match('#/storage/avatars/([^/\?]+)#', $path, $m)) $filename = $m[1];
                    elseif (preg_match('#/avatars/([^/\?]+)#', $path, $m)) $filename = $m[1];
                    if ($filename) {
                        $b64 = $findAvatarBase64($filename);
                        if ($b64) return 'src="' . $b64 . '"';
                    }
                    return 'src=""';
                }

                if (preg_match('#^(/)?storage/avatars/([^/\?]+)#', $src, $m)) {
                    $b64 = $findAvatarBase64($m[2]);
                    if ($b64) return 'src="' . $b64 . '"';
                    return 'src=""';
                }
                if (preg_match('#^avatars/([^/\?]+)#', $src, $m)) {
                    $b64 = $findAvatarBase64($m[1]);
                    if ($b64) return 'src="' . $b64 . '"';
                    return 'src=""';
                }

                return $matches[0];
            },
            $templateHtml
        );

        // ── Border-radius: Fix 50% → 9999px for DomPDF ──────────────────────
        $templateHtml = preg_replace(
            '/border-radius\s*:\s*50%/i',
            'border-radius: 9999px',
            $templateHtml
        );

        // ── Colors: Convert rgba with high opacity → solid rgb ───────────────
        $templateHtml = preg_replace_callback(
            '/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/i',
            function($m) {
                $a = floatval($m[4]);
                if ($a > 0.8) return "rgb({$m[1]}, {$m[2]}, {$m[3]})";
                return $m[0];
            },
            $templateHtml
        );

        // ── Images: Ensure explicit dimensions ───────────────────────────────
        $templateHtml = preg_replace_callback(
            '/<img([^>]*)>/i',
            function($m) {
                $attrs = $m[1];
                if (strpos($attrs, 'width') === false && strpos($attrs, 'height') === false) {
                    if (preg_match('/style="[^"]*width:\s*(\d+)px/i', $attrs, $w)) {
                        $attrs .= ' width="' . $w[1] . '"';
                    }
                    if (preg_match('/style="[^"]*height:\s*(\d+)px/i', $attrs, $h)) {
                        $attrs .= ' height="' . $h[1] . '"';
                    }
                }
                return '<img' . $attrs . '>';
            },
            $templateHtml
        );

        echo $templateHtml;
    @endphp
</body>
</html>
