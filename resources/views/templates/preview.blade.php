<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->name }} — Xem trước | CVactive</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .cv-preview-wrap { display: contents; }
        @media (max-width: 768px) {
            .cv-document { transform-origin: top center; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

    {{-- TOP BAR --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Quay lại
                </a>
                <span class="text-gray-300">|</span>
                <span class="text-sm font-semibold text-gray-900">{{ $template->name }}</span>
                @if($template->is_premium)
                    <span class="inline-flex items-center gap-1 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        PREMIUM
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                {{-- Zoom controls --}}
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1 mr-2">
                    <button onclick="zoomOut()" class="w-8 h-8 flex items-center justify-center rounded text-gray-500 hover:bg-white hover:shadow-sm transition" title="Thu nhỏ">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                    <span id="zoomLabel" class="text-xs font-semibold text-gray-700 w-10 text-center">100%</span>
                    <button onclick="zoomIn()" class="w-8 h-8 flex items-center justify-center rounded text-gray-500 hover:bg-white hover:shadow-sm transition" title="Phóng to">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                    <button onclick="resetZoom()" class="w-8 h-8 flex items-center justify-center rounded text-gray-500 hover:bg-white hover:shadow-sm transition" title="Đặt lại">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </button>
                </div>
                @auth
                    <form action="{{ route('cv.store') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Sử dụng mẫu này
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Sử dụng mẫu này
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-12 gap-8">
            {{-- LEFT: info sidebar --}}
            <aside class="lg:col-span-3 space-y-5">
                {{-- Template info --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-900 text-lg">{{ $template->name }}</h2>
                    @if($template->category)
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                {{ $template->category->name }}
                            </span>
                        </div>
                    @endif

                    <div class="mt-5 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Lượt xem
                            </span>
                            <span class="font-semibold text-gray-900">{{ $template->usage_label }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Lượt sử dụng
                            </span>
                            <span class="font-semibold text-gray-900">{{ number_format($template->usage_count ?? 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Loại
                            </span>
                            @if($template->is_premium)
                                <span class="inline-flex items-center gap-1 text-amber-600 font-semibold text-sm">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Premium
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Miễn phí
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Features --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-1 h-5 bg-indigo-600 rounded-full"></span>
                        Tính năng mẫu CV
                    </h3>
                    <ul class="space-y-3">
                        @foreach([
                            ['ATS-Friendly', 'Tương thích hệ thống lọc CV tự động', 'text-indigo-600', 'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z'],
                            ['Tùy chỉnh màu', 'Thay đổi màu chủ đạo dễ dàng', 'text-violet-600', 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
                            ['Nhiều font', '10+ font chữ chuyên nghiệp', 'text-emerald-600', 'Aa'],
                            ['Tải đa dạng', 'PDF, DOCX, PNG, liên kết công khai', 'text-amber-600', 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
                        ] as [$title, $sub, $color, $icon])
                            <li class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 {{ $color }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $title }}</span>
                                    <span class="text-xs text-gray-400 block">{{ $sub }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- CTA --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white p-6">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative">
                        <h3 class="text-lg font-bold leading-tight">
                            @if($template->is_premium)
                                Mở khóa mẫu Premium
                            @else
                                Bắt đầu tạo CV ngay
                            @endif
                        </h3>
                        <p class="text-sm text-indigo-100 mt-2">
                            @if($template->is_premium)
                                Nâng cấp gói Premium để sử dụng mẫu này và hàng trăm template khác.
                            @else
                                Đăng nhập để sử dụng mẫu CV này hoàn toàn miễn phí.
                            @endif
                        </p>
                        @auth
                            <form action="{{ route('cv.store') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="template_id" value="{{ $template->id }}">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-white text-indigo-700 px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-amber-300 hover:text-indigo-950 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Tạo CV ngay
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 bg-white text-indigo-700 px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-amber-300 hover:text-indigo-950 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                Đăng nhập để sử dụng
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Share --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Chia sẻ mẫu CV</h3>
                    <div class="flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-blue-50 text-blue-600 text-xs font-semibold hover:bg-blue-100 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode('Mẫu CV ' . $template->name . ' trên CVactive') }}" target="_blank"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-sky-50 text-sky-600 text-xs font-semibold hover:bg-sky-100 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            Twitter
                        </a>
                        <button onclick="navigator.clipboard.writeText('{{ request()->fullUrl() }}'); this.textContent='Đã copy!'"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-gray-200 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            Copy link
                        </button>
                    </div>
                </div>
            </aside>

            {{-- RIGHT: CV preview --}}
            <div class="lg:col-span-9">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                    {{-- CV preview toolbar --}}
                    <div class="bg-gray-50 border-b border-gray-200 px-6 py-3 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            <span class="font-semibold text-gray-700">{{ $template->name }}</span>
                            <span class="mx-1">·</span>
                            <span>Xem trước</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                                A4 · 210 × 297mm
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Kéo để phóng to/thu nhỏ
                            </span>
                        </div>
                    </div>

                    {{-- CV content --}}
                    <div id="cvPreviewContainer" class="p-8 bg-gray-100 overflow-auto" style="min-height: 600px; cursor: grab;" onmousedown="startDrag(event)">
                        <div id="cvWrapper" class="cv-preview-wrap inline-block mx-auto shadow-2xl transition-transform duration-200" style="transform: scale(1); transform-origin: top center;">
                            <div class="cv-document" style="font-family: '{{ $cv->font_family ?? 'Inter' }}', sans-serif; color: #1f2937; min-height: 297mm; width: 210mm;">
                                @include($template->blade_view ?? 'cv-templates.classic-blue', ['cv' => $cv, 'preview' => true])
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tips below --}}
                <div class="mt-6 grid sm:grid-cols-3 gap-4">
                    @foreach([
                        ['Chỉnh sửa dễ dàng', 'Kéo thả để thêm/sắp xếp các phần trong CV.', 'text-indigo-600'],
                        ['Tùy chỉnh hoàn toàn', 'Thay đổi màu sắc, font chữ, nội dung theo ý bạn.', 'text-emerald-600'],
                        ['Tải nhiều định dạng', 'Xuất ra PDF, DOCX, PNG hoặc tạo liên kết công khai.', 'text-amber-600'],
                    ] as [$t, $s, $c])
                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 {{ $c }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">{{ $t }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $s }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        let scale = 1;
        const wrapper = document.getElementById('cvWrapper');
        const label = document.getElementById('zoomLabel');
        const container = document.getElementById('cvPreviewContainer');

        function updateScale() {
            wrapper.style.transform = `scale(${scale})`;
            label.textContent = Math.round(scale * 100) + '%';
        }

        function zoomIn() {
            scale = Math.min(scale + 0.1, 2);
            updateScale();
        }

        function zoomOut() {
            scale = Math.max(scale - 0.1, 0.4);
            updateScale();
        }

        function resetZoom() {
            scale = 1;
            updateScale();
        }

        // Wheel zoom
        container.addEventListener('wheel', (e) => {
            if (e.ctrlKey) {
                e.preventDefault();
                scale = Math.min(Math.max(scale + (e.deltaY > 0 ? -0.05 : 0.05), 0.4), 2);
                updateScale();
            }
        });

        // Drag to pan
        let isDragging = false, startX, startY, scrollX, scrollY;
        function startDrag(e) {
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) return;
            isDragging = true;
            container.style.cursor = 'grabbing';
            startX = e.clientX + container.scrollLeft;
            startY = e.clientY + container.scrollTop;
        }
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            container.scrollLeft = startX - e.clientX;
            container.scrollTop = startY - e.clientY;
        });
        document.addEventListener('mouseup', () => {
            isDragging = false;
            container.style.cursor = 'grab';
        });
    </script>
</body>
</html>
