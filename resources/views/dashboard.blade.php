@php
    use Illuminate\Support\Carbon;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Xin chào, {{ auth()->user()->name }} 👋
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ Carbon::now()->translatedFormat('l, d/m/Y') }} — Chúc bạn một ngày tốt lành.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="liveToggle"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border transition"
                    aria-pressed="true">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="live-label">Trực tiếp</span>
                </button>
                <a href="{{ route('cv.create') }}"
                    class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tạo CV mới
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ───── Stats Strip ───── --}}
            <section aria-label="Tổng quan"
                class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $statCards = [
                        ['key'=>'total',        'label'=>'Tổng CV',     'value'=>$stats['total'],        'icon'=>'document',     'color'=>'indigo',  'sub'=>'đã tạo'],
                        ['key'=>'completed',    'label'=>'Hoàn thành',  'value'=>$stats['completed'],    'icon'=>'check',        'color'=>'emerald', 'sub'=>'sẵn sàng dùng'],
                        ['key'=>'drafts',       'label'=>'Đang nháp',   'value'=>$stats['drafts'],       'icon'=>'clock',        'color'=>'amber',   'sub'=>'cần hoàn thiện'],
                        ['key'=>'applications', 'label'=>'Đã ứng tuyển','value'=>$stats['applications'], 'icon'=>'briefcase',    'color'=>'sky',     'sub'=>'công việc'],
                    ];
                    $palette = [
                        'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600',  'bar' => 'bg-indigo-500'],
                        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'bar' => 'bg-emerald-500'],
                        'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'bar' => 'bg-amber-500'],
                        'sky'     => ['bg' => 'bg-sky-50',     'text' => 'text-sky-600',     'bar' => 'bg-sky-500'],
                    ];
                @endphp
                @foreach($statCards as $card)
                    @php $p = $palette[$card['color']]; @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group"
                         data-stat="{{ $card['key'] }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $card['label'] }}</span>
                            <div class="w-9 h-9 {{ $p['bg'] }} rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                                @if($card['icon'] === 'document')
                                    <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @elseif($card['icon'] === 'check')
                                    <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7"/></svg>
                                @elseif($card['icon'] === 'clock')
                                    <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                @endif
                            </div>
                        </div>
                        <div class="text-3xl font-extrabold text-gray-900 tabular-nums">{{ number_format($card['value']) }}</div>
                        <p class="text-xs text-gray-400 mt-1">{{ $card['sub'] }}</p>
                        <div class="mt-3 h-1 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $p['bar'] }} rounded-full transition-all duration-700"
                                 style="width: {{ $stats['total'] > 0 ? min(100, $card['value'] * 100 / max(1, $stats['total'])) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- ───── Quick Actions + Completion ───── --}}
            <section class="grid lg:grid-cols-3 gap-4">
                {{-- Overall completion --}}
                <div class="lg:col-span-1 bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden">
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative">
                        <div class="text-xs uppercase tracking-widest text-indigo-100 mb-2 font-semibold">Mức độ hoàn thiện CV</div>
                        <div class="flex items-end gap-2">
                            <span class="text-5xl font-extrabold">{{ $completion }}</span>
                            <span class="text-2xl font-bold mb-1">%</span>
                        </div>
                        <div class="mt-3 h-2 w-full bg-white/20 rounded-full overflow-hidden">
                            <div id="completionBar" class="h-full bg-white rounded-full transition-all duration-700" style="width: {{ $completion }}%"></div>
                        </div>
                        <p class="mt-3 text-sm text-indigo-100">
                            @if($completion < 40)
                                Hãy bổ sung thông tin để CV thu hút hơn 📈
                            @elseif($completion < 80)
                                Tốt rồi! Thêm vài chi tiết nữa nhé ✨
                            @else
                                Tuyệt vời — CV của bạn đã sẵn sàng! 🚀
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Hành động nhanh</h3>
                        <span class="text-xs text-gray-400">Phím tắt: <kbd class="px-1.5 py-0.5 bg-gray-100 rounded text-gray-600 font-mono text-[10px]">N</kbd> tạo mới</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <a href="{{ route('cv.create') }}" data-shortcut="new-cv"
                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100 hover:border-indigo-300 transition text-indigo-700">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span class="text-xs font-semibold">CV mới</span>
                        </a>
                        <a href="{{ route('templates.index') }}"
                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-violet-200 hover:bg-violet-50 transition text-gray-700">
                            <svg class="w-6 h-6 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                            <span class="text-xs font-semibold">Templates</span>
                        </a>
                        <a href="{{ route('jobs.index') }}"
                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-sky-200 hover:bg-sky-50 transition text-gray-700">
                            <svg class="w-6 h-6 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-xs font-semibold">Việc làm</span>
                        </a>
                        <a href="{{ route('profile.edit') }}"
                            class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50 transition text-gray-700">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs font-semibold">Hồ sơ</span>
                        </a>
                    </div>
                </div>
            </section>

            {{-- ───── CV Library: tabs + grid ───── --}}
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-gray-900">CV của tôi</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600" id="cvCount">{{ $cvs->count() }}</span>
                    </div>
                    <div role="tablist" aria-label="Lọc CV" class="flex items-center bg-gray-100/80 rounded-lg p-1 text-sm" id="cvFilter">
                        <button type="button" role="tab" data-filter="all" aria-selected="true"
                            class="px-3 py-1.5 rounded-md font-medium transition bg-white text-indigo-700 shadow-sm">Tất cả</button>
                        <button type="button" role="tab" data-filter="completed" aria-selected="false"
                            class="px-3 py-1.5 rounded-md font-medium transition text-gray-600 hover:text-gray-900">Hoàn thành</button>
                        <button type="button" role="tab" data-filter="draft" aria-selected="false"
                            class="px-3 py-1.5 rounded-md font-medium transition text-gray-600 hover:text-gray-900">Nháp</button>
                        <button type="button" role="tab" data-filter="shared" aria-selected="false"
                            class="px-3 py-1.5 rounded-md font-medium transition text-gray-600 hover:text-gray-900">Đã chia sẻ</button>
                    </div>
                </div>

                @if($cvs->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 flex items-center justify-center mx-auto mb-5">
                            <svg class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Bắt đầu hành trình CV của bạn</h4>
                        <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">Chưa có CV nào. Chọn mẫu phù hợp và tạo CV chuyên nghiệp chỉ trong vài phút.</p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('cv.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow">
                                Tạo CV đầu tiên
                            </a>
                            <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-indigo-300 px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-700">
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
                            @endphp
                            <article class="group relative bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300"
                                data-cv data-filter="{{ $filter }}" data-completion="{{ $cv->completion }}">

                                {{-- Thumbnail --}}
                                <div class="relative h-40 bg-gray-50 overflow-hidden">
                                    @if($cv->template && $cv->template->thumbnail)
                                        <img loading="lazy" src="{{ $cv->template->thumbnail }}" alt="{{ $cv->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-violet-50">
                                            <svg class="w-14 h-14 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif

                                    {{-- Status badge --}}
                                    <div class="absolute top-2.5 left-2.5">
                                        @if($isDraft)
                                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-[11px] px-2 py-0.5 rounded-full font-semibold">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> Nháp
                                            </span>
                                        @elseif($isShared)
                                            <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-[11px] px-2 py-0.5 rounded-full font-semibold">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span> Đã chia sẻ
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-[11px] px-2 py-0.5 rounded-full font-semibold">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Hoàn thành
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Quick actions overlay --}}
                                    <div class="absolute inset-0 bg-slate-900/55 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <a href="{{ route('cv.edit', $cv) }}" class="bg-white text-slate-700 p-2 rounded-full hover:bg-indigo-600 hover:text-white transition" title="Chỉnh sửa" aria-label="Chỉnh sửa {{ $cv->title }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <a href="{{ route('cv.pdf', $cv) }}" target="_blank" class="bg-white text-slate-700 p-2 rounded-full hover:bg-rose-600 hover:text-white transition" title="Tải PDF" aria-label="Tải PDF {{ $cv->title }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                        @if($cv->share_url)
                                            <a href="{{ $cv->share_url }}" target="_blank" class="bg-white text-slate-700 p-2 rounded-full hover:bg-blue-600 hover:text-white transition" title="Mở link chia sẻ" aria-label="Mở link chia sẻ {{ $cv->title }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                {{-- Body --}}
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="font-semibold text-gray-900 truncate flex-1" title="{{ $cv->title }}">{{ $cv->title }}</h4>
                                        <span class="text-[11px] text-gray-400 whitespace-nowrap">{{ $cv->updated_at?->diffForHumans(null, true) ?? '' }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 truncate">{{ $cv->template->name ?? 'Mẫu tự do' }}</p>

                                    {{-- Mini progress --}}
                                    <div class="mt-3">
                                        <div class="flex items-center justify-between text-[11px] text-gray-500 mb-1">
                                            <span>Hoàn thiện</span>
                                            <span class="font-semibold text-gray-700">{{ $cv->completion }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                            @php
                                                $barColor = $cv->completion >= 80 ? 'bg-emerald-500' : ($cv->completion >= 40 ? 'bg-amber-500' : 'bg-rose-500');
                                            @endphp
                                            <div class="h-full {{ $barColor }} rounded-full transition-all" style="width: {{ $cv->completion }}%"></div>
                                        </div>
                                    </div>

                                    {{-- Bottom row --}}
                                    <div class="mt-4 flex items-center justify-between">
                                        <a href="{{ route('cv.edit', $cv) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            Chỉnh sửa
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                        <form action="{{ route('cv.destroy', $cv) }}" method="POST" onsubmit="return confirm('Xóa CV &quot;{{ $cv->title }}&quot;? Hành động này không thể hoàn tác.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-rose-500 transition p-1" aria-label="Xóa {{ $cv->title }}" title="Xóa">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <p id="emptyCvs" class="hidden text-center text-sm text-gray-400 py-10">Không có CV nào khớp bộ lọc.</p>
                @endif
            </section>

            {{-- ───── Activity + Tips + 14-day chart ───── --}}
            <section class="grid lg:grid-cols-3 gap-4">
                {{-- Activity feed --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Hoạt động gần đây</h3>
                        <span class="text-xs text-gray-400">10 mục mới nhất</span>
                    </div>
                    @if(empty($activity))
                        <p class="px-5 py-8 text-sm text-gray-400">Chưa có hoạt động nào.</p>
                    @else
                        <ol class="divide-y divide-gray-50">
                            @foreach($activity as $ev)
                                @php
                                    $iconWrap = [
                                        'indigo'  => 'bg-indigo-100 text-indigo-600',
                                        'sky'     => 'bg-sky-100 text-sky-600',
                                        'emerald' => 'bg-emerald-100 text-emerald-600',
                                        'amber'   => 'bg-amber-100 text-amber-600',
                                    ][$ev['color']] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <li class="flex items-start gap-3 px-5 py-3.5">
                                    <div class="w-9 h-9 rounded-full {{ $iconWrap }} flex items-center justify-center flex-shrink-0">
                                        @if($ev['icon'] === 'plus')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        @elseif($ev['icon'] === 'pencil')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        @elseif($ev['icon'] === 'link')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 015.656 5.656l-3 3a4 4 0 01-5.656-5.656m-2.828-2.828a4 4 0 00-5.656 5.656l3 3a4 4 0 005.656-5.656"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-800">
                                            @if($ev['type'] === 'cv_created')    Tạo CV <strong>{{ $ev['title'] }}</strong>
                                            @elseif($ev['type'] === 'cv_updated')    Cập nhật CV <strong>{{ $ev['title'] }}</strong>
                                            @elseif($ev['type'] === 'cv_shared')     Chia sẻ CV <strong>{{ $ev['title'] }}</strong>
                                            @elseif($ev['type'] === 'application_sent') Ứng tuyển <strong>{{ $ev['title'] }}</strong>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $ev['when']?->diffForHumans() }}</p>
                                    </div>
                                    @if(!empty($ev['url']))
                                        <a href="{{ $ev['url'] }}" class="text-xs text-indigo-600 hover:underline flex-shrink-0" target="_blank" rel="noopener">Mở →</a>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>

                {{-- Pro tips --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Gợi ý cho bạn</h3>
                        <span class="text-[10px] uppercase tracking-wider bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">Pro</span>
                    </div>
                    <ul class="space-y-3">
                        @foreach($tips as $tip)
                            <li class="rounded-xl border border-amber-100 bg-amber-50/40 p-4 hover:bg-amber-50 transition">
                                <div class="flex items-start gap-2">
                                    <div class="w-7 h-7 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                                        @if($tip['icon'] === 'target')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v20M2 12h20"/></svg>
                                        @elseif($tip['icon'] === 'sparkles')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                        @elseif($tip['icon'] === 'link')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 015.656 5.656l-3 3a4 4 0 01-5.656-5.656"/></svg>
                                        @elseif($tip['icon'] === 'briefcase')
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/></svg>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $tip['title'] }}</h4>
                                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $tip['body'] }}</p>
                                        <a href="{{ $tip['cta']['url'] }}" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-amber-700 hover:text-amber-900">
                                            {{ $tip['cta']['label'] }}
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            {{-- ───── 14-day chart + applications ───── --}}
            <section class="grid lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">CV tạo 14 ngày qua</h3>
                        <span class="text-xs text-gray-400">Tổng: <strong class="text-gray-700">{{ $timeline->sum('count') }}</strong></span>
                    </div>
                    <div class="flex items-end gap-1 h-32" id="timelineChart" role="img" aria-label="Biểu đồ CV tạo theo ngày">
                        @php $maxVal = $timeline->max('count') ?: 1; @endphp
                        @foreach($timeline as $bar)
                            @php
                                $h = max(6, ($bar->count / $maxVal) * 100);
                                $isToday = $bar->date === Carbon::now()->format('Y-m-d');
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1 group relative">
                                <span class="text-[10px] text-gray-500 font-medium opacity-0 group-hover:opacity-100 transition">{{ $bar->count }}</span>
                                <div class="w-full rounded-t {{ $isToday ? 'bg-indigo-600' : 'bg-indigo-400' }} hover:bg-indigo-700 transition"
                                    style="height: {{ $h }}%"></div>
                                <span class="text-[10px] {{ $isToday ? 'text-indigo-700 font-bold' : 'text-gray-400' }}">{{ Carbon::parse($bar->date)->format('d/m') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Đơn ứng tuyển</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-sky-100 text-sky-700 font-semibold">{{ $applications->count() }}</span>
                    </div>
                    @if($applications->isEmpty())
                        <p class="px-5 py-8 text-sm text-gray-400 text-center">Bạn chưa ứng tuyển công việc nào.</p>
                    @else
                        <ul class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                            @foreach($applications as $app)
                                @php
                                    $statusMap = [
                                        'pending' => ['bg-yellow-100 text-yellow-700', 'Chờ duyệt'],
                                        'reviewing' => ['bg-blue-100 text-blue-700', 'Đang xem'],
                                        'interview' => ['bg-violet-100 text-violet-700', 'Phỏng vấn'],
                                        'accepted' => ['bg-emerald-100 text-emerald-700', 'Nhận'],
                                        'rejected' => ['bg-rose-100 text-rose-700', 'Từ chối'],
                                    ];
                                    $st = $statusMap[$app->status] ?? ['bg-gray-100 text-gray-600', $app->status];
                                @endphp
                                <li class="px-5 py-3 hover:bg-gray-50/60 transition">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ optional($app->jobPost)->title ?? '—' }}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-xs text-gray-400">{{ $app->created_at->diffForHumans() }}</span>
                                        <span class="inline-flex text-[11px] px-2 py-0.5 rounded-full font-semibold {{ $st[0] }}">{{ $st[1] }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>

        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            // Tab filter for CV grid
            const filterEl = document.getElementById('cvFilter');
            const emptyEl  = document.getElementById('emptyCvs');
            const cards    = document.querySelectorAll('[data-cv]');
            if (filterEl) {
                filterEl.addEventListener('click', (e) => {
                    const btn = e.target.closest('[data-filter]');
                    if (!btn) return;
                    filterEl.querySelectorAll('[data-filter]').forEach((b) => {
                        const active = b === btn;
                        b.setAttribute('aria-selected', active ? 'true' : 'false');
                        b.classList.toggle('bg-white', active);
                        b.classList.toggle('text-indigo-700', active);
                        b.classList.toggle('shadow-sm', active);
                        b.classList.toggle('text-gray-600', !active);
                    });
                    const f = btn.dataset.filter;
                    let visible = 0;
                    cards.forEach((c) => {
                        const show = f === 'all' || c.dataset.filter === f;
                        c.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });
                    if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
                });
            }

            // Realtime heartbeat (toggleable)
            const btn   = document.getElementById('liveToggle');
            const label = btn?.querySelector('.live-label');
            let timer  = null;

            const stop = () => {
                if (timer) clearInterval(timer);
                timer = null;
                btn?.classList.remove('border-emerald-300', 'bg-emerald-50', 'text-emerald-700');
                btn?.classList.add('border-gray-200', 'text-gray-600');
                if (label) label.textContent = 'Tạm dừng';
                btn?.setAttribute('aria-pressed', 'false');
            };
            const start = () => {
                btn?.classList.add('border-emerald-300', 'bg-emerald-50', 'text-emerald-700');
                btn?.classList.remove('border-gray-200', 'text-gray-600');
                if (label) label.textContent = 'Trực tiếp';
                btn?.setAttribute('aria-pressed', 'true');
                timer = setInterval(refresh, 30_000);
            };
            async function refresh() {
                try {
                    const res = await fetch('{{ route("dashboard.heartbeat") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    const stats = data.stats || {};
                    const map = {
                        'total'        : document.querySelector('[data-stat="total"]        .tabular-nums'),
                        'completed'    : document.querySelector('[data-stat="completed"]    .tabular-nums'),
                        'drafts'       : document.querySelector('[data-stat="drafts"]       .tabular-nums'),
                        'applications' : document.querySelector('[data-stat="applications"] .tabular-nums'),
                    };
                    Object.entries(map).forEach(([k, el]) => {
                        if (el && stats[k] !== undefined) {
                            const v = Number(stats[k]).toLocaleString('vi-VN');
                            if (el.textContent.trim() !== v) {
                                el.textContent = v;
                                el.classList.add('ring-2', 'ring-indigo-200', 'rounded');
                                setTimeout(() => el.classList.remove('ring-2', 'ring-indigo-200', 'rounded'), 800);
                            }
                        }
                    });
                } catch (_) { /* ignore */ }
            }

            if (btn) {
                btn.addEventListener('click', () => (timer ? stop() : start()));
                start();
            }

            // Keyboard shortcut: N => new CV
            document.addEventListener('keydown', (e) => {
                if (e.target.matches('input,textarea,select,[contenteditable]')) return;
                if (e.key.toLowerCase() === 'n' && !e.metaKey && !e.ctrlKey && !e.altKey) {
                    window.location.href = '{{ route("cv.create") }}';
                }
            });
        })();
    </script>
    @endpush
</x-app-layout>
