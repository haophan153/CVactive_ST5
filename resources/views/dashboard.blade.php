@php
    use Illuminate\Support\Carbon;
    $greeting = match(true) {
        Carbon::now()->hour < 12 => 'Chào buổi sáng',
        Carbon::now()->hour < 18 => 'Chào buổi chiều',
        default => 'Chào buổi tối',
    };
@endphp

<x-app-layout>

    {{-- ── Header bar ─────────────────────────────────────────── --}}
    <div class="bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                {{-- Greeting + date --}}
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 bg-slate-800 px-2.5 py-1 rounded-full border border-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ $greeting }}
                        </span>
                        @if(auth()->user()->plan && auth()->user()->plan->slug !== 'free')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-400 bg-amber-400/10 border border-amber-400/20 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ auth()->user()->plan->name }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">
                        {{ auth()->user()->name }}
                    </h1>
                    <p class="text-sm text-slate-400 mt-1">{{ Carbon::now()->translatedFormat('l, d/m/Y') }}</p>
                </div>

                {{-- Primary actions --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('cv.create') }}"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-blue-900/40 transition-all duration-200 hover:shadow-blue-800/50 active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tạo CV mới
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main content ─────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- ══ Stat Cards ══════════════════════════════════════ --}}
        <section aria-label="Tổng quan số liệu">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $cards = [
                        [
                            'key'   => 'total',
                            'label' => 'Tổng CV',
                            'value' => $stats['total'],
                            'sub'   => 'đã tạo',
                            'icon'  => 'document',
                            'bg'    => 'bg-blue-500/10',
                            'icon_bg' => 'bg-blue-500',
                            'bar'   => 'bg-blue-500',
                            'trend' => $stats['total'] > 0 ? '+' . $stats['total'] . ' cv' : null,
                        ],
                        [
                            'key'   => 'completed',
                            'label' => 'Hoàn thành',
                            'value' => $stats['completed'],
                            'sub'   => 'sẵn sàng dùng',
                            'icon'  => 'check',
                            'bg'    => 'bg-emerald-500/10',
                            'icon_bg' => 'bg-emerald-500',
                            'bar'   => 'bg-emerald-500',
                            'trend' => $stats['completed'] > 0 ? round($stats['completed'] / max(1, $stats['total']) * 100) . '% hoàn thiện' : null,
                        ],
                        [
                            'key'   => 'drafts',
                            'label' => 'Đang nháp',
                            'value' => $stats['drafts'],
                            'sub'   => 'cần hoàn thiện',
                            'icon'  => 'clock',
                            'bg'    => 'bg-amber-500/10',
                            'icon_bg' => 'bg-amber-500',
                            'bar'   => 'bg-amber-500',
                            'trend' => $stats['drafts'] > 0 ? 'Cần xử lý' : 'Tất cả xong',
                        ],
                        [
                            'key'   => 'applications',
                            'label' => 'Đã ứng tuyển',
                            'value' => $stats['applications'],
                            'sub'   => 'công việc',
                            'icon'  => 'briefcase',
                            'bg'    => 'bg-purple-500/10',
                            'icon_bg' => 'bg-purple-500',
                            'bar'   => 'bg-purple-500',
                            'trend' => null,
                        ],
                        [
                            'key'   => 'job-matcher',
                            'label' => 'Việc cho bạn',
                            'value' => '⚡',
                            'sub'   => 'Smart Matcher',
                            'icon'  => 'zap',
                            'bg'    => 'bg-indigo-500/10',
                            'icon_bg' => 'bg-indigo-500',
                            'bar'   => 'bg-indigo-500',
                            'trend' => null,
                            'url'   => route('dashboard.job-alerts'),
                        ],
                    ];
                @endphp
                @foreach($cards as $card)
                @php $cardUrl = $card['url'] ?? null; @endphp
                @if($cardUrl)<a href="{{ $cardUrl }}" class="block">@endif
                <div class="group relative bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
                    {{-- Decorative top stripe --}}
                    <div class="absolute top-0 left-0 right-0 h-0.5 {{ $card['bar'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-0.5">{{ $card['label'] }}</p>
                            <div class="flex items-end gap-1.5">
                                <span class="text-3xl font-black text-slate-900 tabular-nums">{{ is_numeric($card['value']) ? number_format((float) $card['value']) : $card['value'] }}</span>
                            </div>
                        </div>
                        <div class="{{ $card['bg'] }} rounded-xl p-2.5 group-hover:scale-110 transition-transform duration-200">
                            <div class="{{ $card['icon_bg'] }} rounded-lg p-1.5">
                                @if($card['icon'] === 'document')
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($card['icon'] === 'check')
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @elseif($card['icon'] === 'clock')
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($card['icon'] === 'zap')
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                @endif
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400 mb-3">{{ $card['sub'] }}</p>

                    {{-- Mini progress bar --}}
                    <div class="h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $card['bar'] }} rounded-full transition-all duration-700"
                             style="width: {{ is_numeric($card['value']) ? min(100, $card['value'] * 100 / max(1, $stats['total'])) : 0 }}%"></div>
                    </div>

                    @if($card['trend'])
                        <p class="mt-2 text-[10px] font-semibold text-slate-500">{{ $card['trend'] }}</p>
                    @endif
                </div>
                @if($cardUrl)</a>@endif
                @endforeach
            </div>
        </section>

        {{-- ══ Completion Hero + Quick Actions ══════════════════ --}}
        <section class="grid lg:grid-cols-5 gap-4">

            {{-- Completion ring card --}}
            <div class="lg:col-span-2 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl shadow-slate-900/30 overflow-hidden relative">
                {{-- Background decorative circles --}}
                <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-blue-600/10 blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-emerald-600/10 blur-2xl"></div>

                <div class="relative">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Mức độ hoàn thiện</p>
                            <div class="flex items-end gap-2">
                                <span class="text-6xl font-black text-white tabular-nums">{{ $completion }}</span>
                                <span class="text-3xl font-bold text-slate-400 mb-1">%</span>
                            </div>
                        </div>

                        {{-- Circular progress --}}
                        <div class="relative w-20 h-20 shrink-0">
                            <svg class="w-20 h-20 -rotate-90" viewBox="0 0 80 80">
                                <circle cx="40" cy="40" r="32" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6"/>
                                <circle cx="40" cy="40" r="32" fill="none" stroke="#2563EB" stroke-width="6"
                                    stroke-dasharray="{{ 2 * 3.14159 * 32 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 32 * (1 - $completion / 100) }}"
                                    stroke-linecap="round"
                                    class="transition-all duration-1000"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                @if($completion >= 80)
                                    <svg class="w-7 h-7 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @elseif($completion >= 40)
                                    <svg class="w-7 h-7 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                @else
                                    <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="h-2 w-full bg-white/10 rounded-full overflow-hidden mb-4">
                        <div class="h-full rounded-full transition-all duration-1000
                            @if($completion >= 80) bg-gradient-to-r from-blue-500 to-emerald-400
                            @elseif($completion >= 40) bg-gradient-to-r from-blue-500 to-amber-400
                            @else bg-blue-500
                            @endif"
                            style="width: {{ $completion }}%"></div>
                    </div>

                    <p class="text-sm text-slate-300 leading-relaxed">
                        @if($completion < 40)
                            Bổ sung thông tin cá nhân, kinh nghiệm và kỹ năng để CV thu hút nhà tuyển dụng hơn.
                        @elseif($completion < 80)
                            Khá tốt rồi! Thêm vài chi tiết nữa để CV hoàn hảo hơn.
                        @else
                            Tuyệt vời — CV của bạn đã sẵn sàng gây ấn tượng!
                        @endif
                    </p>

                    @if($cvs->isNotEmpty() && $cvs->first())
                        <a href="{{ route('cv.edit', $cvs->first()) }}"
                            class="inline-flex items-center gap-2 mt-4 text-xs font-semibold text-blue-400 hover:text-blue-300 transition">
                            Hoàn thiện ngay
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Hành động nhanh</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Truy cập các tính năng phổ biến</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $actions = [
                            ['href' => route('cv.create'),         'label' => 'CV mới',      'icon' => 'plus',        'color' => 'blue',   'desc' => 'Tạo từ đầu'],
                            ['href' => route('templates.index'),  'label' => 'Templates',   'icon' => 'grid',        'color' => 'slate',  'desc' => 'Chọn mẫu có sẵn'],
                            ['href' => route('jobs.index'),        'label' => 'Việc làm',    'icon' => 'briefcase',   'color' => 'emerald','desc' => 'Tìm việc phù hợp'],
                            ['href' => route('profile.edit'),     'label' => 'Hồ sơ',       'icon' => 'user',        'color' => 'purple', 'desc' => 'Cập nhật thông tin'],
                        ];
                        $actionColors = [
                            'blue'    => ['bg' => 'bg-blue-50',    'hover_bg' => 'bg-blue-100',    'border' => 'border-blue-200',    'text' => 'text-blue-600',    'hover_text' => 'text-blue-700'],
                            'slate'   => ['bg' => 'bg-slate-50',  'hover_bg' => 'bg-slate-100',  'border' => 'border-slate-200',  'text' => 'text-slate-600',  'hover_text' => 'text-slate-700'],
                            'emerald' => ['bg' => 'bg-emerald-50', 'hover_bg' => 'bg-emerald-100', 'border' => 'border-emerald-200', 'text' => 'text-emerald-600', 'hover_text' => 'text-emerald-700'],
                            'purple'  => ['bg' => 'bg-purple-50',  'hover_bg' => 'bg-purple-100',  'border' => 'border-purple-200',  'text' => 'text-purple-600',  'hover_text' => 'text-purple-700'],
                        ];
                    @endphp
                    @foreach($actions as $a)
                        @php $c = $actionColors[$a['color']]; @endphp
                        <a href="{{ $a['href'] }}"
                            class="group flex flex-col items-center justify-center gap-2.5 p-4 rounded-xl border-2 border-dashed {{ $c['border'] }} {{ $c['bg'] }}
                                   hover:{{ $c['hover_bg'] }} hover:border-solid transition-all duration-200 active:scale-[0.97]">
                            <div class="w-11 h-11 rounded-xl {{ $c['bg'] }} group-hover:{{ $c['hover_bg'] }} flex items-center justify-center transition-colors duration-200">
                                @if($a['icon'] === 'plus')
                                    <svg class="w-5 h-5 {{ $c['text'] }} group-hover:{{ $c['hover_text'] }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                @elseif($a['icon'] === 'grid')
                                    <svg class="w-5 h-5 {{ $c['text'] }} group-hover:{{ $c['hover_text'] }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                                @elseif($a['icon'] === 'briefcase')
                                    <svg class="w-5 h-5 {{ $c['text'] }} group-hover:{{ $c['hover_text'] }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5 {{ $c['text'] }} group-hover:{{ $c['hover_text'] }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                @endif
                            </div>
                            <div class="text-center">
                                <p class="text-xs font-bold {{ $c['text'] }} group-hover:{{ $c['hover_text'] }} transition-colors">{{ $a['label'] }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $a['desc'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ══ CV Library ════════════════════════════════════════ --}}
        <section class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold text-slate-900 text-base">CV của tôi</h3>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">{{ $cvs->count() }}</span>
                </div>
                <div role="tablist" aria-label="Lọc CV" class="flex items-center bg-slate-100/80 rounded-xl p-1 text-sm" id="cvFilter">
                    @php $filters = [['key'=>'all','label'=>'Tất cả'],['key'=>'completed','label'=>'Hoàn thành'],['key'=>'draft','label'=>'Nháp'],['key'=>'shared','label'=>'Đã chia sẻ']]; @endphp
                    @foreach($filters as $f)
                        <button type="button" role="tab" data-filter="{{ $f['key'] }}"
                            class="px-3.5 py-1.5 rounded-lg font-medium text-sm transition-all duration-150 cursor-pointer
                                {{ $loop->first ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-700' }}">
                            {{ $f['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if($cvs->isEmpty())
                <div class="px-6 py-20 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-blue-50 border-2 border-dashed border-blue-200 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-9 h-9 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Bắt đầu với CV đầu tiên</h4>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto mb-7">Chưa có CV nào. Chọn mẫu phù hợp và tạo CV chuyên nghiệp chỉ trong vài phút.</p>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('cv.create') }}"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-blue-900/30 transition-all active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tạo CV đầu tiên
                        </a>
                        <a href="{{ route('templates.index') }}"
                            class="inline-flex items-center gap-2 bg-white border-2 border-slate-200 hover:border-slate-300 px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-700 transition-all">
                            Khám phá template
                        </a>
                    </div>
                </div>
            @else
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="cvGrid">
                    @foreach($cvs as $cv)
                        @php
                            $isDraft  = $cv->is_draft;
                            $isShared = !empty($cv->share_url);
                            $filter   = $isDraft ? 'draft' : ($isShared ? 'shared' : 'completed');
                            $barColor = match(true) {
                                $cv->completion >= 80 => 'bg-emerald-500',
                                $cv->completion >= 40 => 'bg-amber-500',
                                default => 'bg-rose-400',
                            };
                            $statusMap = [
                                'completed' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Hoàn thành'],
                                'draft'     => ['bg-amber-50 text-amber-700 border-amber-200',     'Nháp'],
                                'shared'    => ['bg-blue-50 text-blue-700 border-blue-200',        'Đã chia sẻ'],
                            ];
                            [$statusClass, $statusLabel] = $statusMap[$filter];
                        @endphp
                        <article class="group relative bg-white border border-slate-100 rounded-2xl overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-200 cursor-pointer"
                            data-cv data-filter="{{ $filter }}" data-completion="{{ $cv->completion }}">

                            {{-- Thumbnail area --}}
                            <div class="relative h-44 bg-gradient-to-br from-slate-50 to-slate-100 overflow-hidden">
                                @if($cv->template && $cv->template->thumbnail)
                                    <img loading="lazy" src="{{ $cv->template->thumbnail }}" alt="{{ $cv->title }}"
                                        class="w-full h-full object-cover object-top">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif

                                {{-- Status badge --}}
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $statusClass }}">
                                        @if($filter === 'completed')
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($filter === 'draft')
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 015.656 5.656l-3 3a4 4 0 01-5.656-5.656m-2.828-2.828a4 4 0 00-5.656 5.656l3 3a4 4 0 005.656-5.656"/></svg>
                                        @endif
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                {{-- Hover overlay --}}
                                <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-200 flex items-center justify-center gap-3">
                                    <a href="{{ route('cv.edit', $cv) }}"
                                        class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-700 hover:bg-blue-500 hover:text-white transition-all duration-150 shadow-lg"
                                        title="Chỉnh sửa" onclick="event.stopPropagation()">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <a href="{{ route('cv.pdf', $cv) }}" target="_blank"
                                        class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-700 hover:bg-rose-500 hover:text-white transition-all duration-150 shadow-lg"
                                        title="Tải PDF" onclick="event.stopPropagation()">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    @if($cv->share_url)
                                        <a href="{{ $cv->share_url }}" target="_blank"
                                            class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-700 hover:bg-blue-500 hover:text-white transition-all duration-150 shadow-lg"
                                            title="Mở link chia sẻ" onclick="event.stopPropagation()">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Card body --}}
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h4 class="font-bold text-slate-900 truncate flex-1 text-sm leading-snug">{{ $cv->title }}</h4>
                                </div>
                                <p class="text-xs text-slate-400 mb-3 truncate">{{ $cv->template->name ?? 'Mẫu tự do' }}</p>

                                {{-- Completion bar --}}
                                <div class="mb-4">
                                    <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1.5">
                                        <span>Hoàn thiện</span>
                                        <span class="font-bold text-slate-700">{{ $cv->completion }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full transition-all duration-700" style="width: {{ $cv->completion }}%"></div>
                                    </div>
                                </div>

                                {{-- Footer actions --}}
                                <div class="flex items-center justify-between pt-2 border-t border-slate-50">
                                    <span class="text-[10px] text-slate-400">{{ $cv->updated_at?->diffForHumans(null, true) ?? '' }}</span>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('cv.edit', $cv) }}"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 transition">
                                            Chỉnh sửa
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                        <form action="{{ route('cv.destroy', $cv) }}" method="POST" class="inline" onsubmit="return confirm('Xóa CV &quot;{{ addslashes($cv->title) }}&quot;? Hành động này không thể hoàn tác.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="ml-2 text-slate-300 hover:text-rose-500 transition p-1 cursor-pointer"
                                                title="Xóa">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <p id="emptyCvs" class="hidden text-center text-sm text-slate-400 py-12">Không có CV nào khớp bộ lọc.</p>
            @endif
        </section>

        {{-- ══ Bottom row: Timeline + Applications + Tips ════════ --}}
        <section class="grid lg:grid-cols-5 gap-4">

            {{-- Timeline chart --}}
            <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">CV tạo 14 ngày qua</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Tổng: <strong class="text-slate-600">{{ $timeline->sum('count') }}</strong> CV</p>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-400 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg">
                        <span class="w-2 h-2 rounded-sm bg-blue-500"></span>
                        CV mới
                    </div>
                </div>
                <div class="flex items-end gap-1 h-36" id="timelineChart" role="img" aria-label="Biểu đồ CV tạo theo ngày">
                    @php $maxVal = $timeline->max('count') ?: 1; @endphp
                    @foreach($timeline as $bar)
                        @php
                            $h = max(8, ($bar->count / $maxVal) * 100);
                            $isToday = $bar->date === Carbon::now()->format('Y-m-d');
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group">
                            <span class="text-[10px] text-slate-500 font-medium opacity-0 group-hover:opacity-100 transition-opacity">{{ $bar->count }}</span>
                            <div class="w-full rounded-t-sm cursor-pointer transition-all duration-150
                                {{ $isToday ? 'bg-blue-600 shadow-lg shadow-blue-200' : 'bg-blue-200 hover:bg-blue-400' }}
                                group-hover:bg-blue-500"
                                style="height: {{ $h }}%"
                                title="{{ Carbon::parse($bar->date)->format('d/m/Y') }}: {{ $bar->count }} CV"></div>
                            <span class="text-[10px] {{ $isToday ? 'text-blue-600 font-bold' : 'text-slate-400' }}">
                                {{ Carbon::parse($bar->date)->format('d/m') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Applications --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Đơn ứng tuyển</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $applications->count() }} đơn gần đây</p>
                    </div>
                    @if($applications->isNotEmpty())
                        <a href="{{ route('my-applications.index') }}"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition flex items-center gap-1">
                            Xem tất cả
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>
                @if($applications->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 border border-dashed border-slate-200 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        </div>
                        <p class="text-sm text-slate-400">Bạn chưa ứng tuyển công việc nào.</p>
                        <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-blue-600 hover:text-blue-700 transition">
                            Tìm việc ngay
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @else
                    <ul class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                        @foreach($applications as $app)
                            @php
                                $stMap = [
                                    'pending'   => ['bg-amber-50 text-amber-700 border-amber-200', 'Chờ duyệt'],
                                    'reviewing' => ['bg-blue-50 text-blue-700 border-blue-200',  'Đang xem'],
                                    'interview' => ['bg-indigo-50 text-indigo-700 border-indigo-200', 'Phỏng vấn'],
                                    'accepted'  => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Nhận việc'],
                                    'rejected'  => ['bg-rose-50 text-rose-700 border-rose-200',  'Từ chối'],
                                ];
                                [$stClass, $stLabel] = $stMap[$app->status] ?? ['bg-slate-50 text-slate-600 border-slate-200', $app->status];
                            @endphp
                            <li class="px-5 py-3.5 hover:bg-slate-50/60 transition cursor-pointer"
                                @if($app->jobPost) onclick="window.location='{{ route('jobs.show', $app->jobPost) }}'" @endif>
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ optional($app->jobPost)->title ?? '—' }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $app->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="shrink-0 text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $stClass }}">
                                        {{ $stLabel }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>

        {{-- ══ Activity + Tips ══════════════════════════════════ --}}
        <section class="grid lg:grid-cols-3 gap-4">

            {{-- Activity feed --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Hoạt động gần đây</h3>
                        <p class="text-xs text-slate-400 mt-0.5">10 mục mới nhất</p>
                    </div>
                </div>
                @if(empty($activity))
                    <div class="px-5 py-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-dashed border-slate-200 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-sm text-slate-400">Chưa có hoạt động nào.</p>
                    </div>
                @else
                    <ol class="divide-y divide-slate-50">
                        @foreach($activity as $ev)
                            @php
                                $iconMap = [
                                    'plus'      => ['bg-blue-50 text-blue-600', 'M12 4v16m8-8H4'],
                                    'pencil'    => ['bg-sky-50 text-sky-600',  'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                    'link'      => ['bg-emerald-50 text-emerald-600', 'M13.828 10.172a4 4 0 015.656 5.656l-3 3a4 4 0 01-5.656-5.656m-2.828-2.828a4 4 0 00-5.656 5.656l3 3a4 4 0 005.656-5.656'],
                                    'briefcase' => ['bg-purple-50 text-purple-600', 'M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM8 7V5a2 2 0 012-2h4a2 2 0 012 2v2'],
                                ];
                                [$iconClass, $iconPath] = $iconMap[$ev['icon']] ?? ['bg-slate-50 text-slate-600', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'];
                            @endphp
                            <li class="flex items-start gap-3.5 px-5 py-3.5 hover:bg-slate-50/50 transition">
                                <div class="w-9 h-9 rounded-xl {{ $iconClass }} flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-800">
                                        @if($ev['type'] === 'cv_created')    Tạo CV <strong>{{ $ev['title'] }}</strong>
                                        @elseif($ev['type'] === 'cv_updated')    Cập nhật CV <strong>{{ $ev['title'] }}</strong>
                                        @elseif($ev['type'] === 'cv_shared')     Chia sẻ CV <strong>{{ $ev['title'] }}</strong>
                                        @elseif($ev['type'] === 'application_sent') Ứng tuyển <strong>{{ $ev['title'] }}</strong>
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $ev['when']?->diffForHumans() }}</p>
                                </div>
                                @if(!empty($ev['url']))
                                    <a href="{{ $ev['url'] }}"
                                        class="shrink-0 text-xs font-semibold text-blue-600 hover:text-blue-700 transition flex items-center gap-1 mt-0.5">
                                        Mở
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            {{-- Tips --}}
            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Gợi ý cho bạn</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Giúp bạn cải thiện CV</p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Pro
                    </span>
                </div>
                <ul class="space-y-3">
                    @foreach($tips as $tip)
                        @php
                            $tipIconMap = [
                                'target'   => ['bg-blue-50 text-blue-600', 'M12 2v20M2 12h20'],
                                'sparkles' => ['bg-purple-50 text-purple-600', 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                                'pencil'   => ['bg-amber-50 text-amber-600', 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                'briefcase'=> ['bg-emerald-50 text-emerald-600', 'M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM8 7V5a2 2 0 012-2h4a2 2 0 012 2v2'],
                            ];
                            [$tipClass, $tipPath] = $tipIconMap[$tip['icon']] ?? ['bg-slate-50 text-slate-600', 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
                        @endphp
                        <li class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 hover:bg-slate-50 transition duration-150">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $tipClass }} flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M{{ $tipPath }}"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 mb-1">{{ $tip['title'] }}</h4>
                                    <p class="text-xs text-slate-500 leading-relaxed">{{ $tip['body'] }}</p>
                                    <a href="{{ $tip['cta']['url'] }}"
                                        class="inline-flex items-center gap-1 mt-2 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                                        {{ $tip['cta']['label'] }}
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

    </div>

    @push('scripts')
    <script>
    (function () {
        // ── CV filter ────────────────────────────────────────────
        var filterEl = document.getElementById('cvFilter');
        var emptyEl  = document.getElementById('emptyCvs');
        var cards    = document.querySelectorAll('[data-cv]');

        if (filterEl) {
            filterEl.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-filter]');
                if (!btn) return;

                filterEl.querySelectorAll('[data-filter]').forEach(function (b) {
                    var active = b === btn;
                    b.classList.toggle('bg-white', active);
                    b.classList.toggle('text-slate-900', active);
                    b.classList.toggle('shadow-sm', active);
                    b.classList.toggle('ring-1', active);
                    b.classList.toggle('ring-slate-200', active);
                    b.classList.toggle('text-slate-500', !active);
                });

                var f = btn.dataset.filter;
                var visible = 0;
                cards.forEach(function (c) {
                    var show = f === 'all' || c.dataset.filter === f;
                    c.classList.toggle('hidden', !show);
                    if (show) visible++;
                });
                if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
            });
        }

        // ── Keyboard shortcut: N = new CV ────────────────────────
        document.addEventListener('keydown', function (e) {
            if (e.target.matches('input,textarea,select,[contenteditable]')) return;
            if (e.key.toLowerCase() === 'n' && !e.metaKey && !e.ctrlKey && !e.altKey) {
                window.location.href = '{{ route("cv.create") }}';
            }
        });
    })();
    </script>
    @endpush
</x-app-layout>
