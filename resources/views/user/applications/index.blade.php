@extends('layouts.app')

@push('styles')
<style>
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('title', 'Hồ sơ ứng tuyển | CVactive')

@section('content')
@php
    use Illuminate\Support\Carbon;
    $greeting = match(true) {
        Carbon::now()->hour < 12 => 'Buổi sáng tốt lành',
        Carbon::now()->hour < 18 => 'Buổi chiều vui vẻ',
        default => 'Buổi tối tốt lành',
    };
    $pendingCount   = $applications->where('status', 'pending')->count();
    $reviewingCount = $applications->where('status', 'reviewing')->count();
    $approvedCount   = $applications->where('status', 'approved')->count();
    $rejectedCount   = $applications->where('status', 'rejected')->count();
@endphp

<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ── Page header ─────────────────────────────────────── --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 bg-white border border-slate-200 px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ $greeting }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Hồ sơ ứng tuyển</h1>
                <p class="text-sm text-slate-500 mt-1">Theo dõi trạng thái các đơn ứng tuyển của bạn</p>
            </div>
            <a href="{{ route('jobs.index') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-lg shadow-blue-900/20 active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tìm việc mới
            </a>
        </div>

        @if($applications->count() > 0)
        {{-- ── Stats cards ─────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            @php
                $statCards = [
                    ['key' => 'total',     'label' => 'Tổng đơn',    'value' => $applications->total(),     'color' => 'slate',    'sub' => 'đã nộp'],
                    ['key' => 'pending',   'label' => 'Chờ duyệt',  'value' => $pendingCount,              'color' => 'amber',    'sub' => 'đang chờ'],
                    ['key' => 'reviewing', 'label' => 'Đang xem',   'value' => $reviewingCount,            'color' => 'blue',     'sub' => 'đang review'],
                    ['key' => 'approved',  'label' => 'Đã duyệt',   'value' => $approvedCount,              'color' => 'emerald',  'sub' => 'cv được nhận'],
                    ['key' => 'rejected',  'label' => 'Từ chối',    'value' => $rejectedCount,              'color' => 'rose',     'sub' => 'không được duyệt'],
                ];
                $palette = [
                    'slate'   => ['bg' => 'bg-slate-50',   'icon_bg' => 'bg-slate-900',   'text' => 'text-white',     'bar' => 'bg-slate-900'],
                    'amber'   => ['bg' => 'bg-amber-50',   'icon_bg' => 'bg-amber-500',   'text' => 'text-white',     'bar' => 'bg-amber-500'],
                    'blue'    => ['bg' => 'bg-blue-50',    'icon_bg' => 'bg-blue-500',    'text' => 'text-white',     'bar' => 'bg-blue-500'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'icon_bg' => 'bg-emerald-500', 'text' => 'text-white',     'bar' => 'bg-emerald-500'],
                    'rose'    => ['bg' => 'bg-rose-50',    'icon_bg' => 'bg-rose-500',    'text' => 'text-white',     'bar' => 'bg-rose-500'],
                ];
                $icons = [
                    'total'     => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'pending'   => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    'reviewing' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                    'approved'  => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'rejected'  => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                ];
            @endphp
            @foreach($statCards as $card)
                @php $p = $palette[$card['color']]; @endphp
                <div class="bg-white rounded-2xl border border-slate-100 p-4 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">{{ $card['label'] }}</span>
                        <div class="w-8 h-8 {{ $p['icon_bg'] }} rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-4 h-4 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$card['key']] }}"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-black text-slate-900 tabular-nums">{{ number_format($card['value']) }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $card['sub'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── Application cards ───────────────────────────────── --}}
        <div class="space-y-4" id="appList">
            @foreach($applications as $application)
            @php
                $stMap = [
                    'pending'   => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Chờ duyệt'],
                    'reviewing'=> ['bg' => 'bg-blue-50 border-blue-200 text-blue-700',    'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'label' => 'Đang xem'],
                    'approved' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Đã duyệt'],
                    'rejected' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700',    'icon' => 'M6 18L18 6M6 6l12 12', 'label' => 'Từ chối'],
                ];
                $st = $stMap[$application->status] ?? ['bg' => 'bg-slate-50 border-slate-200 text-slate-600', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => $application->status];

                $aiScoreColor = match(true) {
                    ($application->ai_score ?? 0) >= 80 => 'text-emerald-600',
                    ($application->ai_score ?? 0) >= 50 => 'text-amber-600',
                    default => 'text-slate-400',
                };
            @endphp
            <article class="bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 group">
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                        {{-- Company logo --}}
                        <div class="shrink-0">
                            @if($application->jobPost->company_logo)
                                <img src="{{ asset('storage/' . $application->jobPost->company_logo) }}"
                                    alt="{{ $application->jobPost->company_name }}"
                                    class="w-14 h-14 object-contain rounded-xl bg-slate-50 border border-slate-100 shadow-sm">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center shadow-sm">
                                    <span class="text-lg font-black text-white">{{ Str::of($application->jobPost->company_name ?? 'C')->substr(0, 2)->upper() }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Job info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-base font-bold text-slate-900 leading-snug">
                                        <a href="{{ route('jobs.show', $application->jobPost) }}"
                                            class="hover:text-blue-600 transition">
                                            {{ $application->jobPost->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-slate-500 mt-0.5">{{ $application->jobPost->company_name ?: 'Công ty chưa cập nhật' }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-400">
                                        @if($application->jobPost->location)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                {{ $application->jobPost->location }}
                                            </span>
                                        @endif
                                        @if($application->jobPost->job_type)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $application->jobPost->job_type }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] px-3 py-1 font-bold rounded-full border {{ $st['bg'] }}">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $st['icon'] }}"/></svg>
                                        {{ $st['label'] }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $application->applied_at->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            {{-- AI Score + CV info --}}
                            @if($application->cv || $application->cv_file || $application->ai_score)
                            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-4">
                                @if($application->ai_score)
                                    <div class="flex items-center gap-2">
                                        <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center">
                                            <span class="text-xs font-black text-white">{{ $application->ai_score }}</span>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">AI Score</p>
                                            <p class="text-xs font-bold {{ $aiScoreColor }}">{{ $application->ai_score_label }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($application->cv)
                                    <span class="inline-flex items-center gap-1.5 text-sm text-slate-600">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        {{ $application->cv->title ?? 'Hồ sơ #' . $application->cv->id }}
                                    </span>
                                @endif

                                @if($application->cv_file)
                                    <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 hover:text-blue-700 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Tải xuống CV
                                    </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        @if($applications->hasPages())
            <div class="flex justify-center pt-2">{{ $applications->links() }}</div>
        @endif

        @else
        {{-- ── Empty state ─────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-100 py-20 text-center shadow-sm">
            <div class="w-24 h-24 rounded-2xl bg-blue-50 border-2 border-dashed border-blue-200 flex items-center justify-center mx-auto mb-6">
                <svg class="w-11 h-11 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Bạn chưa ứng tuyển công việc nào</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto mb-7">Tìm kiếm công việc phù hợp và nộp hồ sơ để theo dõi trạng thái tại đây.</p>
            <a href="{{ route('jobs.index') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-lg shadow-blue-900/20 active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tìm việc ngay
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
