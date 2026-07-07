<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CVactive - Tạo CV chuyên nghiệp online</title>
    <meta name="description" content="Tạo CV đẹp, chuyên nghiệp trong vài phút. Hàng chục mẫu CV miễn phí, xuất PDF, chia sẻ link ngay.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Noise texture overlay for hero — subtle grain, no gradient */
        .hero-texture::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }

        /* Step number: large editorial number as visual anchor */
        .step-num {
            font-size: clamp(4rem, 10vw, 7rem);
            font-weight: 900;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1.5px #CBD5E1;
            letter-spacing: -0.04em;
            user-select: none;
        }

        /* Testimonial quote: no decorative marks, editorial style */
        .testimonial-quote {
            font-size: clamp(1.05rem, 2vw, 1.25rem);
            line-height: 1.65;
            font-weight: 400;
            color: #334155;
            font-style: normal;
        }

        /* Template skeleton: minimalist, clean lines */
        .template-skeleton {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        }

        /* Feature row: alternating content-image layout */
        .feature-row-even { flex-direction: row-reverse; }

        /* Card hover */
        .card-hover {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        }

        /* Marquee */
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .marquee-track {
            animation: marquee 28s linear infinite;
        }
        .marquee-track:hover {
            animation-play-state: paused;
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
            .marquee-track { animation: none; }
        }
    </style>
