@extends('layouts.admin')
@section('title', 'Tổng quan')
@section('page-title', 'Tổng quan hệ thống')

@php
$cards = [
    ['label' => 'Người dùng',     'value' => number_format($stats['users']),      'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'indigo',  'trend' => $trends['users']     ?? null, 'href' => 'admin.users.index'],
    ['label' => 'CV đã tạo',      'value' => number_format($stats['cvs']),        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'blue',    'trend' => $trends['cvs']       ?? null],
    ['label' => 'Tin tuyển dụng', 'value' => number_format($stats['job_posts']),  'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2', 'color' => 'emerald', 'trend' => $trends['job_posts'] ?? null, 'href' => 'admin.job-posts.index'],
    ['label' => 'Templates',      'value' => number_format($stats['templates']),  'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z', 'color' => 'purple', 'sub' => 'đang hoạt động'],
    ['label' => 'Doanh thu',      'value' => number_format($stats['revenue'], 0, ',', '.') . '₫', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color' => 'green', 'trend' => $trends['revenue']   ?? null, 'href' => 'admin.payments.index'],
    ['label' => 'Thanh toán chờ', 'value' => number_format($stats['pending_payments']), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber', 'sub' => 'cần xử lý'],
];

$colorBg = ['indigo' => 'bg-indigo-50',  'blue'   => 'bg-blue-50',  'emerald' => 'bg-emerald-50', 'purple' => 'bg-purple-50', 'green' => 'bg-green-50',  'amber' => 'bg-amber-50'];
$colorTx = ['indigo' => 'text-indigo-600','blue'   => 'text-blue-600','emerald' => 'text-emerald-600','purple' => 'text-purple-600','green' => 'text-green-600','amber' => 'text-amber-600'];
$colorBd = ['indigo' => 'border-indigo-100','blue' => 'border-blue-100','emerald' => 'border-emerald-100','purple' => 'border-purple-100','green' => 'border-green-100','amber' => 'border-amber-100'];
@endphp

@section('content')

{{-- 6 stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @foreach($cards as $card)
    <a href="{{ isset($card['href']) ? route($card['href']) : '#' }}"
       class="block bg-white rounded-xl border {{ $colorBd[$card['color']] ?? 'border-gray-100' }} shadow-sm p-5 hover:shadow-md transition group">
        <div class="flex items-start justify-between mb-3">
            <span class="text-xs font-medium text-gray-500">{{ $card['label'] }}</span>
            <div class="w-9 h-9 {{ $colorBg[$card['color']] ?? 'bg-gray-50' }} rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 {{ $colorTx[$card['color']] ?? 'text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-gray-900">{{ $card['value'] }}</div>
        <div class="mt-1 flex items-center gap-1 text-xs">
            @if(!empty($card['trend']))
                @if($card['trend']['dir'] === 'up')
                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    <span class="text-green-600 font-medium">+{{ $card['trend']['pct'] }}%</span>
                @elseif($card['trend']['dir'] === 'down')
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V3"/></svg>
                    <span class="text-red-600 font-medium">-{{ $card['trend']['pct'] }}%</span>
                @else
                    <span class="text-gray-400">→</span><span class="text-gray-400">0%</span>
                @endif
                <span class="text-gray-400">30 ngày</span>
            @else
                <span class="text-gray-400">{{ $card['sub'] ?? '' }}</span>
            @endif
        </div>
    </a>
    @endforeach
</div>

{{-- Quick actions row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.users.index') }}" class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-xl p-4 flex items-center gap-3 hover:shadow-lg transition">
        <span class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/></svg>
        </span>
        <div>
            <p class="font-semibold text-sm">Quản lý Users</p>
            <p class="text-xs text-indigo-100">{{ $stats['users'] }} tài khoản</p>
        </div>
    </a>
    <a href="{{ route('admin.templates.index') }}" class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-4 flex items-center gap-3 hover:shadow-lg transition">
        <span class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/></svg>
        </span>
        <div>
            <p class="font-semibold text-sm">Templates</p>
            <p class="text-xs text-purple-100">{{ $stats['templates'] }} đang hoạt động</p>
        </div>
    </a>
    <a href="{{ route('admin.blog.index') }}" class="bg-gradient-to-br from-rose-500 to-rose-600 text-white rounded-xl p-4 flex items-center gap-3 hover:shadow-lg transition">
        <span class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1"/></svg>
        </span>
        <div>
            <p class="font-semibold text-sm">Blog</p>
            <p class="text-xs text-rose-100">Quản lý bài viết</p>
        </div>
    </a>
    <a href="{{ route('admin.contacts.index') }}" class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-xl p-4 flex items-center gap-3 hover:shadow-lg transition">
        <span class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center relative">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
            @if($unreadContacts > 0)
            <span class="absolute -top-1 -right-1 w-4 h-4 text-[10px] bg-white text-amber-600 rounded-full flex items-center justify-center font-bold">{{ $unreadContacts }}</span>
            @endif
        </span>
        <div>
            <p class="font-semibold text-sm">Hộp thư</p>
            <p class="text-xs text-amber-100">{{ $unreadContacts }} liên hệ chưa đọc</p>
        </div>
    </a>
</div>

{{-- Charts row --}}
<div class="grid lg:grid-cols-3 gap-5 mb-6">

    {{-- Revenue 6 tháng (bar) --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900">Doanh thu 6 tháng gần nhất</h3>
            <span class="text-xs text-gray-400">Tổng: <strong class="text-gray-700">{{ number_format($stats['revenue'], 0, ',', '.') }}₫</strong></span>
        </div>
        @php $maxRev = $revenueByMonth->max('total') ?: 1; @endphp
        <div class="flex items-end space-x-3 h-44">
            @foreach($revenueByMonth as $month)
                @php
                    $h = $maxRev > 0 ? max(6, ($month->total / $maxRev) * 100) : 6;
                    $isCurrent = $month->month === now()->format('Y-m');
                @endphp
                <div class="flex-1 flex flex-col items-center space-y-1.5">
                    <span class="text-xs font-semibold {{ $isCurrent ? 'text-indigo-600' : 'text-gray-600' }}">{{ number_format($month->total / 1000) }}K</span>
                    <div class="w-full rounded-t-md {{ $isCurrent ? 'bg-gradient-to-b from-indigo-500 to-indigo-600' : 'bg-green-400' }} hover:opacity-80 transition relative"
                        style="height: {{ $h }}%">
                        @if($isCurrent)
                        <span class="absolute -top-2 left-1/2 -translate-x-1/2 w-2 h-2 bg-indigo-600 rounded-full"></span>
                        @endif
                    </div>
                    <span class="text-xs {{ $isCurrent ? 'text-indigo-700 font-semibold' : 'text-gray-400' }}">{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('m/Y') }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Plan distribution --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Phân bổ gói</h3>
        <div class="space-y-3">
            @forelse($planStats as $stat)
            @php $pct = $stats['users'] > 0 ? round($stat->count / $stats['users'] * 100) : 0; @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700 font-medium">{{ $stat->name ?? 'Chưa có gói' }}</span>
                    <span class="text-gray-500">{{ $stat->count }} ({{ $pct }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400">Chưa có dữ liệu.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- User growth chart (area) + System health --}}
<div class="grid lg:grid-cols-3 gap-5 mb-6">

    {{-- User growth 30 ngày (area chart CSS) --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Người dùng mới 30 ngày</h3>
            @php $total30 = $usersGrowth->sum('count'); @endphp
            <span class="text-xs text-gray-400">Tổng: <strong class="text-gray-700">{{ $total30 }}</strong></span>
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
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-40">
                <defs>
                    <linearGradient id="areaGrad" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.45"/>
                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <polygon fill="url(#areaGrad)" points="0,100 {{ implode(' ', $points) }} 100,100"/>
                <polyline fill="none" stroke="#6366f1" stroke-width="1.2" stroke-linejoin="round" points="{{ implode(' ', $points) }}"/>
            </svg>
            <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                <span>{{ \Carbon\Carbon::parse($usersGrowth->first()->date)->format('d/m') }}</span>
                <span>{{ \Carbon\Carbon::parse($usersGrowth->last()->date)->format('d/m') }}</span>
            </div>
        </div>
        @else
        <p class="text-sm text-gray-400 text-center py-6">Chưa có dữ liệu.</p>
        @endif
    </div>

    {{-- System health --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            System Health
        </h3>
        @php
            $diskUsedPct = $systemHealth['disk_total'] > 0 ? round(($systemHealth['disk_total'] - $systemHealth['disk_free']) / $systemHealth['disk_total'] * 100) : 0;
        @endphp
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-600">Disk</span>
                    <span class="font-medium {{ $diskUsedPct > 90 ? 'text-red-600' : ($diskUsedPct > 70 ? 'text-amber-600' : 'text-green-600') }}">{{ $diskUsedPct }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full {{ $diskUsedPct > 90 ? 'bg-red-500' : ($diskUsedPct > 70 ? 'bg-amber-500' : 'bg-green-500') }}" style="width: {{ $diskUsedPct }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Free: {{ number_format($systemHealth['disk_free'] / 1024 / 1024 / 1024, 2) }} GB</p>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">Queue jobs</span>
                <span class="font-semibold {{ $systemHealth['pending_jobs'] > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ number_format($systemHealth['pending_jobs']) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">Cache size</span>
                <span class="font-semibold text-gray-700">{{ number_format($systemHealth['cache_size'] / 1024, 1) }} KB</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600">Error logs</span>
                <span class="font-semibold text-gray-700">{{ number_format($systemHealth['logs_size'] / 1024, 1) }} KB</span>
            </div>
        </div>
    </div>
</div>

{{-- Top HR + Activity timeline --}}
<div class="grid lg:grid-cols-3 gap-5 mb-6">

    {{-- Top HR --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Top HR đăng tuyển</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topHr as $i => $job)
            <div class="flex items-center gap-3 px-5 py-3">
                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $i + 1 }}</span>
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                    {{ strtoupper(substr($job->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $job->user->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $job->user->email }}</p>
                </div>
                <span class="text-sm font-semibold text-gray-700">{{ $job->total }}</span>
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-gray-400 text-center">Chưa có dữ liệu.</p>
            @endforelse
        </div>
    </div>

    {{-- Activity timeline --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Hoạt động gần đây</h3>
        </div>
        <div class="p-5">
            @forelse($activity as $a)
            <div class="flex gap-3 pb-4 relative">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full bg-{{ $a['color'] }}-100 text-{{ $a['color'] }}-600 flex items-center justify-center flex-shrink-0">
                        @if($a['icon'] === 'cash')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                        @elseif($a['icon'] === 'edit')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        @else
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @endif
                    </div>
                    @if(!$loop->last)
                    <span class="w-px flex-1 bg-gray-200 mt-1"></span>
                    @endif
                </div>
                <div class="flex-1 min-w-0 pb-1">
                    <p class="text-sm font-medium text-gray-900">{{ $a['title'] }}</p>
                    @if($a['sub'])<p class="text-xs text-gray-500">{{ $a['sub'] }}</p>@endif
                    <p class="text-xs text-gray-400 mt-0.5">{{ $a['time']->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-6">Chưa có hoạt động.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
