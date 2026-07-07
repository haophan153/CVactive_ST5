@php
    use Illuminate\Support\Carbon;
    $greeting = match(true) {
        Carbon::now()->hour < 12 => 'Buổi sáng tốt lành',
        Carbon::now()->hour < 18 => 'Buổi chiều vui vẻ',
        default => 'Buổi tối tốt lành',
    };
@endphp
@extends('layouts.app')

@section('title', 'Quản lý tuyển dụng')

@section('content')
<div class="py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ── Header bar ─────────────────────────────────────── --}}
        <div class="bg-slate-900 rounded-2xl p-5 sm:p-6 shadow-xl shadow-slate-900/30 overflow-hidden relative">
            <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-blue-600/10 blur-2xl"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 rounded-full bg-emerald-600/10 blur-2xl"></div>
            <div class="relative flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400 bg-white/5 border border-white/10 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ $greeting }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Quản lý tuyển dụng</h1>
                    <p class="text-sm text-slate-400 mt-1">Theo dõi và tối ưu các tin tuyển dụng của bạn</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="liveToggle"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold border transition cursor-pointer"
                        aria-pressed="true">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="live-label text-white">Trực tiếp</span>
                    </button>
                    <a href="{{ route('hr.job-posts.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-900/40 transition active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Đăng tin mới
                    </a>
                </div>
            </div>
        </div>

        {{-- Flash ──────────────────────────────────────────────── --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)"
                class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm flex items-center gap-2 font-semibold text-emerald-800 shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Stats cards ─────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $statsCards = [
                    ['key' => 'total',        'label' => 'Tổng tin',     'value' => $stats['total'],        'color' => 'slate',    'sub' => 'đã tạo',        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['key' => 'published',    'label' => 'Đang đăng',    'value' => $stats['published'],    'color' => 'emerald',  'sub' => 'công khai',      'icon' => 'M5 13l4 4L19 7'],
                    ['key' => 'applications',  'label' => 'Hồ sơ nhận',   'value' => $stats['applications'], 'color' => 'blue',     'sub' => 'từ ứng viên',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['key' => 'views',        'label' => 'Lượt xem',     'value' => $stats['views'],        'color' => 'amber',    'sub' => 'tổng cộng',      'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                ];
                $palette = [
                    'slate'   => ['bg' => 'bg-slate-50',   'icon_bg' => 'bg-slate-900',   'text' => 'text-white'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'icon_bg' => 'bg-emerald-500', 'text' => 'text-white'],
                    'blue'    => ['bg' => 'bg-blue-50',   'icon_bg' => 'bg-blue-500',   'text' => 'text-white'],
                    'amber'   => ['bg' => 'bg-amber-50',  'icon_bg' => 'bg-amber-500',  'text' => 'text-white'],
                ];
            @endphp
            @foreach($statsCards as $c)
                @php $p = $palette[$c['color']]; @endphp
                <div class="group bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden relative">
                    <div class="absolute top-0 left-0 right-0 h-0.5 {{ $c['color'] === 'slate' ? 'bg-slate-900' : ($c['color'] === 'emerald' ? 'bg-emerald-500' : ($c['color'] === 'blue' ? 'bg-blue-500' : 'bg-amber-500')) }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $c['label'] }}</span>
                        <div class="w-9 h-9 {{ $p['icon_bg'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-4 h-4 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $c['icon'] }}"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tabular-nums" data-stat="{{ $c['key'] }}">
                        {{ $c['key'] === 'views' ? number_format($c['value']) : number_format($c['value']) }}
                    </div>
                    <p class="text-xs text-slate-400 mt-1">{{ $c['sub'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── Secondary stats + chart ─────────────────────────── --}}
        <div class="grid lg:grid-cols-5 gap-4">
            <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 p-5 shadow-sm overflow-hidden relative">
                <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Hồ sơ ứng tuyển 14 ngày qua</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Tổng: <strong class="text-slate-700">{{ $timeline->sum('count') }}</strong> đơn</p>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg font-semibold">
                        <span class="w-2 h-2 rounded-sm bg-blue-500"></span>
                        Đơn mới
                    </div>
                </div>
                <div class="flex items-end gap-1 h-36" id="applicationsChart" role="img" aria-label="Biểu đồ hồ sơ theo ngày">
                    @php $maxVal = $timeline->max('count') ?: 1; @endphp
                    @foreach($timeline as $bar)
                        @php
                            $h = max(8, ($bar->count / $maxVal) * 100);
                            $isToday = $bar->date === Carbon::now()->format('Y-m-d');
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group relative">
                            <span class="text-[10px] text-slate-500 font-medium opacity-0 group-hover:opacity-100 transition-opacity absolute -top-5">{{ $bar->count }}</span>
                            <div class="w-full rounded-t-sm cursor-pointer transition-all duration-150
                                {{ $isToday ? 'bg-blue-600 shadow-lg shadow-blue-200' : 'bg-blue-200 hover:bg-blue-400' }} group-hover:bg-blue-500"
                                style="height: {{ $h }}%"
                                title="{{ Carbon::parse($bar->date)->format('d/m/Y') }}: {{ $bar->count }} đơn"></div>
                            <span class="text-[10px] {{ $isToday ? 'text-blue-600 font-bold' : 'text-slate-400' }}">
                                {{ Carbon::parse($bar->date)->format('d/m') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                <h3 class="font-bold text-slate-900 text-base mb-4">Tổng quan nhanh</h3>
                <ul class="space-y-1 text-sm">
                    @php
                        $quickItems = [
                            ['key' => 'drafts',    'label' => 'Nháp',           'color' => 'slate',  'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                            ['key' => 'closed',   'label' => 'Đã đóng',        'color' => 'rose',    'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                            ['key' => 'hot',      'label' => 'Tin HOT',        'color' => 'amber',   'icon' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z'],
                            ['key' => 'remote',   'label' => 'Cho phép remote', 'color' => 'emerald', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['key' => 'expiring', 'label' => 'Sắp hết hạn (7 ngày)', 'color' => 'rose', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ];
                        $qPalette = [
                            'slate'   => 'text-slate-600',
                            'rose'    => 'text-rose-600',
                            'amber'   => 'text-amber-600',
                            'emerald' => 'text-emerald-600',
                        ];
                    @endphp
                    @foreach($quickItems as $item)
                        <li class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
                            <span class="text-slate-600 font-medium text-sm">{{ $item['label'] }}</span>
                            <strong class="tabular-nums font-black text-slate-900" data-stat="{{ $item['key'] }}">{{ number_format($stats[$item['key']] ?? 0) }}</strong>
                        </li>
                    @endforeach
                </ul>
                @if(($stats['expiring'] ?? 0) > 0)
                    <a href="{{ route('hr.job-posts.index', array_merge(request()->query(), ['sort' => 'oldest', 'status' => 'published'])) }}"
                        class="mt-4 flex items-center justify-center gap-2 text-xs font-bold px-3 py-2.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 transition border border-rose-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Xem các tin sắp hết hạn
                    </a>
                @endif
            </div>
        </div>

        {{-- ── Filters + Table ─────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
            <form method="GET" action="{{ route('hr.job-posts.index') }}" id="hrFilter"
                class="flex flex-wrap items-end gap-3 p-4 border-b border-slate-100">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Tìm kiếm</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tiêu đề, công ty, địa điểm…"
                            class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-900 placeholder-slate-400
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Trạng thái</label>
                    <select name="status" class="rounded-xl border border-slate-200 text-sm text-slate-700 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition bg-white">
                        <option value="">Tất cả</option>
                        <option value="draft"     @selected(($filters['status'] ?? '') === 'draft')>Nháp</option>
                        <option value="published" @selected(($filters['status'] ?? '') === 'published')>Đã đăng</option>
                        <option value="closed"    @selected(($filters['status'] ?? '') === 'closed')>Đã đóng</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Loại hình</label>
                    <select name="job_type" class="rounded-xl border border-slate-200 text-sm text-slate-700 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition bg-white">
                        <option value="">Tất cả</option>
                        @foreach($jobTypes as $val => $info)
                            <option value="{{ $val }}" @selected(($filters['job_type'] ?? '') === $val)>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Ngành</label>
                    <select name="category" class="rounded-xl border border-slate-200 text-sm text-slate-700 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition bg-white">
                        <option value="">Tất cả</option>
                        @foreach($categories as $val => $info)
                            <option value="{{ $val }}" @selected(($filters['category'] ?? '') === $val)>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Sắp xếp</label>
                    <select name="sort" class="rounded-xl border border-slate-200 text-sm text-slate-700 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition bg-white">
                        <option value="newest"       @selected(($filters['sort'] ?? 'newest') === 'newest')>Mới nhất</option>
                        <option value="oldest"       @selected(($filters['sort'] ?? '') === 'oldest')>Cũ nhất</option>
                        <option value="most_applied" @selected(($filters['sort'] ?? '') === 'most_applied')>Nhiều hồ sơ</option>
                        <option value="most_viewed"  @selected(($filters['sort'] ?? '') === 'most_viewed')>Nhiều lượt xem</option>
                        <option value="title_asc"     @selected(($filters['sort'] ?? '') === 'title_asc')>A → Z</option>
                    </select>
                </div>
                <div class="flex items-center gap-3 pb-0.5">
                    <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 cursor-pointer">
                        <input type="checkbox" name="is_hot" value="1" @checked(!empty($filters['is_hot']))
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"> HOT
                    </label>
                    <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 cursor-pointer">
                        <input type="checkbox" name="is_remote" value="1" @checked(!empty($filters['is_remote']))
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"> Remote
                    </label>
                </div>
                <div class="flex items-center gap-2 pb-0.5">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white rounded-xl text-sm font-bold transition shadow-sm active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-1.447.894l-2-1A1 1 0 0110 18v-4.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Lọc
                    </button>
                    @if(!empty(array_filter($filters)))
                        <a href="{{ route('hr.job-posts.index') }}" class="text-xs text-slate-500 hover:text-slate-700 font-semibold underline underline-offset-2 transition">Xóa lọc</a>
                    @endif
                </div>
            </form>

            {{-- Results ─────────────────────────────────────── --}}
            @if($jobPosts->isEmpty())
                <div class="px-6 py-20 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-blue-50 border-2 border-dashed border-blue-200 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-9 h-9 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Không tìm thấy tin tuyển nào</h4>
                    <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Thử thay đổi bộ lọc hoặc đăng tin đầu tiên của bạn.</p>
                    <a href="{{ route('hr.job-posts.create') }}"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-lg active:scale-[0.98]">
                        + Đăng tin mới
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50/80 border-b border-slate-100">
                            <tr>
                                <th class="text-left px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-slate-400">Công việc</th>
                                <th class="text-left px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-slate-400">Công ty / Địa điểm</th>
                                <th class="text-left px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-slate-400">Trạng thái</th>
                                <th class="text-center px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-slate-400">Hồ sơ</th>
                                <th class="text-left px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-slate-400">Cập nhật</th>
                                <th class="text-right px-5 py-3.5 text-[11px] font-bold uppercase tracking-widest text-slate-400">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($jobPosts as $job)
                                @php
                                    $typeInfo = $job->type_info;
                                    $catInfo  = $job->category_info;
                                    $statusMap = [
                                        'draft'     => ['bg' => 'bg-slate-100 text-slate-600 border-slate-200',   'dot' => 'bg-slate-400',   'label' => 'Nháp'],
                                        'published' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-500', 'label' => 'Đang đăng'],
                                        'closed'    => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200',     'dot' => 'bg-rose-500',   'label' => 'Đã đóng'],
                                    ];
                                    $st = $statusMap[$job->status] ?? $statusMap['draft'];
                                    $count = $job->applications_count ?? $job->applications()->count();
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition group">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-11 h-11 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                                                {{ $job->company_initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('hr.job-posts.show', $job) }}"
                                                    class="font-bold text-slate-900 hover:text-blue-600 transition truncate block text-sm leading-snug">
                                                    {{ $job->title }}
                                                </a>
                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                    @if($job->job_type)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg {{ $typeInfo['color'] ?? 'bg-slate-100 text-slate-600' }} text-[10px] font-bold">{{ $typeInfo['label'] ?? $job->job_type }}</span>
                                                    @endif
                                                    @if($job->is_remote)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-bold">Remote</span>
                                                    @endif
                                                    @if($job->is_hot)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-black">HOT</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-semibold text-slate-800 truncate max-w-[160px]">{{ $job->company_name ?: '—' }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5 truncate max-w-[160px]">{{ $job->location ?: '—' }} · {{ $catInfo['label'] ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 text-[11px] px-2.5 py-1 font-bold rounded-full border {{ $st['bg'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                            {{ $st['label'] }}
                                        </span>
                                        @if($job->expires_at && $job->status === 'published' && $job->expires_at->isFuture() && $job->expires_at->diffInDays(now()) <= 7)
                                            <div class="text-[10px] text-rose-500 font-bold mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Còn {{ $job->expires_at->diffInDays(now()) }} ngày
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ route('hr.job-posts.applications', $job) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition
                                                {{ $count > 0 ? 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200' : 'bg-slate-50 text-slate-400 hover:bg-slate-100 border border-slate-200' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $count }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-400 font-medium">
                                        {{ $job->updated_at?->diffForHumans(null, true) }}
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-1 opacity-60 group-hover:opacity-100 transition">
                                            <a href="{{ route('hr.job-posts.show', $job) }}"
                                                class="p-2 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-blue-600 transition"
                                                title="Xem chi tiết">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('hr.job-posts.edit', $job) }}"
                                                class="p-2 rounded-xl hover:bg-blue-50 text-slate-500 hover:text-blue-600 transition"
                                                title="Sửa">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form action="{{ route('hr.job-posts.destroy', $job) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Xóa tin &quot;{{ addslashes($job->title) }}&quot;? Hành động này không thể hoàn tác.');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 rounded-xl hover:bg-rose-50 text-slate-500 hover:text-rose-600 transition cursor-pointer"
                                                    title="Xóa">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($jobPosts->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $jobPosts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var btn   = document.getElementById('liveToggle');
    var label = btn?.querySelector('.live-label');
    var timer = null;

    function stop() {
        if (timer) clearInterval(timer);
        timer = null;
        btn?.classList.remove('border-emerald-300', 'bg-emerald-50/20');
        btn?.classList.add('border-white/20');
        if (label) label.textContent = 'Tạm dừng';
        btn?.setAttribute('aria-pressed', 'false');
    }

    function start() {
        btn?.classList.add('border-emerald-300', 'bg-emerald-50/20');
        btn?.classList.remove('border-white/20');
        if (label) label.textContent = 'Trực tiếp';
        btn?.setAttribute('aria-pressed', 'true');
        timer = setInterval(refresh, 30_000);
    }

    async function refresh() {
        try {
            var res = await fetch('{{ route("hr.job-posts.heartbeat") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) return;
            var data = await res.json();
            var s = data.stats || {};
            Object.entries(s).forEach(function (entry) {
                var k = entry[0], v = entry[1];
                var el = document.querySelector('[data-stat="' + k + '"]');
                if (el && /^\d+(\.\d+)?$/.test(String(v))) {
                    var formatted = Number(v).toLocaleString('vi-VN');
                    if (el.textContent.trim() !== formatted) {
                        el.textContent = formatted;
                        el.classList.add('ring-2', 'ring-blue-200', 'rounded', 'transition');
                        setTimeout(function () { el.classList.remove('ring-2', 'ring-blue-200'); }, 800);
                    }
                }
            });
        } catch (_) {}
    }

    if (btn) {
        btn.addEventListener('click', function () { if (timer) stop(); else start(); });
        start();
    }
})();
</script>
@endpush
@endsection
