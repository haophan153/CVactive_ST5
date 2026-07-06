@extends('layouts.app')

@section('title', 'Tìm việc làm - CVactive')

@push('styles')
<style>
    .filter-scroll::-webkit-scrollbar { width: 4px; }
    .filter-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .filter-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .filter-pill { transition: all 0.2s ease; }
    .filter-pill:hover { transform: translateY(-1px); }
    .job-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .job-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.04); }
    @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
    .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
    .filter-overlay { backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); }
    .custom-checkbox { appearance: none; -webkit-appearance: none; width: 16px; height: 16px; border: 2px solid #d1d5db; border-radius: 4px; cursor: pointer; transition: all 0.15s; flex-shrink: 0; }
    .custom-checkbox:checked { background-color: #4f46e5; border-color: #4f46e5; background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e"); }
    .custom-checkbox:hover { border-color: #4f46e5; }
    .view-btn { transition: all 0.15s; }
    .view-btn.active { background: #4f46e5; color: white; }
    .view-btn:not(.active) { background: #f1f5f9; color: #6b7280; }
    .view-btn:not(.active):hover { background: #e5e7eb; }
    .hot-badge { background: linear-gradient(135deg, #ef4444, #f97316); }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] bg-indigo-600 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-violet-600 rounded-full blur-[80px]"></div>
        </div>
        <div class="relative max-w-6xl mx-auto px-4 py-14 lg:py-16">
            <div class="text-center max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 px-3 py-1 rounded-full text-xs font-medium mb-5">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    {{ number_format($totalJobs) }} việc làm đang tuyển
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight">
                    Tìm <span class="bg-gradient-to-r from-amber-300 via-pink-300 to-indigo-300 bg-clip-text text-transparent">Cơ Hội</span> Việc Làm
                </h1>
                <p class="mt-4 text-indigo-200 text-base max-w-xl mx-auto">
                    Hàng nghìn việc làm chất lượng cao từ {{ number_format($totalCompanies) }} công ty uy tín, cập nhật liên tục mỗi ngày.
                </p>

                {{-- Search --}}
                <form id="hero-search-form" class="mt-8 flex flex-col sm:flex-row gap-2 max-w-2xl mx-auto">
                    <div class="relative flex-1">
                        <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <input type="search" name="keyword" id="keyword" value="{{ $filters['keyword'] ?? '' }}"
                            placeholder="Tìm việc, kỹ năng, chức danh..."
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white/10 backdrop-blur border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-400 focus:border-transparent focus:bg-white/20 transition text-sm">
                    </div>
                    <div class="relative sm:w-52">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <input type="text" name="location" id="location" value="{{ $filters['location'] ?? '' }}"
                            placeholder="Địa điểm..."
                            class="w-full pl-10 pr-4 py-3.5 rounded-xl bg-white/10 backdrop-blur border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-400 focus:border-transparent focus:bg-white/20 transition text-sm">
                    </div>
                    <button type="submit" class="px-6 py-3.5 rounded-xl bg-amber-400 text-slate-900 font-bold hover:bg-amber-300 transition shadow-lg shadow-amber-400/25 flex items-center justify-center gap-2 text-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                        Tìm kiếm
                    </button>
                </form>

                {{-- Quick stats --}}
                <div class="mt-10 flex flex-wrap items-center justify-center gap-8 text-sm">
                    <div class="text-center"><div class="text-2xl font-extrabold text-white">{{ number_format($totalJobs) }}</div><div class="text-indigo-300 mt-0.5">Việc làm</div></div>
                    <div class="w-px h-10 bg-indigo-500/30"></div>
                    <div class="text-center"><div class="text-2xl font-extrabold text-emerald-400">{{ number_format($totalCompanies) }}</div><div class="text-indigo-300 mt-0.5">Công ty</div></div>
                    <div class="w-px h-10 bg-indigo-500/30"></div>
                    <div class="text-center"><div class="text-2xl font-extrabold text-amber-400">{{ number_format(App\Models\JobPost::published()->where('is_remote', true)->count()) }}</div><div class="text-indigo-300 mt-0.5">Remote</div></div>
                </div>
            </div>
        </div>
    </section>

    {{-- MAIN LAYOUT --}}
    <div class="max-w-6xl mx-auto px-4 py-8 pb-16">
        <div class="grid lg:grid-cols-12 gap-8">
            {{-- LEFT: sidebar filters --}}
            <aside class="lg:col-span-3 space-y-5">
                {{-- Quick category filter --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Ngành nghề
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('jobs.index', array_filter(['q' => $filters['keyword'] ?? '', 'location' => $filters['location'] ?? '', 'sort' => $filters['sort'] ?? ''])) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ !request('category') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                            Tất cả
                        </a>
                        @foreach(App\Models\JobPost::CATEGORIES as $val => $info)
                            @if(in_array($val, array_filter(explode(',', $filters['category'] ?? ''))) || in_array($val, (array)($filters['category'] ?? [])))
                                <a href="{{ route('jobs.index', array_filter(['q' => $filters['keyword'] ?? '', 'location' => $filters['location'] ?? '', 'sort' => $filters['sort'] ?? ''])) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-indigo-600 text-white shadow-sm transition">
                                    {{ $info['label'] }}
                                </a>
                            @else
                                <a href="{{ route('jobs.index', array_filter(['q' => $filters['keyword'] ?? '', 'location' => $filters['location'] ?? '', 'category' => $val, 'sort' => $filters['sort'] ?? ''])) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                    {{ $info['label'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Main filter card --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span class="text-sm font-semibold text-gray-900">Bộ lọc</span>
                        </div>
                        @if(count(array_filter($filters)) > 0)
                        <button id="clear-all-filters" type="button" class="text-xs text-red-500 hover:text-red-700 font-medium transition">Xóa tất cả</button>
                        @endif
                    </div>

                    <form id="filter-form" class="filter-scroll max-h-[calc(100vh-240px)] overflow-y-auto">
                        {{-- Job Type --}}
                        <div class="p-4 border-b border-gray-50">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Loại công việc</h4>
                            <div class="space-y-2.5">
                                @foreach(App\Models\JobPost::JOB_TYPES as $val => $info)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="job_type[]" value="{{ $val }}"
                                               class="custom-checkbox"
                                               @checked(in_array($val, (array)($filters['job_type'] ?? [])))>
                                        <span class="text-sm text-gray-600 group-hover:text-gray-900 transition flex-1">{{ $info['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Experience Level --}}
                        <div class="p-4 border-b border-gray-50">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Cấp bậc</h4>
                            <div class="space-y-2.5">
                                @foreach(App\Models\JobPost::EXPERIENCE_LEVELS as $val => $info)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="checkbox" name="experience_level[]" value="{{ $val }}"
                                               class="custom-checkbox"
                                               @checked(in_array($val, (array)($filters['experience_level'] ?? [])))>
                                        <span class="text-sm text-gray-600 group-hover:text-gray-900 transition flex-1">{{ $info['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Salary Range --}}
                        <div class="p-4 border-b border-gray-50">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Mức lương</h4>
                            <div class="space-y-2.5">
                                <select name="min_salary" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                                    <option value="">Lương tối thiểu</option>
                                    @foreach(App\Models\JobPost::SALARY_RANGES as $val => $label)
                                        @if($val > 0)
                                            <option value="{{ $val }}" {{ ($filters['min_salary'] ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <select name="max_salary" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                                    <option value="">Lương tối đa</option>
                                    @foreach(App\Models\JobPost::SALARY_RANGES as $val => $label)
                                        @if($val > 0)
                                            <option value="{{ $val }}" {{ ($filters['max_salary'] ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Remote --}}
                        <div class="p-4">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="is_remote" value="1"
                                       class="custom-checkbox"
                                       @checked(($filters['is_remote'] ?? '') == '1')>
                                <span class="text-sm text-gray-600 group-hover:text-gray-900 transition flex-1 flex items-center gap-1.5">
                                    <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                                    Chỉ việc Remote
                                </span>
                            </label>
                        </div>
                    </form>
                </div>

                {{-- CTA post job --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white p-6">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold leading-tight">Đăng tuyển dụng</h3>
                        <p class="text-sm text-indigo-100 mt-2">Tiếp cận hàng nghìn ứng viên chất lượng mỗi ngày.</p>
                        @auth
                        <a href="{{ route('hr.job-posts.create') }}" class="mt-4 inline-flex items-center gap-2 bg-white text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-amber-300 hover:text-indigo-950 transition">
                            Đăng tin tuyển dụng
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="mt-4 inline-flex items-center gap-2 bg-white text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-amber-300 hover:text-indigo-950 transition">
                            Đăng nhập để đăng tin
                        </a>
                        @endauth
                    </div>
                </div>
            </aside>

            {{-- RIGHT: job listings --}}
            <div class="lg:col-span-9 space-y-5">
                {{-- Header + Sort bar --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            @if(count(array_filter($filters)) > 0)
                                <span class="text-indigo-600">Kết quả lọc</span>
                            @else
                                <span class="text-indigo-600">Việc làm mới nhất</span>
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Tìm thấy <strong class="text-gray-800" id="total-count">{{ number_format($jobPosts->total()) }}</strong> việc làm
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <select id="sort-select" name="sort" onchange="document.getElementById('sort-form-' + this.value.split('=')[0] || 'sort-form').submit()"
                                class="appearance-none pl-3 pr-8 py-2 rounded-lg text-sm font-medium bg-white border border-gray-200 text-gray-700 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-200 transition cursor-pointer">
                            @foreach(App\Models\JobPost::SORT_OPTIONS as $val => $label)
                                <option value="{{ route('jobs.index', array_filter(['q' => $filters['keyword'] ?? '', 'location' => $filters['location'] ?? '', 'sort' => $val, 'category' => $filters['category'] ?? ''])) }}"
                                    {{ ($filters['sort'] ?? 'newest') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" style="margin-left:-20px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                {{-- Active filter pills --}}
                @if(count(array_filter($filters)) > 0)
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-400 font-medium">Đang lọc:</span>
                    @if(!empty($filters['keyword']))
                        <span class="filter-pill inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
                            "{{ $filters['keyword'] }}"
                            <button onclick="removeFilter('keyword')" class="hover:text-indigo-900"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </span>
                    @endif
                    @if(!empty($filters['location']))
                        <span class="filter-pill inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
                            📍 {{ $filters['location'] }}
                            <button onclick="removeFilter('location')" class="hover:text-indigo-900"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </span>
                    @endif
                    @if(!empty($filters['job_type']))
                        @foreach((array)$filters['job_type'] as $type)
                            @if(isset(App\Models\JobPost::JOB_TYPES[$type]))
                            <span class="filter-pill inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
                                {{ App\Models\JobPost::JOB_TYPES[$type]['label'] }}
                                <button onclick="removeFilter('job_type', '{{ $type }}')" class="hover:text-indigo-900"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                            </span>
                            @endif
                        @endforeach
                    @endif
                    @if(!empty($filters['experience_level']))
                        @foreach((array)$filters['experience_level'] as $lv)
                            @if(isset(App\Models\JobPost::EXPERIENCE_LEVELS[$lv]))
                            <span class="filter-pill inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
                                {{ App\Models\JobPost::EXPERIENCE_LEVELS[$lv]['label'] }}
                                <button onclick="removeFilter('experience_level', '{{ $lv }}')" class="hover:text-indigo-900"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                            </span>
                            @endif
                        @endforeach
                    @endif
                    @if(($filters['is_remote'] ?? '') == '1')
                        <span class="filter-pill inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium">
                            🌐 Remote
                            <button onclick="removeFilter('is_remote')" class="hover:text-emerald-900"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </span>
                    @endif
                    <button id="clear-all-inline" type="button" class="text-xs text-red-500 hover:text-red-700 font-medium underline underline-offset-2">Xóa tất cả</button>
                </div>
                @endif

                {{-- Jobs list --}}
                <div id="job-listings">
                    @if($jobPosts->count() > 0)
                        <div class="space-y-3" id="jobs-grid">
                            @foreach($jobPosts as $job)
                                @php $typeInfo = $job->type_info; $expInfo = $job->experience_info; @endphp
                                <article class="job-card bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-indigo-200 overflow-hidden">
                                    <a href="{{ route('jobs.show', $job) }}" class="block">
                                        <div class="flex items-start gap-4 p-5">
                                            {{-- Logo --}}
                                            <div class="flex-shrink-0">
                                                @if($job->company_logo_url)
                                                    <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}"
                                                         class="w-14 h-14 object-contain rounded-xl shadow-sm bg-white p-1">
                                                @else
                                                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-50 to-violet-50 rounded-xl flex items-center justify-center">
                                                        <span class="text-xl font-bold text-indigo-400">{{ $job->company_initials }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Main info --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <h3 class="text-base font-bold text-gray-900 hover:text-indigo-600 transition line-clamp-1">
                                                                {{ $job->title }}
                                                            </h3>
                                                            @if($job->is_new)
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-white bg-emerald-500 px-1.5 py-0.5 rounded-full shadow-sm animate-pulse">MỚI</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1">
                                                            <span>{{ $job->company_name ?: 'Công ty chưa cập nhật' }}</span>
                                                            @if($job->is_hot)
                                                                <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-white px-1.5 py-0.5 rounded-full hot-badge">🔥 HOT</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0 hidden sm:block">
                                                        {{ $job->published_for_humans }}
                                                    </span>
                                                </div>

                                                {{-- Tags --}}
                                                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeInfo['color'] }}">
                                                        {{ $typeInfo['label'] }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                        {{ $expInfo['label'] }}
                                                    </span>
                                                    @if($job->is_remote)
                                                        <span class="inline-flex items-center gap-0.5 text-xs font-medium text-emerald-600">
                                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Remote
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Bottom meta --}}
                                                <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-sm">
                                                    @if($job->location)
                                                        <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                            {{ $job->location }}
                                                        </span>
                                                    @endif

                                                    @if($job->salary_min || $job->salary_max)
                                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            {{ $job->salary_label }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-xs text-gray-400 italic">Thương lượng</span>
                                                    @endif

                                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400 ml-auto">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        {{ $job->views_label }}
                                                    </span>

                                                    @if($job->applications_count > 0)
                                                        <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                            {{ $job->applications_count }} ứng viên
                                                        </span>
                                                    @endif

                                                    @if($job->is_urgent)
                                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-white bg-red-500 px-1.5 py-0.5 rounded-full">
                                                            ⏰ Sắp hết hạn
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($jobPosts->hasPages())
                        <div class="mt-6">{{ $jobPosts->withQueryString()->links('pagination::tailwind') }}</div>
                        @endif
                    @else
                        {{-- Empty --}}
                        <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                            <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">Không tìm thấy việc làm nào</h3>
                            <p class="text-sm text-gray-500 mt-2 mb-6">Hãy thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm.</p>
                            <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                                Xem tất cả việc làm
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Loading --}}
                <div id="loading-overlay" class="hidden fixed inset-0 z-40 flex items-center justify-center bg-white/70 backdrop-blur-sm">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                        <p class="text-sm text-gray-500 font-medium">Đang tải...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    const $ = (s) => document.querySelector(s);
    const $$ = (s) => document.querySelectorAll(s);
    const DEBOUNCE_MS = 300;

    function getFilters() {
        const params = new URLSearchParams();
        const kw = $('#keyword')?.value.trim();
        if (kw) params.set('keyword', kw);
        const loc = $('#location')?.value.trim();
        if (loc) params.set('location', loc);
        const sort = $('#sort-select')?.value;
        if (sort) params.set('sort', sort);
        ['job_type', 'experience_level'].forEach(name => {
            const vals = [...$$(`input[name="${name}[]"]:checked`)].map(el => el.value);
            if (vals.length) params.set(name, vals.join(','));
        });
        const minSal = $('select[name="min_salary"]')?.value;
        const maxSal = $('select[name="max_salary"]')?.value;
        if (minSal) params.set('min_salary', minSal);
        if (maxSal) params.set('max_salary', maxSal);
        const remote = $('input[name="is_remote"]:checked');
        if (remote) params.set('is_remote', '1');
        return params;
    }

    function buildUrl() {
        const p = getFilters();
        return `${window.location.pathname}${p.toString() ? '?' + p.toString() : ''}`;
    }

    function fetchJobs() {
        const params = getFilters();
        const url = `/api/jobs?${params.toString()}&per_page=12`;
        $('#loading-overlay')?.classList.remove('hidden');
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(json => {
                renderJobs(json.data, json.meta);
                history.pushState({}, '', buildUrl());
                const totalEl = $('#total-count');
                if (totalEl) totalEl.textContent = new Intl.NumberFormat('vi').format(json.meta.total);
            })
            .catch(() => { /* fallback to page reload */ window.location.href = buildUrl(); })
            .finally(() => $('#loading-overlay')?.classList.add('hidden'));
    }

    function renderJobs(jobs, meta) {
        const container = $('#jobs-grid');
        if (!container) return;
        const JOB_TYPES = @json(App\Models\JobPost::JOB_TYPES);
        const EXP_LEVELS = @json(App\Models\JobPost::EXPERIENCE_LEVELS);

        if (!jobs || jobs.length === 0) {
            container.outerHTML = `<div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg">Không tìm thấy việc làm nào</h3>
                <p class="text-sm text-gray-500 mt-2 mb-6">Hãy thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm.</p>
                <a href="${window.location.pathname}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">Xem tất cả việc làm</a>
            </div>`;
            return;
        }

        container.innerHTML = jobs.map(job => {
            const typeInfo = JOB_TYPES[job.job_type] || { label: job.job_type || '', color: 'bg-gray-50 text-gray-600' };
            const expInfo = EXP_LEVELS[job.experience_level] || { label: job.experience_level || '' };
            const logoHtml = job.company_logo
                ? `<img src="${job.company_logo}" alt="${job.company_name || ''}" class="w-14 h-14 object-contain rounded-xl shadow-sm bg-white p-1">`
                : `<div class="w-14 h-14 bg-gradient-to-br from-indigo-50 to-violet-50 rounded-xl flex items-center justify-center"><span class="text-xl font-bold text-indigo-400">${(job.company_name || job.title || 'J').charAt(0).toUpperCase()}</span></div>`;

            const salaryHtml = (() => {
                if (job.salary_min && job.salary_max) {
                    return `<span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ${(job.salary_min/1000000).toFixed(0)} - ${(job.salary_max/1000000).toFixed(0)} triệu
                    </span>`;
                } else if (job.salary_min) {
                    return `<span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600">Từ ${(job.salary_min/1000000).toFixed(0)} triệu</span>`;
                }
                return `<span class="inline-flex items-center gap-1 text-xs text-gray-400 italic">Thương lượng</span>`;
            })();

            const hotBadge = job.is_hot ? '<span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-white px-1.5 py-0.5 rounded-full" style="background:linear-gradient(135deg,#ef4444,#f97316)">🔥 HOT</span>' : '';
            const newBadge = job.is_new ? '<span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-white bg-emerald-500 px-1.5 py-0.5 rounded-full shadow-sm">MỚI</span>' : '';
            const remoteBadge = job.is_remote ? '<span class="inline-flex items-center gap-0.5 text-xs font-medium text-emerald-600"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Remote</span>' : '';
            const urgentBadge = job.is_urgent ? '<span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-white bg-red-500 px-1.5 py-0.5 rounded-full">⏰ Sắp hết hạn</span>' : '';
            const appsHtml = job.applications_count > 0 ? `<span class="inline-flex items-center gap-1 text-xs text-gray-400"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>${job.applications_count} ứng viên</span>` : '';
            const viewsLabel = job.views_count >= 1000 ? (job.views_count/1000).toFixed(1)+'K' : job.views_count;

            return `<article class="job-card bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-indigo-200 overflow-hidden">
                <a href="/jobs/${job.id}" class="block">
                    <div class="flex items-start gap-4 p-5">
                        <div class="flex-shrink-0">${logoHtml}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-base font-bold text-gray-900 hover:text-indigo-600 transition line-clamp-1">${esc(job.title)}</h3>
                                        ${newBadge}${hotBadge}
                                    </div>
                                    <p class="text-sm text-gray-500 mt-0.5">${esc(job.company_name || 'Công ty chưa cập nhật')}</p>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0 hidden sm:block">${timeAgo(job.published_at)}</span>
                            </div>
                            <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${typeInfo.color}">${typeInfo.label}</span>
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    ${expInfo.label}
                                </span>
                                ${remoteBadge}
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-sm">
                                ${job.location ? `<span class="inline-flex items-center gap-1 text-xs text-gray-500"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${esc(job.location)}</span>` : ''}
                                ${salaryHtml}
                                <span class="inline-flex items-center gap-1 text-xs text-gray-400 ml-auto"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>${viewsLabel}</span>
                                ${appsHtml}
                                ${urgentBadge}
                            </div>
                        </div>
                    </div>
                </a>
            </article>`;
        }).join('');
    }

    function esc(s) { if (!s) return ''; const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function timeAgo(dateStr) {
        if (!dateStr) return '';
        const s = Math.floor((new Date() - new Date(dateStr)) / 1000);
        const intervals = [['năm',31536000],['tháng',2592000],['tuần',604800],['ngày',86400],['giờ',3600],['phút',60]];
        for (const [l,s_] of intervals) { const c=Math.floor(s/s_); if(c>=1) return `${c} ${l} trước`; }
        return 'Vừa xong';
    }

    function removeFilter(key, value = null) {
        if (key === 'keyword') { const el = $('#keyword'); if(el) el.value = ''; }
        else if (key === 'location') { const el = $('#location'); if(el) el.value = ''; }
        else if (key === 'min_salary') { const el = $('select[name="min_salary"]'); if(el) el.value = ''; }
        else if (key === 'max_salary') { const el = $('select[name="max_salary"]'); if(el) el.value = ''; }
        else if (key === 'is_remote') { const el = $('input[name="is_remote"]'); if(el) el.checked = false; }
        else if (value !== null) {
            const cb = document.querySelector(`input[name="${key}[]"][value="${value}"]`);
            if (cb) cb.checked = false;
        }
        fetchJobs();
    }
    window.removeFilter = removeFilter;

    function clearAll() {
        ['keyword','location'].forEach(id => { const el = $(`#${id}`); if(el) el.value = ''; });
        $$('input[type="checkbox"]:checked').forEach(el => el.checked = false);
        $('select[name="min_salary"]')?.removeAttribute('selected');
        $('select[name="max_salary"]')?.removeAttribute('selected');
        fetchJobs();
    }
    window.clearAllFilters = clearAll;

    // Event bindings
    $('#hero-search-form')?.addEventListener('submit', e => { e.preventDefault(); fetchJobs(); });
    $('#keyword')?.addEventListener('input', () => { clearTimeout(window._debounce); window._debounce = setTimeout(fetchJobs, DEBOUNCE_MS); });
    $('#location')?.addEventListener('input', () => { clearTimeout(window._debounce2); window._debounce2 = setTimeout(fetchJobs, DEBOUNCE_MS); });
    ['job_type', 'experience_level'].forEach(name => {
        $$(`input[name="${name}[]"]`).forEach(cb => cb.addEventListener('change', fetchJobs));
    });
    $('select[name="min_salary"]')?.addEventListener('change', fetchJobs);
    $('select[name="max_salary"]')?.addEventListener('change', fetchJobs);
    $('input[name="is_remote"]')?.addEventListener('change', fetchJobs);
    $('#clear-all-filters')?.addEventListener('click', clearAll);
    $('#clear-all-inline')?.addEventListener('click', clearAll);

    // Back/forward
    window.addEventListener('popstate', () => {
        const p = new URLSearchParams(location.search);
        const kw = $('#keyword'); if(kw) kw.value = p.get('keyword') || '';
        const loc = $('#location'); if(loc) loc.value = p.get('location') || '';
        ['job_type','experience_level'].forEach(name => {
            const vals = (p.get(name) || '').split(',').filter(Boolean);
            $$(`input[name="${name}[]"]`).forEach(cb => cb.checked = vals.includes(cb.value));
        });
        fetchJobs();
    });
})();
</script>
@endpush
