<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvSectionItem;
use App\Models\Template;

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
            'theme_color' => '#4F46E5',
            'font_family' => 'Inter',
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

        $request->validate([
            'title'       => 'required|string|max:255',
            'theme_color' => 'nullable|string|max:20',
            'font_family' => 'nullable|string|max:50',
            'visibility'  => 'nullable|in:public,private',
            'objective'   => 'nullable|string',
            'personal_info' => 'nullable|array',
        ]);

        $cv->update([
            'title'        => $request->title,
            'theme_color'  => $request->theme_color ?? $cv->theme_color,
            'font_family'  => $request->font_family ?? $cv->font_family,
            'visibility'   => $request->visibility ?? $cv->visibility,
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
        $share = \App\Models\CvShare::where('share_token', $token)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        $share->increment('view_count');
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

        $html = view('cv.pdf', compact('cv'))->render();

        // Ensure all fonts are 'dejavusans' (lowercase, no space) for full Unicode / Vietnamese support
        // dompdf only recognizes 'dejavusans' (not 'DejaVu Sans' with space)
        $html = str_replace(
            "font-family: 'Helvetica', 'Arial', sans-serif",
            "font-family: 'dejavusans', sans-serif",
            $html
        );
        $html = preg_replace('/font-family\s*:\s*["\'][^"\']+["\']/i', "font-family: 'dejavusans'", $html);

        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

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

        if (!$share) {
            $share = \App\Models\CvShare::create([
                'cv_id'       => $cv->id,
                'share_token' => Str::random(32),
            ]);
        }

        $url = route('cv.public', $share->share_token);

        return response()->json(['success' => true, 'url' => $url]);
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
        $template->increment('usage_count');

        $cv->update(['template_id' => $request->template_id]);

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

        // Xóa avatar cũ nếu có (chỉ xóa file local, không xóa URL bên ngoài như Google avatars)
        if (!empty($cv->personal_info['avatar']) && !str_starts_with($cv->personal_info['avatar'], 'http')) {
            $oldAvatar = public_path('storage/' . $cv->personal_info['avatar']);
            if (file_exists($oldAvatar)) {
                unlink($oldAvatar);
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

            // Only delete local files (skip external URLs like Google avatars)
            if (!str_starts_with($oldAvatar, 'http')) {
                $avatarPath = public_path('storage/' . $oldAvatar);
                if (file_exists($avatarPath)) {
                    unlink($avatarPath);
                }
            }

            $personalInfo = $cv->personal_info ?? [];
            $personalInfo['avatar'] = '';
            $cv->update(['personal_info' => $personalInfo]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Lấy preview HTML (AJAX)
     */
    public function getPreview(Cv $cv)
    {
        $this->authorize('view', $cv);

        $cv->load(['template', 'sections.items']);

        // Load template directly from database to avoid any stale data
        $templateModel = \App\Models\Template::find($cv->template_id);
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
     */
    public function exportPdf(Cv $cv)
    {
        $this->authorize('view', $cv);
        $cv->load(['template', 'sections.items']);

        $html = view('cv.pdf', compact('cv'))->render();

        // Ensure all fonts are 'dejavusans' (lowercase, no space) for full Unicode / Vietnamese support
        // dompdf only recognizes 'dejavusans' (not 'DejaVu Sans' with space)
        $html = str_replace(
            "font-family: 'Helvetica', 'Arial', sans-serif",
            "font-family: 'dejavusans', sans-serif",
            $html
        );
        $html = preg_replace('/font-family\s*:\s*["\'][^"\']+["\']/i', "font-family: 'dejavusans'", $html);

        $pdf = \PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

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
