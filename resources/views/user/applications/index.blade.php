@extends('layouts.app')

@push('styles')
<style>
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('title', 'Ho so ung tuyen | CVactive')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Page header --}}
        <div>
            <h1 class="text-2xl font-black text-slate-900">Ho so ung tuyen</h1>
            <p class="text-sm text-slate-500 mt-1">Theo doi trang thai cac don ung tuyen cua ban</p>
        </div>

        @if($applications->count() > 0)
        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-100 p-5 text-center">
                <p class="text-2xl font-black text-slate-900">{{ $applications->total() }}</p>
                <p class="text-xs text-slate-500 mt-1">Tong don</p>
            </div>
            @php
                $statCards = [
                    ['label'=>'Cho duyet','status'=>'pending','color'=>'amber'],
                    ['label'=>'Dang xem','status'=>'reviewing','color'=>'sky'],
                    ['label'=>'Da duyet','status'=>'approved','color'=>'emerald'],
                ];
                $palette = [
                    'amber'=>['bg'=>'bg-amber-50','text'=>'text-amber-600'],
                    'sky'=>['bg'=>'bg-sky-50','text'=>'text-sky-600'],
                    'emerald'=>['bg'=>'bg-emerald-50','text'=>'text-emerald-600'],
                ];
            @endphp
            @foreach($statCards as $card)
            @php $p=$palette[$card['color']]; @endphp
            <div class="bg-white rounded-2xl border border-slate-100 p-5 text-center">
                <p class="text-2xl font-black {{ $p['text'] }}">{{ $applications->where('status',$card['status'])->count() }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $card['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Application cards --}}
        <div class="space-y-4">
            @foreach($applications as $application)
            @php
                $statusMap = [
                    'pending'   =>['bg'=>'bg-amber-50 border-amber-200','text'=>'text-amber-700','label'=>'Cho duyet'],
                    'reviewing' =>['bg'=>'bg-sky-50 border-sky-200','text'=>'text-sky-700','label'=>'Dang xem'],
                    'approved'  =>['bg'=>'bg-emerald-50 border-emerald-200','text'=>'text-emerald-700','label'=>'Da duyet'],
                    'rejected'  =>['bg'=>'bg-rose-50 border-rose-200','text'=>'text-rose-700','label'=>'Tu choi'],
                ];
                $st = $statusMap[$application->status] ?? ['bg'=>'bg-slate-50 border-slate-200','text'=>'text-slate-700','label'=>$application->status];
            @endphp
            <div class="bg-white rounded-2xl border border-slate-100 p-6 hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:items-start gap-4">
                    {{-- Company logo --}}
                    <div class="shrink-0">
                        @if($application->jobPost->company_logo)
                            <img src="{{ asset('storage/' . $application->jobPost->company_logo) }}"
                                alt="{{ $application->jobPost->company_name }}"
                                class="w-12 h-12 object-contain rounded-xl bg-slate-50">
                        @else
                            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Job info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">
                                    <a href="{{ route('jobs.show', $application->jobPost) }}" class="hover:text-indigo-600 transition">
                                        {{ $application->jobPost->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-slate-500 mt-0.5">{{ $application->jobPost->company_name ?: 'Cong ty chua cap nhat' }}</p>
                                <div class="flex flex-wrap gap-3 mt-2 text-xs text-slate-400">
                                    @if($application->jobPost->location)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            {{ $application->jobPost->location }}
                                        </span>
                                    @endif
                                    @if($application->jobPost->job_type)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $application->jobPost->job_type }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <span class="inline-flex text-[11px] px-2.5 py-1 font-bold rounded-full border {{ $st['bg'] }} {{ $st['text'] }}">
                                    {{ $st['label'] }}
                                </span>
                                <p class="text-[11px] text-slate-400 whitespace-nowrap">{{ $application->applied_at->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        @if($application->cv || $application->cv_file)
                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-4 flex-wrap">
                            @if($application->cv)
                                <span class="inline-flex items-center gap-1.5 text-sm text-slate-600">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    CV: {{ $application->cv->title ?? 'Ho so #' . $application->cv->id }}
                                </span>
                            @endif
                            @if($application->cv_file)
                                <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Tai xuong CV
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div>{{ $applications->links() }}</div>
        @else
        {{-- Empty state --}}
        <div class="bg-white rounded-2xl border border-slate-100 py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h3 class="font-semibold text-slate-700">Ban chua ung tuyen cong viec nao</h3>
            <p class="text-sm text-slate-500 mt-1 mb-6 max-w-sm mx-auto">Hay tim kiem cong viec phu hop va nop ho so de theo doi trang thai tai day.</p>
            <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition shadow-sm active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tim viec ngay
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
