@php
    use Illuminate\Support\Carbon;
@endphp
@extends('layouts.app')

@section('title', 'Quản lý tin tuyển dụng')

@section('content')
<div class="py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ─── Header ─── --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Quản lý tuyển dụng</h1>
                <p class="text-sm text-gray-500 mt-1">Theo doi va toi uu cac tin tuyen dung cua ban - tat ca o mot noi.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="liveToggle"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-medium border transition"
                    aria-pressed="true">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="live-label">Trực tiếp</span>
                </button>
                <a href="{{ route('hr.job-posts.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Đăng tin mới
                </a>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
                class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ─── Stats strip ─── --}}
        @php
            $statsCards = [
                ['key' => 'total',        'label' => 'Tổng tin',      'value' => $stats['total'],        'color' => 'indigo',  'sub' => 'đã tạo',       'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['key' => 'published',    'label' => 'Đang đăng',     'value' => $stats['published'],    'color' => 'emerald', 'sub' => 'công khai',     'icon' => 'M5 13l4 4L19 7'],
                ['key' => 'applications', 'label' => 'Hồ sơ nhận',    'value' => $stats['applications'], 'color' => 'sky',     'sub' => 'từ ứng viên',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['key' => 'views',        'label' => 'Lượt xem',      'value' => $stats['views'],        'color' => 'amber',   'sub' => 'tổng cộng',     'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
            ];
            $palette = [
                'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600'],
                'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                'sky'     => ['bg' => 'bg-sky-50',     'text' => 'text-sky-600'],
                'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600'],
            ];
        @endphp
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($statsCards as $c)
                @php $p = $palette[$c['color']]; @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group"
                     data-stat="{{ $c['key'] }}">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] uppercase tracking-wider text-gray-500 font-semibold">{{ $c['label'] }}</span>
                        <div class="w-9 h-9 {{ $p['bg'] }} rounded-lg flex items-center justify-center group-hover:scale-110 transition">
                            <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $c['icon'] }}"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 tabular-nums">
                        {{ $c['key'] === 'views' ? number_format($c['value']) : number_format($c['value']) }}
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $c['sub'] }}</p>
                </div>
            @endforeach
        </section>

        {{-- ─── Secondary stats + chart ─── --}}
        <section class="grid lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">Hồ sơ ứng tuyển 14 ngày qua</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Tổng: <strong class="text-gray-700">{{ $timeline->sum('count') }}</strong> đơn</p>
                    </div>
                </div>
                <div class="flex items-end gap-1 h-32" id="applicationsChart" role="img" aria-label="Biểu đồ hồ sơ theo ngày">
                    @php $maxVal = $timeline->max('count') ?: 1; @endphp
                    @foreach($timeline as $bar)
                        @php
                            $h = max(6, ($bar->count / $maxVal) * 100);
                            $isToday = $bar->date === Carbon::now()->format('Y-m-d');
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group relative">
                            <span class="text-[10px] text-gray-500 font-medium opacity-0 group-hover:opacity-100 transition absolute -top-5">{{ $bar->count }}</span>
                            <div class="w-full rounded-t {{ $isToday ? 'bg-indigo-600' : 'bg-sky-400' }} hover:bg-indigo-700 transition"
                                style="height: {{ $h }}%"></div>
                            <span class="text-[10px] {{ $isToday ? 'text-indigo-700 font-bold' : 'text-gray-400' }}">{{ Carbon::parse($bar->date)->format('d/m') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Tổng quan nhanh</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-600">📝 Nháp</span>
                        <strong class="text-gray-900 tabular-nums" data-stat="drafts">{{ number_format($stats['drafts']) }}</strong>
                    </li>
                    <li class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-600">🔒 Đã đóng</span>
                        <strong class="text-gray-900 tabular-nums" data-stat="closed">{{ number_format($stats['closed']) }}</strong>
                    </li>
                    <li class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-600">🔥 Tin HOT</span>
                        <strong class="text-gray-900 tabular-nums" data-stat="hot">{{ number_format($stats['hot']) }}</strong>
                    </li>
                    <li class="flex items-center justify-between py-2 border-b border-gray-50">
                        <span class="text-gray-600">🌍 Cho phép remote</span>
                        <strong class="text-gray-900 tabular-nums" data-stat="remote">{{ number_format($stats['remote']) }}</strong>
                    </li>
                    <li class="flex items-center justify-between py-2">
                        <span class="text-gray-600">⏰ Sắp hết hạn (7 ngày)</span>
                        <strong class="text-rose-600 tabular-nums" data-stat="expiring">{{ number_format($stats['expiring']) }}</strong>
                    </li>
                </ul>
                @if($stats['expiring'] > 0)
                    <a href="{{ route('hr.job-posts.index', array_merge(request()->query(), ['sort' => 'oldest', 'status' => 'published'])) }}"
                        class="mt-4 block text-center text-xs font-semibold px-3 py-2 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 transition">
                        Xem các tin sắp hết hạn →
                    </a>
                @endif
            </div>
        </section>

        {{-- ─── Filters bar ─── --}}
        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <form method="GET" action="{{ route('hr.job-posts.index') }}" id="hrFilter"
                class="flex flex-wrap items-end gap-3 p-4 border-b border-gray-100">
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-[11px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tiêu đề, công ty, địa điểm…"
                            class="w-full pl-9 pr-3 py-2 rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Trạng thái</label>
                    <select name="status" class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tất cả</option>
                        <option value="draft"     @selected(($filters['status'] ?? '') === 'draft')>Nháp</option>
                        <option value="published" @selected(($filters['status'] ?? '') === 'published')>Đã đăng</option>
                        <option value="closed"    @selected(($filters['status'] ?? '') === 'closed')>Đã đóng</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Loại hình</label>
                    <select name="job_type" class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tất cả</option>
                        @foreach($jobTypes as $val => $info)
                            <option value="{{ $val }}" @selected(($filters['job_type'] ?? '') === $val)>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Ngành</label>
                    <select name="category" class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tất cả</option>
                        @foreach($categories as $val => $info)
                            <option value="{{ $val }}" @selected(($filters['category'] ?? '') === $val)>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="inline-flex items-center gap-1.5 text-xs text-gray-700">
                        <input type="checkbox" name="is_hot" value="1" @checked(!empty($filters['is_hot']))
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"> HOT
                    </label>
                    <label class="inline-flex items-center gap-1.5 text-xs text-gray-700">
                        <input type="checkbox" name="is_remote" value="1" @checked(!empty($filters['is_remote']))
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"> Remote
                    </label>
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-wider text-gray-500 font-semibold mb-1">Sắp xếp</label>
                    <select name="sort" class="rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="newest"        @selected(($filters['sort'] ?? 'newest') === 'newest')>Mới nhất</option>
                        <option value="oldest"        @selected(($filters['sort'] ?? '') === 'oldest')>Cũ nhất</option>
                        <option value="most_applied"  @selected(($filters['sort'] ?? '') === 'most_applied')>Nhiều hồ sơ</option>
                        <option value="most_viewed"    @selected(($filters['sort'] ?? '') === 'most_viewed')>Nhiều lượt xem</option>
                        <option value="title_asc"      @selected(($filters['sort'] ?? '') === 'title_asc')>A → Z</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg text-sm font-semibold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-1.447.894l-2-1A1 1 0 0110 18v-4.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Lọc
                    </button>
                    @if(!empty(array_filter($filters)))
                        <a href="{{ route('hr.job-posts.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Xóa lọc</a>
                    @endif
                </div>
            </form>

            {{-- ─── Results table / cards ─── --}}
            <div class="overflow-x-auto">
                @if($jobPosts->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-indigo-50 flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                        </div>
                        <h4 class="text-base font-semibold text-gray-900">Không tìm thấy tin tuyển nào</h4>
                        <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">Thử thay đổi bộ lọc hoặc đăng tin đầu tiên của bạn.</p>
                        <a href="{{ route('hr.job-posts.create') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">
                            + Đăng tin mới
                        </a>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/70">
                            <tr>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Công việc</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Công ty / Địa điểm</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Trạng thái</th>
                                <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-500">Hồ sơ</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Cập nhật</th>
                                <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-500">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @foreach($jobPosts as $job)
                                @php
                                    $typeInfo = $job->type_info;
                                    $expInfo  = $job->experience_info;
                                    $catInfo  = $job->category_info;
                                    $statusMap = [
                                        'draft'     => ['bg' => 'bg-gray-100 text-gray-700',   'dot' => 'bg-gray-400',   'label' => 'Nháp'],
                                        'published' => ['bg' => 'bg-emerald-100 text-emerald-700','dot' => 'bg-emerald-500','label' => 'Đang đăng'],
                                        'closed'    => ['bg' => 'bg-rose-100 text-rose-700',    'dot' => 'bg-rose-500',   'label' => 'Đã đóng'],
                                    ];
                                    $st = $statusMap[$job->status] ?? $statusMap['draft'];
                                @endphp
                                <tr class="hover:bg-gray-50/60 transition group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                {{ $job->company_initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('hr.job-posts.show', $job) }}" class="font-semibold text-gray-900 hover:text-indigo-600 transition truncate block">
                                                    {{ $job->title }}
                                                </a>
                                                <div class="flex items-center gap-2 mt-1 text-[11px] text-gray-500">
                                                    @if($job->job_type)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded {{ $typeInfo['color'] }} text-[10px] font-semibold">{{ $typeInfo['label'] }}</span>
                                                    @endif
                                                    @if($job->is_remote)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-semibold">Remote</span>
                                                    @endif
                                                    @if($job->is_hot)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-bold">🔥 HOT</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-medium text-gray-800 truncate">{{ $job->company_name ?: '—' }}</div>
                                        <div class="text-xs text-gray-400 truncate">{{ $job->location ?: '—' }} · {{ $catInfo['label'] ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $st['bg'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                            {{ $st['label'] }}
                                        </span>
                                        @if($job->expires_at && $job->status === 'published' && $job->expires_at->isFuture() && $job->expires_at->diffInDays(now()) <= 7)
                                            <div class="text-[10px] text-rose-500 mt-1">Còn {{ $job->expires_at->diffInDays(now()) }} ngày</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @php $count = $job->applications_count ?? $job->applications()->count(); @endphp
                                        <a href="{{ route('hr.job-posts.applications', $job) }}"
                                            class="inline-flex items-center gap-1 {{ $count > 0 ? 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' : 'bg-gray-50 text-gray-500 hover:bg-gray-100' }} px-2.5 py-1 rounded-full text-xs font-semibold transition">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $count }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $job->updated_at?->diffForHumans() }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center gap-1 opacity-70 group-hover:opacity-100 transition">
                                            <a href="{{ route('hr.job-posts.show', $job) }}" class="p-1.5 rounded-md hover:bg-indigo-50 text-indigo-600" title="Xem">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('hr.job-posts.edit', $job) }}" class="p-1.5 rounded-md hover:bg-sky-50 text-sky-600" title="Sửa">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form action="{{ route('hr.job-posts.destroy', $job) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Xóa tin &quot;{{ $job->title }}&quot;?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-md hover:bg-rose-50 text-rose-600" title="Xóa">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($jobPosts->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $jobPosts->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

@push('scripts')
<script>
(function () {
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
            const res = await fetch('{{ route("hr.job-posts.heartbeat") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            const data = await res.json();
            const s = data.stats || {};
            Object.entries(s).forEach(([k, v]) => {
                const el = document.querySelector(`[data-stat="${k}"]`);
                if (el && /^\d+(\.\d+)?$/.test(String(v))) {
                    const formatted = Number(v).toLocaleString('vi-VN');
                    if (el.textContent.trim() !== formatted) {
                        el.textContent = formatted;
                        el.classList.add('ring-2', 'ring-indigo-200', 'rounded', 'transition');
                        setTimeout(() => el.classList.remove('ring-2', 'ring-indigo-200'), 800);
                    }
                }
            });
        } catch (_) {}
    }
    if (btn) {
        btn.addEventListener('click', () => (timer ? stop() : start()));
        start();
    }
})();
</script>
@endpush
@endsection
