@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Tổng quan hệ thống')

@php
$cards = [
    ['label' => 'Người dùng',        'value' => number_format($stats['users']),              'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'blue',    'trend' => $trends['users']      ?? null, 'href' => 'admin.users.index'],
    ['label' => 'CV đã tạo',         'value' => number_format($stats['cvs']),               'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'violet', 'trend' => $trends['cvs']        ?? null],
    ['label' => 'Tin tuyển dụng',    'value' => number_format($stats['job_posts']),         'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'emerald','trend' => $trends['job_posts']  ?? null, 'href' => 'admin.job-posts.index'],
    ['label' => 'Templates',         'value' => number_format($stats['templates']),         'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z', 'color' => 'purple',  'sub' => 'đang hoạt động'],
    ['label' => 'Doanh thu',         'value' => number_format($stats['revenue'], 0, ',', '.') . '₫', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color' => 'green',  'trend' => $trends['revenue']    ?? null, 'href' => 'admin.payments.index'],
    ['label' => 'Thanh toán chờ',    'value' => number_format($stats['pending_payments']),  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber',   'sub' => 'cần xử lý'],
];

$colorMap = [
    'blue'    => ['bg' => 'bg-blue-50',       'text' => 'text-blue-600',    'border' => 'border-blue-100',      'grad' => 'from-blue-500 to-blue-600'],
    'violet'  => ['bg' => 'bg-violet-50',     'text' => 'text-violet-600',   'border' => 'border-violet-100',    'grad' => 'from-violet-500 to-violet-600'],
    'emerald' => ['bg' => 'bg-emerald-50',    'text' => 'text-emerald-600',  'border' => 'border-emerald-100',   'grad' => 'from-emerald-500 to-emerald-600'],
    'purple'  => ['bg' => 'bg-purple-50',     'text' => 'text-purple-600',   'border' => 'border-purple-100',    'grad' => 'from-purple-500 to-purple-600'],
    'green'   => ['bg' => 'bg-emerald-50',    'text' => 'text-emerald-600',  'border' => 'border-emerald-100',   'grad' => 'from-emerald-500 to-green-600'],
    'amber'   => ['bg' => 'bg-amber-50',      'text' => 'text-amber-600',    'border' => 'border-amber-100',    'grad' => 'from-amber-500 to-amber-600'],
];
@endphp

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @foreach($cards as $card)
    @php $c = $colorMap[$card['color']]; @endphp
    <a href="{{ isset($card['href']) ? route($card['href']) : '#' }}"
       class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-lg hover:border-indigo-200/60 transition-all duration-200 p-5 block">
        <div class="flex items-start justify-between mb-4">
            <span class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</span>
            <div class="w-10 h-10 {{ $c['bg'] }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $card['value'] }}</div>
        <div class="mt-2 flex items-center gap-1.5 text-xs">
            @if(!empty($card['trend']))
                @if($card['trend']['dir'] === 'up')
                    <span class="inline-flex items-center gap-0.5 text-emerald-600 font-bold">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        +{{ $card['trend']['pct'] }}%
                    </span>
                    <span class="text-slate-400">30 ngày</span>
                @elseif($card['trend']['dir'] === 'down')
                    <span class="inline-flex items-center gap-0.5 text-red-500 font-bold">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V3"/></svg>
                        -{{ $card['trend']['pct'] }}%
                    </span>
                    <span class="text-slate-400">30 ngày</span>
                @else
                    <span class="text-slate-400">→ 0%</span>
                @endif
            @else
                <span class="text-slate-400 font-medium">{{ $card['sub'] ?? '' }}</span>
            @endif
        </div>
    </a>
    @endforeach
</div>

{{-- Quick actions --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.users.index') }}" class="group bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-2xl p-4 flex items-center gap-4 hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-200">
        <span class="w-12 h-12 bg-white/15 backdrop-blur rounded-xl flex items-center justify-center group-hover:bg-white/25 transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/></svg>
        </span>
        <div>
            <p class="font-bold text-sm">Quản lý Users</p>
            <p class="text-xs text-blue-100 mt-0.5">{{ $stats['users'] }} tài khoản</p>
        </div>
    </a>
    <a href="{{ route('admin.templates.index') }}" class="group bg-gradient-to-br from-violet-600 to-violet-700 text-white rounded-2xl p-4 flex items-center gap-4 hover:shadow-xl hover:shadow-violet-500/20 transition-all duration-200">
        <span class="w-12 h-12 bg-white/15 backdrop-blur rounded-xl flex items-center justify-center group-hover:bg-white/25 transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/></svg>
        </span>
        <div>
            <p class="font-bold text-sm">Templates</p>
            <p class="text-xs text-violet-100 mt-0.5">{{ $stats['templates'] }} đang hoạt động</p>
        </div>
    </a>
    <a href="{{ route('admin.blog.index') }}" class="group bg-gradient-to-br from-rose-600 to-rose-700 text-white rounded-2xl p-4 flex items-center gap-4 hover:shadow-xl hover:shadow-rose-500/20 transition-all duration-200">
        <span class="w-12 h-12 bg-white/15 backdrop-blur rounded-xl flex items-center justify-center group-hover:bg-white/25 transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1"/></svg>
        </span>
        <div>
            <p class="font-bold text-sm">Blog</p>
            <p class="text-xs text-rose-100 mt-0.5">Quản lý bài viết</p>
        </div>
    </a>
    <a href="{{ route('admin.contacts.index') }}" class="group bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl p-4 flex items-center gap-4 hover:shadow-xl hover:shadow-amber-500/20 transition-all duration-200 relative">
        <span class="w-12 h-12 bg-white/15 backdrop-blur rounded-xl flex items-center justify-center group-hover:bg-white/25 transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
        </span>
        @if($unreadContacts > 0)
        <span class="absolute top-2 right-2 w-5 h-5 bg-white text-amber-600 rounded-full flex items-center justify-center text-[10px] font-extrabold shadow">{{ $unreadContacts }}</span>
        @endif
        <div>
            <p class="font-bold text-sm">Hộp thư</p>
            <p class="text-xs text-amber-100 mt-0.5">{{ $unreadContacts }} liên hệ chưa đọc</p>
        </div>
    </a>
</div>

{{-- Charts row --}}
<div class="grid lg:grid-cols-3 gap-5 mb-6">

    {{-- Revenue 6 tháng --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-slate-900">Doanh thu 6 tháng gần nhất</h3>
                <p class="text-xs text-slate-400 mt-1">Tổng: <strong class="text-emerald-600">{{ number_format($stats['revenue'], 0, ',', '.') }}₫</strong></p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-indigo-500"></span>Hiện tại</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-400"></span>Tháng trước</span>
            </div>
        </div>
        @php $maxRev = $revenueByMonth->max('total') ?: 1; @endphp
        <div class="flex items-end space-x-4 h-48">
            @foreach($revenueByMonth as $month)
                @php
                    $h = $maxRev > 0 ? max(8, ($month->total / $maxRev) * 100) : 8;
                    $isCurrent = $month->month === now()->format('Y-m');
                @endphp
                <div class="flex-1 flex flex-col items-center space-y-2">
                    <span class="text-xs font-bold {{ $isCurrent ? 'text-indigo-600' : 'text-slate-500' }}">{{ number_format($month->total / 1000) }}K</span>
                    <div class="w-full rounded-t-xl {{ $isCurrent ? 'bg-gradient-to-b from-indigo-500 to-indigo-600 shadow-lg shadow-indigo-500/30' : 'bg-gradient-to-b from-emerald-400 to-emerald-500' }} hover:opacity-80 transition-opacity relative"
                        style="height: {{ $h }}%">
                        @if($isCurrent)
                        <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-indigo-600 rounded-full ring-2 ring-white"></span>
                        @endif
                    </div>
                    <span class="text-xs {{ $isCurrent ? 'text-indigo-700 font-bold' : 'text-slate-400' }}">{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('m/Y') }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Plan distribution --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-slate-900">Phân bổ gói</h3>
            <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full font-medium">{{ $stats['users'] }} users</span>
        </div>
        <div class="space-y-4">
            @forelse($planStats as $stat)
            @php $pct = $stats['users'] > 0 ? round($stat->count / $stats['users'] * 100) : 0; @endphp
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-semibold text-slate-700">{{ $stat->name ?? 'Chưa có gói' }}</span>
                    <span class="text-slate-500 font-medium">{{ $stat->count }} người ({{ $pct }}%)</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-all duration-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-4">Chưa có dữ liệu.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- User growth + System health --}}
<div class="grid lg:grid-cols-3 gap-5 mb-6">

    {{-- User growth --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-slate-900">Người dùng mới 30 ngày</h3>
                @php $total30 = $usersGrowth->sum('count'); @endphp
                <p class="text-xs text-slate-400 mt-0.5">{{ $total30 }} người dùng mới trong 30 ngày</p>
            </div>
            <span class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-full">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                {{ $total30 }}
            </span>
        </div>
        @php
            $max = $usersGrowth->max('count') ?: 1;
            $w = 100 / max(1, $usersGrowth->count() - 1);
            $points = [];
            $i = 0;
            foreach ($usersGrowth as $p) {
                $x = round($i * $w, 2);
                $y = round(100 - (($p->count / $max) * 100), 2);
                $points[] = $x . ',' . $y;
                $i++;
            }
        @endphp
        @if(count($points) > 1)
        <div class="relative">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-44">
                <defs>
                    <linearGradient id="areaGrad2" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0.02"/>
                    </linearGradient>
                </defs>
                <polygon fill="url(#areaGrad2)" points="0,100 {{ implode(' ', $points) }} 100,100"/>
                <polyline fill="none" stroke="#6366f1" stroke-width="1.5" stroke-linejoin="round" points="{{ implode(' ', $points) }}"/>
                @foreach($usersGrowth as $idx => $p)
                    @php $xp = round($idx * $w, 2); $yp = round(100 - (($p->count / $max) * 100), 2); @endphp
                    <circle cx="{{ $xp }}" cy="{{ $yp }}" r="2.5" fill="#6366f1" class="opacity-60"/>
                @endforeach
            </svg>
            <div class="flex justify-between text-[11px] text-slate-400 mt-2 px-1">
                <span>{{ \Carbon\Carbon::parse($usersGrowth->first()->date)->format('d/m') }}</span>
                <span>{{ \Carbon\Carbon::parse($usersGrowth->last()->date)->format('d/m') }}</span>
            </div>
        </div>
        @else
        <p class="text-sm text-slate-400 text-center py-10">Chưa có dữ liệu.</p>
        @endif
    </div>

    {{-- System health --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                System Health
            </h3>
            @php $diskUsedPct = $systemHealth['disk_total'] > 0 ? round(($systemHealth['disk_total'] - $systemHealth['disk_free']) / $systemHealth['disk_total'] * 100) : 0; @endphp
            <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $diskUsedPct > 90 ? 'bg-red-50 text-red-600' : ($diskUsedPct > 70 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}">
                {{ $diskUsedPct }}% disk
            </span>
        </div>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-slate-600 font-medium">Disk sử dụng</span>
                    <span class="font-bold {{ $diskUsedPct > 90 ? 'text-red-500' : ($diskUsedPct > 70 ? 'text-amber-500' : 'text-emerald-500') }}">{{ $diskUsedPct }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $diskUsedPct > 90 ? 'bg-red-500' : ($diskUsedPct > 70 ? 'bg-amber-500' : 'bg-emerald-500') }} transition-all duration-500" style="width: {{ $diskUsedPct }}%"></div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Free: {{ number_format($systemHealth['disk_free'] / 1024 / 1024 / 1024, 2) }} GB</p>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                <span class="text-sm text-slate-600 font-medium flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $systemHealth['pending_jobs'] > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                    Queue jobs
                </span>
                <span class="font-bold text-slate-900">{{ number_format($systemHealth['pending_jobs']) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-slate-100">
                <span class="text-sm text-slate-600 font-medium">Cache size</span>
                <span class="font-bold text-slate-900">{{ number_format($systemHealth['cache_size'] / 1024, 1) }} KB</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-slate-600 font-medium">Error logs</span>
                <span class="font-bold text-slate-900">{{ number_format($systemHealth['logs_size'] / 1024, 1) }} KB</span>
            </div>
        </div>
    </div>
</div>

{{-- Top HR + Activity timeline --}}
<div class="grid lg:grid-cols-3 gap-5 mb-6">

    {{-- Top HR --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">Top HR đăng tuyển</h3>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Xem tất cả →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($topHr as $i => $job)
            <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 transition-colors">
                <span class="w-6 h-6 rounded-full {{ $i === 0 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-500' }} text-xs font-extrabold flex items-center justify-center flex-shrink-0">{{ $i + 1 }}</span>
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-500/20">
                    {{ strtoupper(substr($job->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ $job->user->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $job->user->email }}</p>
                </div>
                <span class="text-sm font-extrabold text-slate-900">{{ $job->total }}</span>
            </div>
            @empty
            <p class="px-5 py-8 text-sm text-slate-400 text-center">Chưa có dữ liệu.</p>
            @endforelse
        </div>
    </div>

    {{-- Activity timeline --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-900">Hoạt động gần đây</h3>
        </div>
        <div class="p-5">
            @forelse($activity as $a)
            <div class="flex gap-4 pb-4 relative">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-xl bg-{{ $a['color'] }}-50 text-{{ $a['color'] }}-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                        @if($a['icon'] === 'cash')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                        @elseif($a['icon'] === 'edit')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        @else
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @endif
                    </div>
                    @if(!$loop->last)
                    <span class="w-px flex-1 bg-slate-100 mt-2"></span>
                    @endif
                </div>
                <div class="flex-1 min-w-0 pb-2">
                    <p class="text-sm font-semibold text-slate-900">{{ $a['title'] }}</p>
                    @if($a['sub'])<p class="text-xs text-slate-500 mt-0.5">{{ $a['sub'] }}</p>@endif
                    <p class="text-xs text-slate-400 mt-1">{{ $a['time']->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-8">Chưa có hoạt động.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