</head>
<body class="bg-[#FAFAF9] text-slate-900 antialiased">

    {{-- NAVBAR --}}
    @include('layouts.navigation')

    {{-- ══════════════════════════════════════════════ HERO (centered typographic, no floating cards) ══ --}}
    <section class="relative bg-[#0F172A] text-white pt-20 pb-28 overflow-hidden">
        <div class="hero-texture absolute inset-0"></div>

        {{-- Thin top rule accent --}}
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-indigo-600"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
            <p class="text-[11px] font-semibold tracking-[0.2em] uppercase text-indigo-400 mb-8">
                Nền tảng tạo CV hàng đầu Việt Nam
            </p>

            <h1 class="text-5xl md:text-6xl lg:text-[4.5rem] font-black leading-[1.04] tracking-tight mb-8">
                CV chuyên nghiệp<br>
                <span class="text-white/20">—</span> trong vài phút
            </h1>

            <p class="text-base md:text-lg text-slate-400 leading-relaxed max-w-[52ch] mx-auto mb-10">
                Duyệt mẫu, nhập thông tin, xuất PDF. Không cần kỹ năng thiết kế. Không cần thẻ tín dụng.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-10 py-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-500 active:scale-[0.98] transition shadow-xl shadow-indigo-900/50 text-sm">
                    Tạo CV miễn phí
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="{{ route('templates.index') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-10 py-4 text-slate-400 font-medium rounded-xl hover:text-white hover:bg-white/5 active:scale-[0.98] transition text-sm border border-slate-700">
                    Xem mẫu CV
                </a>
            </div>

            {{-- Trust strip --}}
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 mt-8 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Miễn phí mãi mãi
                </span>
                <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Không cần thẻ tín dụng
                </span>
                <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Xuất PDF ngay
                </span>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ STATS (dark band) ══ --}}
    <section class="bg-[#0F172A] py-10 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-0 divide-x divide-white/10">
                @php
                $stats = [
                    ['value' => '10.000+', 'label' => 'CV đã tạo'],
                    ['value' => '50+',     'label' => 'Mẫu CV'],
                    ['value' => '98%',     'label' => 'Hài lòng'],
                    ['value' => '5 phút',  'label' => 'Tạo xong'],
                ];
                @endphp
                @foreach($stats as $i => $s)
                <div class="px-6 py-4 text-center {{ $i > 0 ? 'border-l border-white/10' : '' }}">
                    <div class="text-2xl md:text-3xl font-black text-white tracking-tight">{{ $s['value'] }}</div>
                    <div class="text-[11px] text-slate-400 mt-1 tracking-wide">{{ $s['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ HOW IT WORKS (editorial numbered layout) ══ --}}
    <section class="py-24 px-6 bg-white">
        <div class="max-w-4xl mx-auto">
            <div class="mb-16">
                <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900">Quy trình đơn giản</h2>
                <p class="text-slate-500 mt-3">Không cần kỹ năng thiết kế. Không cần cài đặt phức tạp.</p>
            </div>

            @php
            $steps = [
                [
                    'num'   => '01',
                    'title' => 'Chọn mẫu phù hợp',
                    'body'  => 'Kho mẫu được phân loại theo ngành nghề và phong cách. Mỗi mẫu đều được thiết kế theo chuẩn ATS.',
                ],
                [
                    'num'   => '02',
                    'title' => 'Nhập thông tin',
                    'body'  => 'Điền thông tin cá nhân, kinh nghiệm, học vấn. Preview cập nhật theo thời gian thực.',
                ],
                [
                    'num'   => '03',
                    'title' => 'Xuất hoặc chia sẻ',
                    'body'  => 'Tải PDF chất lượng cao hoặc tạo link để gửi trực tiếp cho nhà tuyển dụng.',
                ],
            ];
            @endphp

            <div class="grid md:grid-cols-3 gap-8 md:gap-12">
                @foreach($steps as $step)
                <div>
                    <div class="step-num mb-4">{{ $step['num'] }}</div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $step['body'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ FEATURES (alternating editorial rows) ══ --}}
    <section class="py-24 px-6 bg-[#FAFAF9]">
        <div class="max-w-5xl mx-auto space-y-20">

            <div class="mb-12">
                <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900">Mọi thứ bạn cần</h2>
                <p class="text-slate-500 mt-3">Tất cả đều miễn phí. Không giới hạn.</p>
            </div>

            {{-- Feature row 1: Live Preview (text left, image right) --}}
            <div class="flex flex-col md:flex-row items-center gap-10 md:gap-16">
                <div class="flex-1 min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-widest text-indigo-600 mb-3">Live Preview</div>
                    <h3 class="text-2xl md:text-3xl font-black text-slate-900 leading-tight mb-4">
                        CV thay đổi<br>ngay khi bạn gõ
                    </h3>
                    <p class="text-slate-500 leading-relaxed mb-6 max-w-[42ch]">
                        Không cần refresh trang. Không cần chờ render. Mọi thay đổi hiển thị tức thì trên bản xem trước trực tiếp.
                    </p>
                    <ul class="space-y-2.5">
                        @foreach(['Cập nhật theo thời gian thực', 'Không mất dữ liệu khi tắt trình duyệt', 'Hỗ trợ mọi thiết bị'] as $item)
                        <li class="flex items-center gap-2.5 text-sm text-slate-700">
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="w-full md:w-[45%] flex-shrink-0">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <div class="h-2.5 rounded bg-slate-800 w-28 mb-1.5"></div>
                                <div class="h-1.5 rounded bg-slate-200 w-20"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-1.5 rounded bg-slate-200 w-full"></div>
                            <div class="h-1.5 rounded bg-slate-100 w-4/5"></div>
                            <div class="h-1.5 rounded bg-slate-100 w-3/5"></div>
                        </div>
                        <div class="h-px bg-slate-100 my-4"></div>
                        <div class="space-y-1.5">
                            <div class="h-1.5 rounded bg-slate-200 w-full"></div>
                            <div class="h-1.5 rounded bg-slate-100 w-full"></div>
                            <div class="h-1.5 rounded bg-slate-100 w-11/12"></div>
                            <div class="h-1.5 rounded bg-slate-100 w-4/5"></div>
                        </div>
                        <div class="mt-5 flex items-center gap-2 p-3 bg-emerald-50 rounded-xl">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-xs font-semibold text-emerald-700">Sẵn sàng gửi</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feature row 2: PDF Export (image left, text right) --}}
            <div class="flex flex-col md:flex-row-reverse items-center gap-10 md:gap-16">
                <div class="flex-1 min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-widest text-indigo-600 mb-3">Xuất PDF</div>
                    <h3 class="text-2xl md:text-3xl font-black text-slate-900 leading-tight mb-4">
                        Chuẩn A4,<br>sẵn sàng in
                    </h3>
                    <p class="text-slate-500 leading-relaxed mb-6 max-w-[42ch]">
                        Mỗi bản PDF được tối ưu cho in ấn. Chất lượng 300dpi, không vỡ font, không lệch layout khi mở trên máy khác.
                    </p>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition">
                        Thử ngay
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
                <div class="w-full md:w-[45%] flex-shrink-0">
                    <div class="bg-[#0F172A] rounded-2xl p-6 shadow-xl">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                        </div>
                        <div class="bg-white rounded-xl p-4">
                            <div class="flex gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="h-2.5 rounded bg-slate-800 w-3/4 mb-2"></div>
                                    <div class="h-1.5 rounded bg-slate-300 w-1/2 mb-3"></div>
                                    <div class="space-y-1">
                                        <div class="h-1 rounded bg-slate-200 w-full"></div>
                                        <div class="h-1 rounded bg-slate-100 w-full"></div>
                                        <div class="h-1 rounded bg-slate-100 w-4/5"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-[11px] text-slate-500">NguyenVanA_CV.pdf</span>
                            <span class="text-[11px] text-emerald-400 font-medium">Đã tải</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feature row 3: Share Link (text left, image right) --}}
            <div class="flex flex-col md:flex-row items-center gap-10 md:gap-16">
                <div class="flex-1 min-w-0">
                    <div class="text-[11px] font-bold uppercase tracking-widest text-indigo-600 mb-3">Chia sẻ link</div>
                    <h3 class="text-2xl md:text-3xl font-black text-slate-900 leading-tight mb-4">
                        Gửi CV qua<br>Zalo, email, SMS
                    </h3>
                    <p class="text-slate-500 leading-relaxed mb-6 max-w-[42ch]">
                        Tạo link CV cá nhân chỉ với một cú nhấp. Gửi thẳng qua Zalo, email hoặc SMS. Không cần đính kèm file.
                    </p>
                    <ul class="space-y-2.5">
                        @foreach(['Link tồn tại vĩnh viễn', 'Theo dõi lượt xem CV', 'Cập nhật CV mà không cần gửi lại'] as $item)
                        <li class="flex items-center gap-2.5 text-sm text-slate-700">
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="w-full md:w-[45%] flex-shrink-0">
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                            </div>
                            <div class="h-2.5 rounded bg-slate-800 w-32"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-8 rounded-lg border-2 border-dashed border-slate-200 flex items-center justify-center text-xs text-slate-400">
                                cvactive.vn/cv/nguyenvana
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <div class="flex-1 h-8 rounded-lg bg-slate-100"></div>
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 shrink-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ TEMPLATES (dark section, horizontal scroll) ══ --}}
    <section class="py-24 px-6 bg-[#0F172A]">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <h2 class="text-3xl md:text-4xl font-black tracking-tight text-white">Hơn 50 mẫu CV</h2>
                    <p class="text-slate-400 mt-3">Thiết kế bởi chuyên gia HR. Phù hợp mọi ngành nghề.</p>
                </div>
                <a href="{{ route('templates.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-medium text-slate-400 hover:text-white transition shrink-0">
                    Xem tất cả
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            {{-- Template carousel: horizontal scroll, hidden scrollbar --}}
            <div class="relative">
                <div class="overflow-x-auto pb-4 -mx-6 px-6 scrollbar-hide" style="-ms-overflow-style:none;scrollbar-width:none;">
                    <div class="flex gap-4" style="width:max-content;">
                        @php
                        $templates = [
                            ['name' => 'Classic Navy',  'cat' => 'Chuyên nghiệp', 'bg' => '#1E3A5F', 'line' => 'rgba(255,255,255,0.4)', 'text' => 'rgba(255,255,255,0.85)'],
                            ['name' => 'Modern Slate',  'cat' => 'Hiện đại',      'bg' => '#2D3748', 'line' => 'rgba(255,255,255,0.25)', 'text' => 'rgba(255,255,255,0.8)'],
                            ['name' => 'Minimal Light','cat' => 'Đơn giản',     'bg' => '#F1F5F9', 'line' => '#CBD5E1', 'text' => '#1E293B'],
                            ['name' => 'Executive',     'cat' => 'Quản lý',       'bg' => '#0F172A', 'line' => 'rgba(255,255,255,0.2)', 'text' => 'rgba(255,255,255,0.9)'],
                            ['name' => 'Creative',     'cat' => 'Sáng tạo',      'bg' => '#4C1D95', 'line' => 'rgba(255,255,255,0.3)', 'text' => 'rgba(255,255,255,0.9)'],
                        ];
                        @endphp
                        @foreach($templates as $t)
                        <a href="{{ route('templates.index') }}"
                           class="block w-56 flex-shrink-0 rounded-2xl overflow-hidden border border-white/10 card-hover group">
                            {{-- CV skeleton preview --}}
                            <div class="aspect-[3/4] p-4" style="background-color: {{ $t['bg'] }};">
                                <div class="h-full flex flex-col gap-2">
                                    <div class="w-10 h-10 rounded-full" style="background:{{ $t['line'] }};opacity:.35;"></div>
                                    <div class="w-3/4 h-3 rounded" style="background:{{ $t['line'] }};"></div>
                                    <div class="w-1/2 h-2 rounded" style="background:{{ $t['line'] }};opacity:.45;"></div>
                                    <div class="mt-3 space-y-1.5">
                                        <div class="w-full h-1 rounded" style="background:{{ $t['line'] }};opacity:.18;"></div>
                                        <div class="w-full h-1 rounded" style="background:{{ $t['line'] }};opacity:.18;"></div>
                                        <div class="w-11/12 h-1 rounded" style="background:{{ $t['line'] }};opacity:.18;"></div>
                                    </div>
                                    <div class="mt-3 h-px w-full" style="background:{{ $t['line'] }};opacity:.12;"></div>
                                    <div class="space-y-1">
                                        <div class="w-full h-1 rounded" style="background:{{ $t['line'] }};opacity:.12;"></div>
                                        <div class="w-full h-1 rounded" style="background:{{ $t['line'] }};opacity:.12;"></div>
                                        <div class="w-3/4 h-1 rounded" style="background:{{ $t['line'] }};opacity:.12;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-[#1E293B]">
                                <p class="font-semibold text-sm text-white">{{ $t['name'] }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $t['cat'] }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Category pills --}}
            <div class="flex flex-wrap gap-2 mt-8">
                @foreach(['Chuyên nghiệp', 'Hiện đại', 'Đơn giản', 'Sáng tạo', 'Công nghệ', 'Kinh doanh', 'Marketing', 'Thiết kế'] as $cat)
                <a href="{{ route('templates.index') }}"
                   class="px-4 py-1.5 rounded-full text-xs font-medium border border-slate-700 text-slate-400 hover:border-indigo-500 hover:text-indigo-400 hover:bg-indigo-500/10 transition">
                    {{ $cat }}
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ PRICING ══ --}}
    <section class="py-24 px-6 bg-white">
        <div class="max-w-3xl mx-auto">
            <div class="mb-14">
                <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900">Miễn phí để bắt đầu</h2>
                <p class="text-slate-500 mt-3">Không giới hạn tính năng cơ bản. Nâng cấp khi bạn cần nhiều hơn.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Free --}}
                <div class="rounded-2xl border-2 border-slate-200 p-8 bg-white">
                    <div class="text-sm font-semibold text-slate-500 mb-1">Free</div>
                    <div class="flex items-baseline gap-1 mb-1">
                        <span class="text-4xl font-black text-slate-900 tracking-tight">0đ</span>
                        <span class="text-sm text-slate-400">/ vĩnh viễn</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-7">Đủ dùng cho hầu hết mọi người.</p>
                    <ul class="space-y-3 mb-8">
                        @foreach(['2 CV miễn phí', 'Mẫu CV cơ bản', 'Xuất PDF', 'Chia sẻ link online'] as $item)
                        <li class="flex items-start gap-2.5 text-sm text-slate-700">
                            <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}"
                        class="block text-center py-3 border-2 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-semibold rounded-xl text-sm transition">
                        Bắt đầu miễn phí
                    </a>
                </div>

                {{-- Pro --}}
                <div class="rounded-2xl border-2 border-indigo-600 p-8 bg-indigo-50/30 relative">
                    <div class="text-sm font-semibold text-indigo-600 mb-1">Pro</div>
                    <div class="flex items-baseline gap-1 mb-1">
                        <span class="text-4xl font-black text-slate-900 tracking-tight">99K</span>
                        <span class="text-sm text-slate-400">/ tháng</span>
                    </div>
                    <p class="text-sm text-slate-600 mb-7">Toàn bộ tính năng, không giới hạn.</p>
                    <ul class="space-y-3 mb-8">
                        @foreach(['Không giới hạn CV', 'Tất cả mẫu Premium', 'Xuất PDF & PNG chất lượng cao', 'Tuỳ chỉnh màu sắc & font', 'Hỗ trợ ưu tiên'] as $item)
                        <li class="flex items-start gap-2.5 text-sm text-slate-700">
                            <svg class="w-4 h-4 text-indigo-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}"
                        class="block text-center py-3 bg-indigo-600 text-white hover:bg-indigo-700 font-semibold rounded-xl text-sm transition shadow-lg shadow-indigo-600/25">
                        Dùng thử 7 ngày
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ TESTIMONIALS (horizontal scroll marquee) ══ --}}
    <section class="py-24 px-6 bg-[#FAFAF9] overflow-hidden">
        <div class="max-w-5xl mx-auto mb-12">
            <h2 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900">Người dùng nói gì</h2>
        </div>

        {{-- Marquee: 2x identical set for seamless loop --}}
        <div class="overflow-hidden">
            <div class="marquee-track flex gap-4" style="width:max-content;">
                @php
                $testimonials = [
                    [
                        'name'  => 'Minh Đỗ',
                        'role'  => 'Senior Developer, FPT Software',
                        'quote' => 'CVactive giúp tôi nhận được phản hồi từ nhà tuyển dụng trong vòng 48 giờ đầu tiên. Giao diện tối giản, không thừa thãi.',
                        'initials' => 'MD',
                    ],
                    [
                        'name'  => 'Thu Hà',
                        'role'  => 'Product Designer, VNPay',
                        'quote' => 'Là người làm design, tôi khó tính với các công cụ tạo CV. CVactive là một trong số ít công cụ mà tôi thực sự dùng được.',
                        'initials' => 'TH',
                    ],
                    [
                        'name'  => 'Hoàng An',
                        'role'  => 'Marketing Manager, Lazada',
                        'quote' => 'Tôi đã thử nhiều công cụ khác nhưng CVactive nhanh và chính xác nhất. Không phí thời gian với những thứ không cần thiết.',
                        'initials' => 'HA',
                    ],
                    [
                        'name'  => 'Lan Phương',
                        'role'  => 'Data Analyst, Viettel',
                        'quote' => 'CV của tôi trước đây rất dài và rối. Sau khi dùng CVactive, tôi có bản CV ngắn gọn 1 trang mà nhà tuyển dụng khen rõ ràng.',
                        'initials' => 'LP',
                    ],
                    [
                        'name'  => 'Quang Minh',
                        'role'  => 'Fresher, ĐH Bách Khoa',
                        'quote' => 'Sinh viên mới ra trường như tôi mà có CV đẹp như người có 3 năm kinh nghiệm. Quá hài lòng.',
                        'initials' => 'QM',
                    ],
                ];
                $doubled = array_merge($testimonials, $testimonials);
                @endphp

                @foreach($doubled as $t)
                <div class="w-80 flex-shrink-0 bg-white rounded-2xl border border-slate-200 p-7">
                    <p class="testimonial-quote mb-6">{{ $t['quote'] }}</p>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 shrink-0">
                            {{ $t['initials'] }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $t['name'] }}</p>
                            <p class="text-[11px] text-slate-500">{{ $t['role'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════ CTA (clean white, bold typography) ══ --}}
    <section class="py-24 px-6 bg-white border-t border-slate-200">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900 mb-5">
                Sẵn sàng?<br>
                <span class="text-indigo-600">Bắt đầu ngay.</span>
            </h2>
            <p class="text-base text-slate-500 mb-10 max-w-[44ch] mx-auto">
                Tạo tài khoản miễn phí trong 30 giây. Không cần thẻ tín dụng.
            </p>
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center gap-2 px-12 py-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-500 active:scale-[0.98] transition shadow-xl shadow-indigo-600/30 text-sm">
                Tạo CV miễn phí
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <p class="text-xs text-slate-400 mt-4">Dùng thử Pro miễn phí 7 ngày — không tự động gia hạn.</p>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════ FOOTER (with newsletter) ══ --}}
    <footer class="bg-slate-900 text-slate-400 py-14 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('storage/avatars/logo/logo.png') }}" alt="CVactive" class="h-7 w-auto object-contain brightness-0 invert opacity-80">
                        <span class="font-bold text-white">CV<span class="text-indigo-400">active</span></span>
                    </div>
                    <p class="text-sm leading-relaxed mb-5 text-slate-500">Nền tảng tạo CV chuyên nghiệp hàng đầu Việt Nam. Hàng nghìn người dùng đã tạo CV thành công.</p>
                    {{-- Newsletter --}}
                    <form class="flex gap-2" onsubmit="event.preventDefault(); this.querySelector('button').textContent='Đã đăng ký'; this.querySelector('input').disabled=true; this.querySelector('button').disabled=true;">
                        <input type="email" required placeholder="Email của bạn"
                            class="flex-1 px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition min-w-0">
                        <button type="submit" class="px-3 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-500 transition shrink-0">
                            Đăng ký
                        </button>
                    </form>
                </div>

                {{-- Product --}}
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Sản phẩm</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('templates.index') }}" class="hover:text-white transition">Mẫu CV</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-white transition">Bảng giá</a></li>
                        <li><a href="{{ route('cv.create') }}" class="hover:text-white transition">Tạo CV ngay</a></li>
                        <li><a href="{{ route('jobs.index') }}" class="hover:text-white transition">Việc làm</a></li>
                    </ul>
                </div>

                {{-- Resources --}}
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Tài nguyên</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Liên hệ</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h4 class="font-semibold text-white text-sm mb-4">Pháp lý</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="hover:text-white transition">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="hover:text-white transition">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-white transition">Chính sách hoàn tiền</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                <span>&copy; 2026 CVactive. Tất cả quyền được bảo lưu.</span>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-white transition" aria-label="Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="hover:text-white transition" aria-label="LinkedIn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="#" class="hover:text-white transition" aria-label="YouTube">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
