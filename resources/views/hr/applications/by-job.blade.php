@extends('layouts.app')

@section('title', 'Ung viên: ' . $jobPost->title . ' | CVactive')

@php
    $openaiConfigured = !empty(config('services.openai.key'));
    $currentSort = request('sort', 'newest');
    $sortOptions = [
        'newest'  => 'Moi nhất',
        'oldest'  => 'Cu nhất',
        'ai'      => 'Diem AI',
    ];
@endphp

@push('styles')
<style>
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

        {{-- Back + Job header --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('hr.job-posts.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-indigo-600 transition mb-3">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Quay lai danh sach tin
                    </a>
                    <h1 class="text-xl font-bold text-slate-900 truncate">{{ $jobPost->title ?? 'N/A' }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $jobPost->company_name ?? 'N/A' }}</p>
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-slate-400">
                        @if($jobPost->location)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                {{ $jobPost->location }}
                            </span>
                        @endif
                        @if($jobPost->job_type)
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $jobPost->job_type }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-wrap justify-end">
                    <div class="text-right">
                        <p class="text-[11px] text-slate-500 uppercase tracking-wider">Tong ung vien</p>
                        <p class="text-2xl font-black text-slate-900">{{ $applications->total() }}</p>
                    </div>

                    <button type="button" id="ai-score-btn"
                        data-url="{{ route('hr.job-posts.ai-score', $jobPost) }}"
                        data-openai="{{ $openaiConfigured ? '1' : '0' }}"
                        @if(!$openaiConfigured) disabled @endif
                        title="{{ $openaiConfigured ? 'Cham diem AI cho tat ca ung vien' : 'Chua cau hinh OPENAI_API_KEY' }}"
                        class="px-4 py-2 text-sm font-semibold rounded-xl transition shadow-sm flex items-center gap-2 {{ $openaiConfigured ? 'bg-indigo-600 hover:bg-indigo-700 text-white active:scale-[0.98]' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        AI cham diem
                    </button>

                    <a href="{{ route('hr.job-posts.search-cv', $jobPost) }}"
                        class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Tim kiem CV
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php $statCards = [
                ['label'=>'Cho duyet',  'count'=>$applications->where('status','pending')->count(),    'color'=>'amber',  'icon'=>'clock'],
                ['label'=>'Dang xem',  'count'=>$applications->where('status','reviewing')->count(),  'color'=>'sky',    'icon'=>'eye'],
                ['label'=>'Da duyet',   'count'=>$applications->where('status','approved')->count(),    'color'=>'emerald','icon'=>'check'],
                ['label'=>'Tu choi',     'count'=>$applications->where('status','rejected')->count(),   'color'=>'rose',   'icon'=>'x'],
            ];
            $palette = [
                'amber'  =>['bg'=>'bg-amber-50','text'=>'text-amber-600','bar'=>'bg-amber-500'],
                'sky'   =>['bg'=>'bg-sky-50','text'=>'text-sky-600','bar'=>'bg-sky-500'],
                'emerald'=>['bg'=>'bg-emerald-50','text'=>'text-emerald-600','bar'=>'bg-emerald-500'],
                'rose'  =>['bg'=>'bg-rose-50','text'=>'text-rose-600','bar'=>'bg-rose-500'],
            ]; @endphp
            @foreach($statCards as $card)
            @php $p=$palette[$card['color']]; @endphp
            <div class="bg-white rounded-2xl border border-slate-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                        <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $card['count'] }}</p>
                    </div>
                    <div class="w-10 h-10 {{ $p['bg'] }} rounded-xl flex items-center justify-center">
                        @if($card['icon']==='clock')
                            <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($card['icon']==='eye')
                            <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        @elseif($card['icon']==='check')
                            <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tim kiem</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Theo ten, email..."
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 transition bg-white">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Trang thai</label>
                    <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 bg-white">
                        <option value="all">Tat ca</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Cho duyet</option>
                        <option value="reviewing" {{ request('status')=='reviewing'?'selected':'' }}>Dang xem</option>
                        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Da duyet</option>
                        <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Tu choi</option>
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sap xep</label>
                    <select name="sort" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 bg-white">
                        @foreach($sortOptions as $key => $label)
                            <option value="{{ $key }}" {{ $currentSort===$key?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm active:scale-[0.98]">
                    Loc
                </button>
                @if(request()->anyFilled(['search','status','sort']))
                <a href="{{ route('hr.job-posts.applications', $jobPost) }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-200 transition">
                    Xoa loc
                </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
            @if($applications->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Ung vien</th>
                            <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Lien he</th>
                            <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                @php $aiSortParams = array_merge(request()->all(), ['sort' => $currentSort === 'ai' ? 'newest' : 'ai']); @endphp
                                <a href="{{ route('hr.job-posts.applications', array_merge(['jobPost'=>$jobPost->id], $aiSortParams)) }}"
                                   class="inline-flex items-center gap-1 hover:text-indigo-600 transition">
                                    Diem AI
                                    @if($currentSort === 'ai')
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Ngay nop</th>
                            <th class="px-6 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Trang thai</th>
                            <th class="px-6 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500">Thao tac</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($applications as $application)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ strtoupper(substr($application->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $application->full_name }}</p>
                                        @if($application->cv)
                                            <p class="text-[11px] text-slate-400">CV: {{ $application->cv->title ?? '#'.$application->cv->id }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700">{{ $application->email }}</p>
                                @if($application->phone)
                                    <p class="text-[11px] text-slate-400">{{ $application->phone }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @include('hr.applications._score_cell', ['application' => $application])
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-slate-700">{{ $application->applied_at->format('d/m/Y') }}</p>
                                <p class="text-[11px] text-slate-400">{{ $application->applied_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($application->status)
                                    @case('pending')
                                        <span class="inline-flex text-[11px] px-2.5 py-1 font-bold bg-amber-100 text-amber-700 rounded-full">Cho duyet</span>
                                        @break
                                    @case('reviewing')
                                        <span class="inline-flex text-[11px] px-2.5 py-1 font-bold bg-sky-100 text-sky-700 rounded-full">Dang xem</span>
                                        @break
                                    @case('approved')
                                        <span class="inline-flex text-[11px] px-2.5 py-1 font-bold bg-emerald-100 text-emerald-700 rounded-full">Da duyet</span>
                                        @break
                                    @case('rejected')
                                        <span class="inline-flex text-[11px] px-2.5 py-1 font-bold bg-rose-100 text-rose-700 rounded-full">Tu choi</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('hr.applications.show', $application) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                    Xem chi tiet
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $applications->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-16">
                <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-700">Chua co don ung tuyen nao</h3>
                <p class="text-sm text-slate-500 mt-1">Cac don se hien thi o day khi co ung vien nop ho so.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- AI Modal --}}
<div id="ai-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60" style="backdrop-filter:blur(2px)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            Xac nhan cham diem AI
        </h3>
        <p id="ai-modal-body" class="mt-3 text-sm text-slate-600">
            Ban sap cham diem AI cho tat ca ung vien cua bai dang nay (chi nhung don chua co diem). Chi phi uoc tinh: ~$0.001 / CV.
        </p>
        <div id="ai-modal-progress" class="mt-4 hidden">
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div id="ai-modal-progress-bar" class="bg-indigo-600 h-2 transition-all" style="width:0%"></div>
            </div>
            <p id="ai-modal-progress-text" class="text-xs text-slate-500 mt-2">Dang phan tich...</p>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" id="ai-modal-cancel" class="px-4 py-2 bg-slate-100 text-slate-700 font-medium text-sm rounded-xl hover:bg-slate-200 transition">
                Huy
            </button>
            <button type="button" id="ai-modal-confirm" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-sm rounded-xl hover:bg-indigo-700 transition flex items-center gap-2 shadow-sm active:scale-[0.98]">
                <span id="ai-modal-confirm-label">Bat dau</span>
                <svg id="ai-modal-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var btn = document.getElementById('ai-score-btn');
    if (!btn) return;
    var url = btn.dataset.url;
    var openaiOk = btn.dataset.openai === '1';
    var modal = document.getElementById('ai-modal');
    var modalBody = document.getElementById('ai-modal-body');
    var modalProgress = document.getElementById('ai-modal-progress');
    var modalProgressBar = document.getElementById('ai-modal-progress-bar');
    var modalProgressText = document.getElementById('ai-modal-progress-text');
    var modalConfirm = document.getElementById('ai-modal-confirm');
    var modalConfirmLabel = document.getElementById('ai-modal-confirm-label');
    var modalSpinner = document.getElementById('ai-modal-spinner');
    var modalCancel = document.getElementById('ai-modal-cancel');
    var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function hideModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    function setBusy(busy) {
        modalConfirm.disabled = busy;
        modalCancel.disabled = busy;
        modalSpinner.classList.toggle('hidden', !busy);
        modalConfirmLabel.textContent = busy ? 'Dang xu ly...' : 'Bat dau';
    }

    btn.addEventListener('click', function () {
        if (!openaiOk) {
            alert('Chua cau hinh OPENAI_API_KEY. Vui long lien he quan tri vien.');
            return;
        }
        modalBody.textContent = 'Ban sap cham diem AI cho tat ca ung vien (chi nhung don chua co diem). Chi phi uoc tinh: ~$0.001 / CV.';
        modalProgress.classList.add('hidden');
        modalProgressBar.style.width = '0%';
        setBusy(false);
        showModal();
    });

    modalCancel.addEventListener('click', hideModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) hideModal(); });

    modalConfirm.addEventListener('click', function () {
        if (!openaiOk) return;
        setBusy(true);
        modalProgress.classList.remove('hidden');
        modalProgressBar.style.width = '5%';
        modalProgressText.textContent = 'Dang gui yeu cau toi OpenAI...';

        fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: '{}'
        }).then(function (res) { return res.json().catch(function () { return {}; }); })
        .then(function (data) {
            if (!data.success) throw new Error(data.message || 'Loi');
            modalProgressBar.style.width = '100%';
            modalProgressText.textContent = 'Da cham ' + (data.scored||0) + '/' + (data.total||0) + ' ung vien. Dang tai lai...';
            setTimeout(function () { window.location.href = data.redirect || (url + '?sort=ai'); }, 800);
        }).catch(function (err) {
            setBusy(false);
            modalProgress.classList.add('hidden');
            alert('Loi khi cham diem: ' + (err.message || err));
        });
    });
})();
</script>
@endpush
@endsection
