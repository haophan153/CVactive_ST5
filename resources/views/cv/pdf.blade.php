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
        // Load template directly from database to avoid any stale data
        $templateModel = \App\Models\Template::find($cv->template_id);
        $bladeView = $templateModel ? $templateModel->blade_view : null;
        $actualView = $bladeView && \View::exists($bladeView) ? $bladeView : 'cv-templates.classic-blue';
        $templateHtml = view($actualView, [
            'cv' => $cv,
            'preview' => false,
        ])->render();

        // FIX 1: Replace all font-family declarations with 'dejavusans' (lowercase, no space)
        // dompdf ONLY recognizes 'dejavusans' (from installed-fonts.json key) — 'DejaVu Sans' fails silently
        $templateHtml = preg_replace(
            '/font-family\s*:\s*["\'][^"\']+["\']\s*,/i',
            "font-family: 'dejavusans',",
            $templateHtml
        );
        $templateHtml = preg_replace(
            '/font-family\s*:\s*["\'][^"\']+["\']\s*;/i',
            "font-family: 'dejavusans';",
            $templateHtml
        );

        // FIX 2: Convert image URLs to local filesystem paths for domPDF
        $projectRoot = realpath(base_path());
        $baseUrl = rtrim(url('/'), '/');
        
        $templateHtml = preg_replace_callback(
            '/src=["\']([^"\']+)["\']/',
            function ($matches) use ($projectRoot, $baseUrl) {
                $src = $matches[1];

                // Skip data URIs
                if (str_starts_with($src, 'data:')) {
                    return $matches[0];
                }

                // Helper function to find avatar file and return base64
                $findAvatarBase64 = function($filename) use ($projectRoot) {
                    $paths = [
                        $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $filename,
                        $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $filename,
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

                // Handle full URLs (http:// or https://)
                if (preg_match('/^https?:\/\//i', $src)) {
                    // Extract path from URL
                    $parsed = parse_url($src);
                    $path = $parsed['path'] ?? '';
                    
                    // Extract filename from /storage/avatars/xxx or /avatars/xxx
                    $filename = null;
                    if (preg_match('#/storage/avatars/([^/\?]+)#', $path, $m)) {
                        $filename = $m[1];
                    } elseif (preg_match('#/avatars/([^/\?]+)#', $path, $m)) {
                        $filename = $m[1];
                    }
                    
                    if ($filename) {
                        $base64 = $findAvatarBase64($filename);
                        if ($base64) {
                            return 'src="' . $base64 . '"';
                        }
                    }
                    return 'src=""';
                }

                // Handle /storage/avatars/xxx paths
                if (preg_match('#^(/)?storage/avatars/([^/\?]+)#', $src, $pathMatches)) {
                    $filename = $pathMatches[2];
                    $base64 = $findAvatarBase64($filename);
                    if ($base64) {
                        return 'src="' . $base64 . '"';
                    }
                    return 'src=""';
                }

                // Handle avatars/xxx paths (relative)
                if (preg_match('#^avatars/([^/\?]+)#', $src, $pathMatches)) {
                    $filename = $pathMatches[1];
                    $base64 = $findAvatarBase64($filename);
                    if ($base64) {
                        return 'src="' . $base64 . '"';
                    }
                    return 'src=""';
                }

                return $matches[0];
            },
            $templateHtml
        );
        
        // FIX 3: Remove or simplify box-shadow (domPDF doesn't render them well)
        $templateHtml = preg_replace(
            '/box-shadow\s*:\s*[^;]+;/i',
            '',
            $templateHtml
        );
        
        // FIX 4: Remove or simplify gradients (domPDF doesn't support them well)
        $themeColor = $cv->theme_color ?? '#4F46E5';
        $templateHtml = preg_replace(
            '/background\s*:\s*linear-gradient\([^)]+\)/i',
            'background-color: ' . $themeColor,
            $templateHtml
        );
        
        // Also handle background with gradient in shorthand
        $templateHtml = preg_replace(
            '/background\s*:\s*[^;]*linear-gradient[^;]+/i',
            'background-color: ' . $themeColor,
            $templateHtml
        );
        
        // FIX 5: Ensure border-radius works (domPDF supports it but needs explicit values)
        $templateHtml = preg_replace(
            '/border-radius\s*:\s*50%/i',
            'border-radius: 9999px',
            $templateHtml
        );
        
        // FIX 6: Convert flexbox to table for better compatibility (but keep flex-wrap as is)
        // Only convert flex containers that don't have flex-wrap
        $templateHtml = preg_replace_callback(
            '/style="([^"]*)"/i',
            function($matches) {
                $style = $matches[1];
                // If it has display:flex but no flex-wrap, consider converting
                // For now, keep flex but ensure it works
                if (strpos($style, 'display: flex') !== false && strpos($style, 'flex-wrap') === false) {
                    // Keep flex for now as domPDF 2.0+ supports it better
                    // But ensure align-items and justify-content are preserved
                }
                return $matches[0];
            },
            $templateHtml
        );
        
        // FIX 7: Ensure object-fit: cover works (domPDF may not support it, use width/height instead)
        // This is already handled by the img tag having width: 100%; height: 100%
        
        // FIX 8: Remove opacity from rgba colors in borders (convert to solid)
        $templateHtml = preg_replace_callback(
            '/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/i',
            function($matches) {
                $r = $matches[1];
                $g = $matches[2];
                $b = $matches[3];
                $a = floatval($matches[4]);
                // If opacity is high enough, use solid color
                if ($a > 0.8) {
                    return "rgb($r, $g, $b)";
                }
                return $matches[0];
            },
            $templateHtml
        );
        
        // FIX 9: Ensure all images have explicit dimensions for better PDF rendering
        $templateHtml = preg_replace_callback(
            '/<img([^>]*)>/i',
            function($matches) {
                $attrs = $matches[1];
                // If no width/height, add them based on style
                if (strpos($attrs, 'width') === false && strpos($attrs, 'height') === false) {
                    if (preg_match('/style="[^"]*width:\s*(\d+)px/i', $attrs, $wMatch)) {
                        $attrs .= ' width="' . $wMatch[1] . '"';
                    }
                    if (preg_match('/style="[^"]*height:\s*(\d+)px/i', $attrs, $hMatch)) {
                        $attrs .= ' height="' . $hMatch[1] . '"';
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
