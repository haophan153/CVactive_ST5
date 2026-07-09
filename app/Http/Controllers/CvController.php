<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvSectionItem;
use App\Models\Template;
use App\Support\CvHtmlSanitizer;

class CvController extends Controller
{
    /**
     * Dashboard: danh sách CV của user
     */
    public function index()
    {
        $cvs = auth()->user()->cvs()->with(['template', 'shares'])->latest()->get();
        $cvs->each(function ($cv) {
            $cv->share_url = $cv->shares->isNotEmpty()
                ? route('cv.public', $cv->shares->first()->share_token)
                : null;
        });
        return view('dashboard', compact('cvs'));
    }

    /**
     * Chuyển hướng sang trang chọn template
     */
    public function create()
    {
        return redirect()->route('templates.index');
    }

    /**
     * Tạo CV mới từ template đã chọn
     */
    public function store(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
        ]);

        $user     = auth()->user();
        $plan     = $user->plan;
        $cvLimit  = $plan ? $plan->cv_limit : 2;
        $cvCount  = $user->cvs()->count();

        if ($cvCount >= $cvLimit) {
            return back()->with('error', "Bạn đã đạt giới hạn {$cvLimit} CV của gói hiện tại. Vui lòng nâng cấp để tạo thêm.");
        }

        $template = Template::findOrFail($request->template_id);

        // H2: Free user không được chọn template Premium
        if ($template->is_premium && !$user->hasProAccess()) {
            return back()->with('error', "Template '{$template->name}' chỉ dành cho gói Pro. Vui lòng nâng cấp để sử dụng.");
        }

        // Tăng usage count
        $template->increment('usage_count');

        $cv = Cv::create([
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'CV của ' . $user->name,
            'slug'        => Str::slug($user->name) . '-' . Str::random(6),
            'personal_info' => [
                'full_name'  => $user->name,
                'email'      => $user->email,
                'phone'      => '',
                'address'    => '',
                'website'    => '',
                'linkedin'   => '',
                'github'     => '',
                'avatar'     => $user->avatar ?? '',
            ],
            'objective'   => '',
            'theme_color' => $template->theme_color ?? '#4F46E5',
            'font_family' => $template->font_family ?? 'Inter',
            'visibility'  => 'private',
            'is_draft'    => true,
        ]);

        // Tạo các section mặc định
        $defaultSections = [
            ['type' => 'personal',       'title' => 'Thông tin cá nhân',      'sort_order' => 0],
            ['type' => 'objective',      'title' => 'Mục tiêu nghề nghiệp',   'sort_order' => 1],
            ['type' => 'experience',     'title' => 'Kinh nghiệm làm việc',   'sort_order' => 2],
            ['type' => 'education',      'title' => 'Học vấn',                'sort_order' => 3],
            ['type' => 'skills',         'title' => 'Kỹ năng',                'sort_order' => 4],
            ['type' => 'certifications', 'title' => 'Chứng chỉ',             'sort_order' => 5],
            ['type' => 'projects',       'title' => 'Dự án',                  'sort_order' => 6],
            ['type' => 'activities',     'title' => 'Hoạt động',              'sort_order' => 7],
            ['type' => 'references',     'title' => 'Người tham chiếu',       'sort_order' => 8],
        ];

        foreach ($defaultSections as $section) {
            CvSection::create(array_merge($section, [
                'cv_id'      => $cv->id,
                'is_visible' => true,
                'is_custom'  => false,
            ]));
        }

        return redirect()->route('cv.edit', $cv)->with('success', 'CV đã được tạo! Hãy bắt đầu chỉnh sửa.');
    }

    /**
     * Mở CV editor
     */
    public function edit(Cv $cv)
    {
        $this->authorize('update', $cv);

        $cv->load(['template', 'sections.items']);
        $templates = Template::where('is_active', true)->get();

        // Chuẩn bị dữ liệu sections dạng JSON để tránh lỗi Blade compiler
        $sectionsJson = $cv->sections->map(function($s) {
            return [
                'id' => $s->id,
                'type' => $s->type,
                'title' => $s->title,
                'sort_order' => $s->sort_order,
                'is_visible' => $s->is_visible,
                'is_custom' => $s->is_custom,
                'items' => $s->items->map(function($i) {
                    return [
                        'id' => $i->id,
                        'content' => $i->content,
                        'sort_order' => $i->sort_order,
                    ];
                })->values(),
            ];
        })->values();

        return view('cv.editor', compact('cv', 'templates', 'sectionsJson'));
    }

    /**
     * Lưu toàn bộ CV (AJAX + form)
     */
    public function update(Request $request, Cv $cv)
    {
        $this->authorize('update', $cv);

        $user = auth()->user();

        $request->validate([
            'title'       => 'required|string|max:255',
            'theme_color' => 'nullable|string|max:20',
            'font_family' => 'nullable|string|max:50',
            'visibility'  => 'nullable|in:public,private',
            'objective'   => 'nullable|string',
            'personal_info' => 'nullable|array',
        ]);

        // H4: CV visibility=public chỉ dành cho Pro users (chống spam SEO,
        // free user tạo link public vô tận làm Google index lụi CV).
        $visibility = $request->visibility ?? $cv->visibility;
        if ($visibility === 'public' && !$user->hasProAccess()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'CV công khai chỉ dành cho gói Pro. Vui lòng nâng cấp hoặc chuyển về riêng tư.',
                ], 403);
            }
            return back()->with('error', 'CV công khai chỉ dành cho gói Pro. Vui lòng nâng cấp hoặc chuyển về riêng tư.');
        }

        $cv->update([
            'title'        => $request->title,
            'theme_color'  => $request->theme_color ?? $cv->theme_color,
            'font_family'  => $request->font_family ?? $cv->font_family,
            'visibility'   => $visibility,
            'objective'    => $request->objective,
            'personal_info' => $request->personal_info ?? $cv->personal_info,
            'is_draft'     => $request->boolean('is_draft', $cv->is_draft),
            'last_saved_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã lưu!', 'saved_at' => now()->format('H:i:s')]);
        }

        return back()->with('success', 'CV đã được lưu!');
    }

    /**
     * Xoá CV
     */
    public function destroy(Cv $cv)
    {
        $this->authorize('delete', $cv);
        $cv->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dashboard')->with('success', 'CV đã được xoá.');
    }

    // ─── Section management ────────────────────────────────────────────────

    /**
     * Lưu toàn bộ sections + items (AJAX auto-save)
     */
    public function saveSections(Request $request, Cv $cv)
    {
        $this->authorize('update', $cv);

        $sections = $request->input('sections', []);

        foreach ($sections as $sectionData) {
            $section = CvSection::find($sectionData['id'] ?? null);

            if (!$section || $section->cv_id !== $cv->id) {
                continue;
            }

            $section->update([
                'title'      => $sectionData['title'] ?? $section->title,
                'sort_order' => $sectionData['sort_order'] ?? $section->sort_order,
                'is_visible' => $sectionData['is_visible'] ?? $section->is_visible,
            ]);

            // Xử lý items
            if (isset($sectionData['items'])) {
                $keptIds = [];

                foreach ($sectionData['items'] as $itemData) {
                    if (!empty($itemData['id'])) {
                        $item = CvSectionItem::where('id', $itemData['id'])
                            ->where('cv_section_id', $section->id)
                            ->first();

                        if ($item) {
                            $item->update([
                                'content'    => $itemData['content'],
                                'sort_order' => $itemData['sort_order'] ?? 0,
                            ]);
                            $keptIds[] = $item->id;
                        }
                    } else {
                        $item = CvSectionItem::create([
                            'cv_section_id' => $section->id,
                            'content'       => $itemData['content'],
                            'sort_order'    => $itemData['sort_order'] ?? 0,
                        ]);
                        $keptIds[] = $item->id;
                    }
                }

                // Xoá items không còn tồn tại
                $section->items()->whereNotIn('id', $keptIds)->delete();
            }
        }

        $cv->update(['last_saved_at' => now()]);

        return response()->json(['success' => true, 'saved_at' => now()->format('H:i:s')]);
    }

    /**
     * Thêm section tùy chỉnh
     */
    public function addSection(Request $request, Cv $cv)
    {
        $this->authorize('update', $cv);

        $request->validate(['title' => 'required|string|max:100']);

        $maxOrder = $cv->sections()->max('sort_order') ?? -1;

        $section = CvSection::create([
            'cv_id'      => $cv->id,
            'type'       => 'custom',
            'title'      => $request->title,
            'sort_order' => $maxOrder + 1,
            'is_visible' => true,
            'is_custom'  => true,
        ]);

        return response()->json(['success' => true, 'section' => $section]);
    }

    /**
     * Xoá section
     */
    public function deleteSection(Cv $cv, CvSection $section)
    {
        $this->authorize('update', $cv);

        if ($section->cv_id !== $cv->id) {
            abort(403);
        }

        $section->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Xem CV public qua share token
     */
    public function share(string $token)
    {
        $share = \App\Models\CvShare::where('share_token', $token)->firstOrFail();

        // M-4: validate revoked OR expired → reject
        if (!$share->isActive()) {
            abort(404, 'Share link đã bị thu hồi hoặc hết hạn.');
        }

        // M2: Atomic increment + dùng DB::raw cho view_count để tránh race
        // concurrent request cùng truy cập share token
        \DB::table('cv_shares')
            ->where('id', $share->id)
            ->increment('view_count');

        // Audit trail + rate-limit best-effort
        \DB::table('cv_shares')
            ->where('id', $share->id)
            ->update(['last_viewed_at' => now()]);

        $share->refresh();
        $cv = $share->cv->load(['template', 'sections.items']);

        return view('cv.public', compact('cv', 'share'));
    }

    /**
     * Xuất PDF qua share token (không cần đăng nhập)
     */
    public function exportPdfByShareToken(string $token)
    {
        $share = \App\Models\CvShare::where('share_token', $token)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        $cv = $share->cv->load(['template', 'sections.items']);

        // C-3: PDF generate — chống DoS via payload cực lớn
        set_time_limit(30);
        ini_set('memory_limit', '256M');

        $html = view('cv.pdf', compact('cv'))->render();

        // C-3: Sanitize HTML để chống XSS/file:// scheme payload trong CV content
        $html = CvHtmlSanitizer::purify($html);

        // Ensure all fonts are 'dejavusans' (lowercase, no space) for full Unicode / Vietnamese support
        $html = str_replace(
            "font-family: 'Helvetica', 'Arial', sans-serif",
            "font-family: 'dejavusans', sans-serif",
            $html
        );
        $html = preg_replace('/font-family\s*:\s*["\'][^"\']+["\']/i', "font-family: 'dejavusans'", $html);

        // C-3: Chống LFI/SSRF — tắt local file access, không cho phép
        // DomPDF fetch file://, phar://, hoặc các scheme ngoài.
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable-local-file-access', false);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);
        $pdf->setOption('chroot', realpath(base_path()));

        return $pdf->download(Str::slug($cv->title) . '.pdf');
    }

    /**
     * Xuất PNG qua share token (không cần đăng nhập)
     */
    public function exportPngByShareToken(string $token)
    {
        $share = \App\Models\CvShare::where('share_token', $token)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        $cv = $share->cv->load(['template', 'sections.items']);

        return view('cv.png-export', compact('cv', 'share'));
    }

    /**
     * Tạo / lấy share link
     */
    public function getShareLink(Cv $cv)
    {
        $this->authorize('update', $cv);

        $share = $cv->shares()->first();

        // M-4: Nếu share hết hạn hoặc đã revoke → tạo token mới luôn.
        // Nếu dùng token cũ vĩnh viễn sẽ leak PII sau khi share email forward.
        $needNewToken = !$share
            || ($share->expires_at !== null && $share->expires_at->isPast());

        if ($needNewToken) {
            if ($share) {
                $share->update([
                    'share_token' => Str::random(32),
                    'expires_at'  => now()->addDays(30),  // M-4: default 30-day TTL
                ]);
            } else {
                $share = \App\Models\CvShare::create([
                    'cv_id'       => $cv->id,
                    'share_token' => Str::random(32),
                    'expires_at'  => now()->addDays(30),
                ]);
            }
        }

        $url = route('cv.public', $share->share_token);

        return response()->json([
            'success'    => true,
            'url'        => $url,
            'expires_at' => $share->expires_at?->toIso8601String(),
        ]);
    }

    /**
     * M-4 + L-11: Thu hồi share link ngay lập tức (xóa token, không xóa record
     * để giữ audit trail).
     */
    public function revokeShare(Cv $cv)
    {
        $this->authorize('update', $cv);

        $share = $cv->shares()->first();
        if ($share) {
            $share->update([
                'share_token' => null,
                'expires_at'  => now()->subDay(),  // Mark expired
                'revoked_at'  => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Đổi template của CV (AJAX)
     */
    public function changeTemplate(Request $request, Cv $cv)
    {
        $this->authorize('update', $cv);

        $request->validate([
            'template_id' => 'required|exists:templates,id',
        ]);

        $template = Template::findOrFail($request->template_id);

        // H2: Free user không được đổi sang template Premium
        if ($template->is_premium && !auth()->user()->hasProAccess()) {
            return response()->json([
                'success' => false,
                'error'   => 'Template này chỉ dành cho gói Pro. Vui lòng nâng cấp.',
            ], 403);
        }

        $template->increment('usage_count');

        $cv->update([
            'template_id'  => $request->template_id,
            'theme_color' => $template->theme_color ?? $cv->theme_color,
            'font_family' => $template->font_family ?? $cv->font_family,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Upload avatar cho CV (AJAX)
     */
    public function uploadAvatar(Request $request, Cv $cv)
    {
        $this->authorize('update', $cv);

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Xóa avatar cũ nếu có (chỉ xóa file local an toàn)
        if (
            !empty($cv->personal_info['avatar'])
            && is_string($cv->personal_info['avatar'])
            && !str_starts_with($cv->personal_info['avatar'], 'http')
            && $this->isSafeStoragePath($cv->personal_info['avatar'])
        ) {
            $oldAvatar = \Storage::disk('public')->path($cv->personal_info['avatar']);
            if (file_exists($oldAvatar)) {
                @unlink($oldAvatar);
            }
        }

        // Lưu avatar mới
        $path = $request->file('avatar')->store('avatars', 'public');

        // Cập nhật personal_info
        $personalInfo = $cv->personal_info ?? [];
        $personalInfo['avatar'] = $path;
        $cv->update(['personal_info' => $personalInfo]);

        return response()->json([
            'success' => true,
            'avatar_url' => asset('storage/' . $path),
            'avatar_path' => $path,
        ]);
    }

    /**
     * Xóa avatar của CV (AJAX)
     */
    public function deleteAvatar(Cv $cv)
    {
        $this->authorize('update', $cv);

        if (!empty($cv->personal_info['avatar'])) {
            $oldAvatar = $cv->personal_info['avatar'];

            // H-1: Chỉ delete khi path nằm trong storage path hợp lệ
            // — chặn path traversal (../../config/database.php)
            // và XSS qua URL bên ngoài.
            if (
                is_string($oldAvatar)
                && !str_starts_with($oldAvatar, 'http')
                && $this->isSafeStoragePath($oldAvatar)
            ) {
                $avatarPath = \Storage::disk('public')->path($oldAvatar);
                if (file_exists($avatarPath)) {
                    @unlink($avatarPath);
                }
            }

            $personalInfo = $cv->personal_info ?? [];
            $personalInfo['avatar'] = '';
            $cv->update(['personal_info' => $personalInfo]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * H-1: Validate avatar path nằm trong storage disk 'public'.
     * Chặn ../, absolute path, hoặc symlink trỏ ra ngoài.
     */
    private function isSafeStoragePath(string $path): bool
    {
        if ($path === '' || str_contains($path, '..') || str_contains($path, "\0")) {
            return false;
        }

        $disk = \Storage::disk('public');
        $realBase = realpath($disk->path(''));
        $realPath = realpath($disk->path($path));

        if (!$realBase || !$realPath) {
            return false;
        }

        return str_starts_with($realPath, $realBase);
    }

    /**
     * Lấy preview HTML (AJAX)
     */
    public function getPreview(Cv $cv)
    {
        $this->authorize('view', $cv);

        $cv->load(['template', 'sections.items']);

        // Load template directly from database to avoid any stale data
        $templateModel = Template::find($cv->template_id);
        $bladeView = $templateModel ? $templateModel->blade_view : null;
        $actualView = $bladeView && \View::exists($bladeView) ? $bladeView : 'cv-templates.classic-blue';

        $html = view($actualView, [
            'cv' => $cv,
            'preview' => true,
        ])->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    /**
     * Xuất CV ra PDF
     *
     * L5: set memory + time limit để chống DoS qua PDF generation với content lớn.
     */
    public function exportPdf(Cv $cv)
    {
        $this->authorize('view', $cv);
        $cv->load(['template', 'sections.items']);

        // C-3 + L5: PDF generate — chống DoS + sanitize XSS
        set_time_limit(30);
        ini_set('memory_limit', '256M');

        $html = view('cv.pdf', compact('cv'))->render();

        // C-3: Sanitize HTML trước khi render PDF
        $html = CvHtmlSanitizer::purify($html);

        // Ensure all fonts are 'dejavusans' (lowercase, no space) for full Unicode / Vietnamese support
        // dompdf only recognizes 'dejavusans' (not 'DejaVu Sans' with space)
        $html = str_replace(
            "font-family: 'Helvetica', 'Arial', sans-serif",
            "font-family: 'dejavusans', sans-serif",
            $html
        );
        $html = preg_replace('/font-family\s*:\s*["\'][^"\']+["\']/i', "font-family: 'dejavusans'", $html);

        // C-3: Chống LFI/SSRF — tắt local file access + chroot về base path
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable-local-file-access', false);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);
        $pdf->setOption('chroot', realpath(base_path()));

        return $pdf->download(Str::slug($cv->title) . '.pdf');
    }

    /**
     * Xuất CV ra PNG (server-side via Browsershot or redirect to client)
     */
    public function exportPng(Cv $cv)
    {
        $this->authorize('view', $cv);
        $cv->load(['template', 'sections.items']);

        // Render the CV HTML
        $html = view('cv.pdf', compact('cv'))->render();

        // Return the rendered HTML page with html2canvas auto-download trigger
        return view('cv.png-export', compact('cv', 'html'));
    }
}
