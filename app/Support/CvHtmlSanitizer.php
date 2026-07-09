<?php

namespace App\Support;

use voku\helper\ASCII;

/**
 * C-3: HTML sanitizer cho CV content trước khi render PDF.
 *
 * Loại bỏ các cú pháp có thể bị DomPDF/SVG/HTML parse exploit:
 *   - <base href> → pivot cho file:// hoặc http:// payload
 *   - protocol handler scheme (file:, phar:, java:, javascript:, vbscript:, data:text/html)
 *   - inline event handlers (onerror=, onclick=, onload=, ...)
 *   - meta refresh
 *
 * Chỉ dùng trong pipeline PDF/PNG — KHÔNG thay thế validation input.
 */
class CvHtmlSanitizer
{
    private const FORBIDDEN_PROTOCOLS = [
        'file:', 'phar:', 'phar://', 'jar:', 'expect:',
        'java:', 'javascript:', 'vbscript:', 'data:text/html',
        'data:application/xhtml', 'feed:',
    ];

    private const FORBIDDEN_EVENTS = [
        'onabort', 'onblur', 'onchange', 'onclick', 'ondblclick',
        'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover',
        'ondragstart', 'ondrop', 'onerror', 'onfocus', 'oninput',
        'onkeydown', 'onkeypress', 'onkeyup', 'onload', 'onmousedown',
        'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup',
        'onreset', 'onresize', 'onscroll', 'onselect', 'onsubmit',
        'onunload', 'onfocusin', 'onfocusout', 'oninvalid', 'onsearch',
    ];

    /**
     * Strip out protocol handlers từ URL attributes.
     */
    public static function purify(string $html): string
    {
        // 1. Xoá <base> tag hoàn toàn
        $html = preg_replace('/<\s*base[^>]*>/i', '', $html) ?? $html;
        $html = preg_replace('/<\s*meta[^>]+http-equiv\s*=\s*["\']?refresh["\']?[^>]*>/i', '', $html) ?? $html;

        // 2. Strip dangerous protocols trong mọi attribute (href, src, action, ...)
        foreach (self::FORBIDDEN_PROTOCOLS as $proto) {
            $pattern = '/\b(href|src|action|formaction|background|cite|longdesc|usemap|xlink:href|srcset|data|codebase|profile|manifest)\s*=\s*["\']?\s*[\'"]?' . preg_quote($proto, '/') . '[^"\']*["\']?/i';
            $html = preg_replace($pattern, '', $html) ?? $html;
        }

        // 3. Strip event handlers
        $eventPattern = '/\b(' . implode('|', self::FORBIDDEN_EVENTS) . ')\s*=\s*(["\']?)[^"\']*\2/i';
        $html = preg_replace($eventPattern, '', $html) ?? $html;

        // 4. Strip javascript: scheme ở mọi nơi
        $html = preg_replace('/javascript\s*:/i', '', $html) ?? $html;

        // 5. Strip <script> nội dung inline đã được bảo toàn (vì DomPDF không thực thi JS, nhưng cẩn thận với CSS injection)
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? $html;

        return $html;
    }

    /**
     * Strip base64 / data: trong CSS @font-face để chặn font smuggling.
     */
    public static function sanitizeFontFace(string $css): string
    {
        // Loại bỏ data: trong @font-face src
        return preg_replace(
            '/(@font-face\s*\{[^}]*src\s*:\s*url\()\s*["\']?data:[^"\')]+["\']?\s*(\))/is',
            '$1""$2',
            $css
        ) ?? $css;
    }
}
