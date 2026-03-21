<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cv->personal_info['full_name'] ?? $cv->title }} – CV</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden" style="font-family: '{{ $cv->font_family }}', sans-serif; min-height: 297mm;">
            @php
                // Load template directly from database to avoid any stale data
                $templateModel = \App\Models\Template::find($cv->template_id);
                $bladeView = $templateModel ? $templateModel->blade_view : null;
                $actualView = $bladeView && \View::exists($bladeView) ? $bladeView : 'cv-templates.classic-blue';
            @endphp
            @include($actualView, ['cv' => $cv, 'preview' => false])
        </div>

        <div class="mt-6 text-center space-y-3">
            <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="{{ route('cv.public.pdf', $share->share_token) }}" target="_blank"
                class="inline-flex items-center space-x-2 px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Tải PDF</span>
            </a>
            <a href="{{ route('cv.public.png', $share->share_token) }}" target="_blank"
                class="inline-flex items-center space-x-2 px-5 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Tải PNG</span>
            </a>
            </div>
            <p class="text-xs text-gray-400">
                Tạo CV chuyên nghiệp tại <a href="{{ route('home') }}" class="text-indigo-600 hover:underline">CVactive.vn</a>
            </p>
        </div>
    </div>
</body>
</html>
