@php
    use Illuminate\Support\Carbon;
@endphp
@extends('layouts.app')

@section('title', $jobPost->title)

@section('content')
<div class="py-6 sm:py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Back link --}}
        <div>
            <a href="{{ route('hr.job-posts.index') }}" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Quay lại danh sách
            </a>
        </div>

        {{-- ─── Header ─── --}}
        @php
            $statusMap = [
                'draft'     => ['bg' => 'bg-gray-100 text-gray-700',   'dot' => 'bg-gray-400',   'label' => 'Nháp'],
                'published' => ['bg' => 'bg-emerald-100 text-emerald-700','dot' => 'bg-emerald-500','label' => 'Đang đăng'],
                'closed'    => ['bg' => 'bg-rose-100 text-rose-700',    'dot' => 'bg-rose-500',   'label' => 'Đã đóng'],
            ];
            $st = $statusMap[$jobPost->status] ?? $statusMap['draft'];
            $typeInfo = $jobPost->type_info;
            $expInfo  = $jobPost->experience_info;
            $catInfo  = $jobPost->category_info;
        @endphp

        <div class="relative rounded-2xl overflow-hidden bg-[#0F172A] text-white shadow-xl">
            <div class="absolute inset-0 opacity-20 pointer-events-none">
                <div class="absolute -top-20 -left-20 w-80 h-80 bg-white/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 right-0 w-96 h-96 bg-fuchsia-300/40 rounded-full blur-3xl"></div>
            </div>

            <div class="relative p-6 sm:p-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-start gap-4 min-w-0 flex-1">
                        @if($jobPost->company_logo_url)
                            <img src="{{ $jobPost->company_logo_url }}" alt="Logo" class="w-16 h-16 rounded-xl object-contain bg-white p-1.5 shadow-md flex-shrink-0">
                        @else
                            <div class="w-16 h-16 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center font-extrabold text-2xl flex-shrink-0">
                                {{ $jobPost->company_initials }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $st['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                    {{ $st['label'] }}
                                </span>
                                @if($jobPost->is_hot)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-300 text-amber-950">🔥 HOT</span>
                                @endif
                                @if($jobPost->is_remote)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-300 text-emerald-950">🌍 Remote</span>
                                @endif
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold leading-tight line-clamp-2">{{ $jobPost->title }}</h1>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3 text-sm text-indigo-100">
                                @if($jobPost->company_name)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                                        {{ $jobPost->company_name }}
                                    </span>
                                @endif
                                @if($jobPost->location)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $jobPost->location }}
                                    </span>
                                @endif
                                @if($jobPost->job_type)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                                        {{ $typeInfo['label'] }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ $catInfo['label'] ?? 'N/A' }} · {{ $expInfo['short'] ?? '' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($jobPost->status === 'draft')
                            <form action="{{ route('hr.job-posts.publish', $jobPost) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-semibold text-sm shadow-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Đăng tin
                                </button>
                            </form>
                        @elseif($jobPost->status === 'published')
                            <form action="{{ route('hr.job-posts.close', $jobPost) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg font-semibold text-sm shadow-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Đóng tin
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('hr.job-posts.edit', $jobPost) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-indigo-700 hover:bg-indigo-50 rounded-lg font-semibold text-sm shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/></svg>
                            Chỉnh sửa
                        </a>
                        <a href="{{ $jobPost->share_url }}" target="_blank" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg" title="Xem tin công khai">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Stats row ─── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $appCards = [
                    ['label' => 'Tổng hồ sơ',  'value' => $appStats['total'],     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'indigo'],
                    ['label' => 'Chờ duyệt',   'value' => $appStats['pending'],   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
                    ['label' => 'Đang xem xét','value' => $appStats['reviewing'], 'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color' => 'sky'],
                    ['label' => 'Đã duyệt',    'value' => $appStats['approved'],  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
                ];
                $palette = [
                    'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600'],
                    'amber'   => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600'],
                    'sky'     => ['bg' => 'bg-sky-50',     'text' => 'text-sky-600'],
                    'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                ];
            @endphp
            @foreach($appCards as $c)
                @php $p = $palette[$c['color']]; @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] uppercase tracking-wider text-gray-500 font-semibold">{{ $c['label'] }}</span>
                        <div class="w-9 h-9 {{ $p['bg'] }} rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 {{ $p['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $c['icon'] }}"/></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 tabular-nums">{{ number_format($c['value']) }}</div>
                </div>
            @endforeach
        </div>

        {{-- ─── Salary card (if present) ─── --}}
        @if($jobPost->salary_min || $jobPost->salary_max)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Mức lương</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-gray-900 mt-0.5">
                        @if($jobPost->salary_min && $jobPost->salary_max)
                            {{ number_format($jobPost->salary_min) }}<span class="text-gray-400 mx-1">–</span>{{ number_format($jobPost->salary_max) }}<span class="text-base font-bold text-gray-500 ml-1">{{ $jobPost->salary_currency }}</span>
                        @elseif($jobPost->salary_min)
                            Từ {{ number_format($jobPost->salary_min) }} {{ $jobPost->salary_currency }}
                        @else
                            Đến {{ number_format($jobPost->salary_max) }} {{ $jobPost->salary_currency }}
                        @endif
                    </p>
                </div>
            </div>
        @endif

        {{-- ─── Tabs ─── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div role="tablist" aria-label="Chi tiết tin" class="flex border-b border-gray-100 bg-gray-50/50">
                <button type="button" role="tab" data-tab="overview"
                    class="px-5 py-3.5 text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600">
                    📄 Tổng quan
                </button>
                <button type="button" role="tab" data-tab="applications"
                    class="px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900">
                    👥 Hồ sơ ({{ $appStats['total'] }})
                </button>
                <button type="button" role="tab" data-tab="company"
                    class="px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900">
                    🏢 Về công ty
                </button>
                <button type="button" role="tab" data-tab="meta"
                    class="px-5 py-3.5 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900">
                    ℹ️ Thông tin
                </button>
            </div>

            {{-- Tab: Overview --}}
            <div data-panel="overview" class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Mô tả công việc</h3>
                <div class="prose prose-indigo max-w-none text-gray-700 leading-relaxed whitespace-pre-line">{{ $jobPost->description }}</div>

                @if($jobPost->contact_email || $jobPost->contact_phone)
                    <div class="mt-6 p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                        <h4 class="text-sm font-bold text-indigo-900 mb-2">📞 Thông tin liên hệ</h4>
                        <div class="space-y-1 text-sm text-indigo-800">
                            @if($jobPost->contact_email)
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                                    <a href="mailto:{{ $jobPost->contact_email }}" class="hover:underline">{{ $jobPost->contact_email }}</a>
                                </p>
                            @endif
                            @if($jobPost->contact_phone)
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19"/></svg>
                                    <a href="tel:{{ $jobPost->contact_phone }}" class="hover:underline">{{ $jobPost->contact_phone }}</a>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tab: Applications --}}
            <div data-panel="applications" class="hidden p-6 sm:p-8">
                @if($jobPost->applications->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857"/></svg>
                        </div>
                        <h4 class="font-semibold text-gray-900">Chưa có hồ sơ nào</h4>
                        <p class="text-sm text-gray-500 mt-1">Khi có ứng viên nộp đơn, danh sách sẽ hiện ở đây.</p>
                    </div>
                @else
                    <ul class="space-y-3">
                        @foreach($jobPost->applications as $app)
                            @php
                                $statusMap = [
                                    'pending'   => ['bg-yellow-100 text-yellow-700', 'Chờ duyệt'],
                                    'reviewing' => ['bg-sky-100 text-sky-700', 'Đang xem'],
                                    'approved'  => ['bg-emerald-100 text-emerald-700', 'Đã duyệt'],
                                    'rejected'  => ['bg-rose-100 text-rose-700', 'Từ chối'],
                                    'interview' => ['bg-violet-100 text-violet-700', 'Phỏng vấn'],
                                ];
                                $st = $statusMap[$app->status] ?? ['bg-gray-100 text-gray-600', $app->status];
                            @endphp
                            <li>
                                <a href="{{ route('hr.applications.show', $app) }}"
                                    class="block p-4 rounded-xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/40 transition">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                                                {{ mb_substr($app->full_name, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-gray-900 truncate">{{ $app->full_name }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ $app->email }} · {{ $app->phone ?: '—' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $st[0] }}">{{ $st[1] }}</span>
                                            <p class="text-[11px] text-gray-400 mt-1">{{ $app->applied_at?->diffForHumans() ?? '' }}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-6 text-center">
                        <a href="{{ route('hr.job-posts.applications', $jobPost) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            Xem tất cả {{ $appStats['total'] }} hồ sơ
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Tab: Company --}}
            <div data-panel="company" class="hidden p-6 sm:p-8">
                @if($jobPost->company_name || $jobPost->company_description)
                    <div class="flex items-start gap-4">
                        @if($jobPost->company_logo_url)
                            <img src="{{ $jobPost->company_logo_url }}" alt="Logo" class="w-20 h-20 rounded-xl object-contain border border-gray-100 p-1.5 flex-shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-2xl flex-shrink-0">
                                {{ $jobPost->company_initials }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">{{ $jobPost->company_name ?: 'Công ty chưa cập nhật' }}</h3>
                            @if($jobPost->company_description)
                                <p class="text-gray-700 mt-2 leading-relaxed whitespace-pre-line">{{ $jobPost->company_description }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-center text-gray-500 py-12">Chưa có thông tin công ty. <a href="{{ route('hr.job-posts.edit', $jobPost) }}" class="text-indigo-600 font-semibold">Thêm ngay →</a></p>
                @endif
            </div>

            {{-- Tab: Meta --}}
            <div data-panel="meta" class="hidden p-6 sm:p-8">
                <dl class="divide-y divide-gray-100">
                    <div class="py-3 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">ID</dt>
                        <dd class="text-sm font-mono text-gray-900">#{{ $jobPost->id }}</dd>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Ngày tạo</dt>
                        <dd class="text-sm text-gray-900">{{ $jobPost->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="py-3 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Cập nhật</dt>
                        <dd class="text-sm text-gray-900">{{ $jobPost->updated_at->diffForHumans() }}</dd>
                    </div>
                    @if($jobPost->published_at)
                        <div class="py-3 flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Ngày đăng</dt>
                            <dd class="text-sm text-gray-900">{{ $jobPost->published_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    @endif
                    @if($jobPost->expires_at)
                        <div class="py-3 flex items-center justify-between">
                            <dt class="text-sm text-gray-500">Hết hạn</dt>
                            <dd class="text-sm {{ $jobPost->expires_at->isPast() ? 'text-rose-600 font-semibold' : 'text-gray-900' }}">
                                {{ $jobPost->expires_at->format('d/m/Y') }}
                                @if($jobPost->expires_at->isFuture())
                                    ({{ $jobPost->expires_at->diffInDays(now()) }} ngày nữa)
                                @endif
                            </dd>
                        </div>
                    @endif
                    <div class="py-3 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Lượt xem</dt>
                        <dd class="text-sm text-gray-900">{{ number_format($jobPost->views_count ?? 0) }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex flex-wrap items-center gap-2">
                    <form action="{{ route('hr.job-posts.destroy', $jobPost) }}" method="POST"
                        onsubmit="return confirm('Xóa tin &quot;{{ $jobPost->title }}&quot;? Hành động này không thể hoàn tác.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Xóa tin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const tabs   = document.querySelectorAll('[role="tab"]');
    const panels = document.querySelectorAll('[data-panel]');
    tabs.forEach((t) => {
        t.addEventListener('click', () => {
            tabs.forEach((tt) => {
                const active = tt === t;
                tt.classList.toggle('border-indigo-600', active);
                tt.classList.toggle('text-indigo-600', active);
                tt.classList.toggle('border-transparent', !active);
                tt.classList.toggle('text-gray-500', !active);
            });
            panels.forEach((p) => {
                p.classList.toggle('hidden', p.dataset.panel !== t.dataset.tab);
            });
        });
    });
})();
</script>
@endpush
@endsection
