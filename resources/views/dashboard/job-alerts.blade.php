@php
    $categories = [
        'it' => 'Công nghệ thông tin',
        'marketing' => 'Marketing',
        'design' => 'Thiết kế',
        'finance' => 'Tài chính / Kế toán',
        'hr' => 'Nhân sự',
        'sales' => 'Kinh doanh / Bán hàng',
        'operation' => 'Vận hành / QA',
        'consulting' => 'Tư vấn',
        'education' => 'Giáo dục / Đào tạo',
        'other' => 'Khác',
    ];
    $jobTypes = [
        'full-time' => 'Toàn thời gian',
        'part-time' => 'Bán thời gian',
        'contract' => 'Hợp đồng',
        'internship' => 'Thực tập',
        'remote' => 'Remote / Từ xa',
    ];
@endphp

<x-app-layout>

    {{-- ── Header bar ── --}}
    <div class="bg-slate-900 border-b border-slate-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-tight">Smart Job Matcher</h1>
                        <p class="text-sm text-slate-400 mt-0.5">AI chủ động gợi ý việc làm phù hợp dựa trên CV của bạn</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main content ── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- ══ Master toggle banner ════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/20 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Smart Job Matcher</h2>
                        <p id="toggle-subtitle" class="text-sm text-slate-400 mt-0.5">
                            @if($alert && $alert->is_active)
                                Đang hoạt động · {{ $alert->notification_frequency === 'daily' ? 'Email hàng ngày lúc 8:00' : 'Gửi ngay khi có việc mới' }}
                            @else
                                AI gợi ý việc làm phù hợp mỗi ngày dựa trên CV của bạn
                            @endif
                        </p>
                    </div>
                </div>

                    <div class="flex items-center flex-shrink-0">
                        <button type="button" id="toggle-btn" class="cursor-pointer select-none">
                            <div id="toggle-track" class="w-14 h-8 bg-slate-200 rounded-full transition-colors duration-200 relative" style="background-color: {{ ($alert?->is_active ?? false) ? '#6366f1' : '#e2e8f0' }}">
                                <div id="toggle-thumb" class="absolute top-1 left-1 w-6 h-6 bg-white border border-slate-300 rounded-full shadow-sm transition-transform duration-200" style="transform: translateX({{ ($alert?->is_active ?? false) ? '24px' : '0px' }})"></div>
                            </div>
                        </button>
                    </div>
            </div>
        </div>

        {{-- ══ Stats row ══════════════════════════════════════ --}}
        <div id="stats-row" @if(!($alert && $alert->is_active)) style="display:none" @endif>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php
                $statsCards = [
                    [
                        'label' => 'Ngưỡng khớp',
                        'value' => $alert->match_threshold . '%',
                        'icon' => 'target',
                        'bg' => 'bg-indigo-500/10',
                        'icon_bg' => 'bg-indigo-500',
                        'color' => 'text-indigo-500',
                    ],
                    [
                        'label' => 'Tần suất',
                        'value' => $alert->notification_frequency === 'daily' ? 'Hàng ngày' : 'Tức thì',
                        'icon' => 'clock',
                        'bg' => 'bg-violet-500/10',
                        'icon_bg' => 'bg-violet-500',
                        'color' => 'text-violet-500',
                    ],
                    [
                        'label' => 'Danh mục',
                        'value' => $alert->preferred_categories ? count($alert->preferred_categories) : 'Tất cả',
                        'sub'  => $alert->preferred_categories ? 'danh mục' : null,
                        'icon' => 'grid',
                        'bg' => 'bg-purple-500/10',
                        'icon_bg' => 'bg-purple-500',
                        'color' => 'text-purple-500',
                    ],
                    [
                        'label' => 'Kỹ năng',
                        'value' => $profile && count($profile->skills ?? []) > 0 ? count($profile->skills) : '—',
                        'sub'  => $profile && count($profile->skills ?? []) > 0 ? 'đã phân tích' : null,
                        'icon' => 'star',
                        'bg' => 'bg-amber-500/10',
                        'icon_bg' => 'bg-amber-500',
                        'color' => 'text-amber-500',
                    ],
                ];
            @endphp
            @foreach($statsCards as $stat)
            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">{{ $stat['label'] }}</p>
                    <div class="{{ $stat['bg'] }} rounded-xl p-2">
                        <div class="{{ $stat['icon_bg'] }} rounded-lg p-1.5 w-7 h-7 flex items-center justify-center">
                            @if($stat['icon'] === 'target')
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20M2 12h20"/></svg>
                            @elseif($stat['icon'] === 'clock')
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($stat['icon'] === 'grid')
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                            @else
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-end gap-1.5">
                    <span class="text-2xl font-black text-slate-900 tabular-nums">{{ $stat['value'] }}</span>
                    @if(!empty($stat['sub']))
                        <span class="text-xs text-slate-400 mb-0.5">{{ $stat['sub'] }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        </div>

        {{-- ══ Settings form ══════════════════════════════════ --}}
        <form id="job-alerts-form" action="{{ route('dashboard.job-alerts.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="is_active" id="is_active_input" value="{{ $alert && $alert->is_active ? 1 : 0 }}">

            <div class="grid lg:grid-cols-5 gap-4">
                {{-- Left: settings --}}
                <div class="lg:col-span-3 space-y-4">

                    {{-- Threshold + Frequency side by side --}}
                    <div class="grid sm:grid-cols-2 gap-4">
                        {{-- Threshold --}}
                        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-7 h-7 rounded-lg bg-indigo-500 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20M2 12h20"/></svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">Ngưỡng khớp</h3>
                            </div>
                            <p class="text-xs text-slate-400 mb-3">Chỉ gợi ý việc có điểm khớp từ ngưỡng này</p>
                            @php $currentThreshold = $alert?->match_threshold ?? 60; @endphp
                            <div class="flex flex-col gap-2">
                                @foreach([80 => 'Cao', 60 => 'Trung bình', 40 => 'Thấp'] as $val => $label)
                                <label class="cursor-pointer" onclick="selectThreshold({{ $val }})">
                                    <input type="radio" name="match_threshold" value="{{ $val }}"
                                        {{ $currentThreshold == $val ? 'checked' : '' }}
                                        class="hidden peer">
                                    <div id="threshold-{{ $val }}" class="flex items-center justify-between py-2.5 px-4 rounded-xl border-2 border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition hover:border-slate-300
                                        {{ $currentThreshold == $val ? 'border-indigo-500 bg-indigo-50' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full @if($val == 80) bg-emerald-500 w-full @elseif($val == 60) bg-amber-500 w-[60%] @else bg-slate-400 w-[40%] @endif"></div>
                                            </div>
                                            <span class="text-sm font-medium {{ $currentThreshold == $val ? 'text-indigo-700' : 'text-slate-600' }}">{{ $label }}</span>
                                        </div>
                                        <span class="text-xs font-bold text-slate-400">{{ $val }}%</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Frequency --}}
                        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-7 h-7 rounded-lg bg-violet-500 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900">Tần suất</h3>
                            </div>
                            <p class="text-xs text-slate-400 mb-3">Email gửi lúc 8:00 sáng mỗi ngày</p>
                            @php $currentFreq = $alert?->notification_frequency ?? 'daily'; @endphp
                            <div class="flex flex-col gap-2">
                                @foreach(['daily' => ['label' => 'Hàng ngày', 'desc' => 'Tổng hợp mỗi sáng', 'icon' => 'calendar'], 'instant' => ['label' => 'Tức thì', 'desc' => 'Ngay khi có việc mới', 'icon' => 'zap']] as $val => $opt)
                                <label class="cursor-pointer" onclick="selectFrequency('{{ $val }}')">
                                    <input type="radio" name="notification_frequency" value="{{ $val }}"
                                        {{ $currentFreq == $val ? 'checked' : '' }}
                                        class="hidden peer">
                                    <div id="frequency-{{ $val }}" class="flex items-center gap-3 py-2.5 px-4 rounded-xl border-2 border-slate-200 transition hover:border-slate-300
                                        {{ $currentFreq == $val ? 'border-violet-500 bg-violet-50' : '' }}">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                                            @if($opt['icon'] === 'calendar')
                                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium block {{ $currentFreq == $val ? 'text-violet-700' : 'text-slate-600' }}">{{ $opt['label'] }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $opt['desc'] }}</span>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Categories --}}
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 rounded-lg bg-purple-500 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Danh mục quan tâm</h3>
                        </div>
                        <p class="text-xs text-slate-400 mb-4">Để trống = nhận tất cả danh mục</p>

                        @php $selectedCats = $alert?->preferred_categories ?? $profile?->preferred_categories ?? []; @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach($categories as $key => $cat)
                            <button type="button" id="cat-btn-{{ $key }}" onclick="toggleCategory('{{ $key }}')"
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border transition
                                    {{ in_array($key, $selectedCats) ? 'bg-purple-500 text-white border-purple-500' : 'border-slate-200 text-slate-600 hover:border-purple-300' }}">
                                {{ $cat }}
                            </button>
                            @endforeach
                        </div>
                        <div id="cat-hidden-inputs">
                            @foreach($categories as $key => $cat)
                            <input type="checkbox" name="preferred_categories[]" value="{{ $key }}"
                                {{ in_array($key, $selectedCats) ? 'checked' : '' }}
                                class="hidden cat-input">
                            @endforeach
                        </div>
                    </div>

                    {{-- Job types --}}
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Loại hình công việc</h3>
                        </div>
                        <p class="text-xs text-slate-400 mb-4">Để trống = tất cả loại hình</p>

                        @php $selectedTypes = $alert?->preferred_job_types ?? $profile?->preferred_job_types ?? []; @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach($jobTypes as $key => $type)
                            <button type="button" id="type-btn-{{ $key }}" onclick="toggleJobType('{{ $key }}')"
                                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border transition
                                    {{ in_array($key, $selectedTypes) ? 'bg-emerald-500 text-white border-emerald-500' : 'border-slate-200 text-slate-600 hover:border-emerald-300' }}">
                                {{ $type }}
                            </button>
                            @endforeach
                        </div>
                        <div id="type-hidden-inputs">
                            @foreach($jobTypes as $key => $type)
                            <input type="checkbox" name="preferred_job_types[]" value="{{ $key }}"
                                {{ in_array($key, $selectedTypes) ? 'checked' : '' }}
                                class="hidden type-input">
                            @endforeach
                        </div>
                    </div>

                    {{-- Locations --}}
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 rounded-lg bg-blue-500 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Địa điểm ưa thích</h3>
                        </div>
                        <p class="text-xs text-slate-400 mb-4">Nhấn Enter hoặc dấu phẩy để thêm địa điểm</p>

                        <div class="flex flex-wrap gap-2" id="location-tags">
                            @foreach(($alert?->preferred_locations ?? []) as $loc)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-full border border-blue-200">
                                {{ $loc }}
                                <button type="button" onclick="removeLocation(this, '{{ addslashes($loc) }}')" class="hover:text-blue-900 font-bold leading-none">×</button>
                                <input type="hidden" name="preferred_locations[]" value="{{ $loc }}">
                            </span>
                            @endforeach
                            <input type="text" id="location-input" placeholder="+ Thêm địa điểm"
                                class="px-3 py-1.5 text-xs border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 w-40 placeholder:text-slate-300"
                                onkeydown="if(event.key==='Enter'||event.key===','){event.preventDefault();addLocation(this.value);this.value='';}">
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center gap-4 pt-1">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-indigo-900/30 active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Lưu cài đặt
                        </button>
                        @if(session('success'))
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ session('success') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Right: skill profile + recent matches --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Skill profile + Upload CV --}}
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Kỹ năng từ CV</h3>
                                @if($profile && $profile->last_extracted_at)
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $profile->last_extracted_at->diffForHumans() }}</p>
                                @endif
                            </div>
                            @if($profile && count($profile->skills ?? []) > 0)
                            <button type="button" id="extract-btn" onclick="extractSkills()"
                                class="text-[10px] font-medium text-indigo-600 border border-indigo-200 rounded-lg px-2.5 py-1 hover:bg-indigo-50 transition">
                                ↻ Cập nhật
                            </button>
                            @endif
                        </div>

                        @if($profile && count($profile->skills ?? []) > 0)
                            @php $topSkills = array_slice($profile->skills, 0, 12); @endphp
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($topSkills as $skill)
                                <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[11px] rounded-lg font-medium">{{ $skill }}</span>
                                @endforeach
                            </div>
                            @if(count($profile->skills) > 12)
                                <p class="text-[10px] text-slate-400">+{{ count($profile->skills) - 12 }} kỹ năng khác</p>
                            @endif
                            @if($profile->experience_level)
                                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[11px] text-slate-400">Cấp bậc</span>
                                    <span class="text-[11px] font-bold text-slate-700 capitalize">{{ $profile->experience_level }}</span>
                                </div>
                            @endif
                        @endif

                        {{-- Drop zone --}}
                        <div id="cv-dropzone"
                            class="mt-3 relative border-2 border-dashed border-slate-200 rounded-xl p-4 text-center cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/30 transition group"
                            onclick="document.getElementById('cv-file-input').click()">
                            <input type="file" id="cv-file-input" accept=".pdf,.txt,application/pdf,text/plain" class="hidden">
                            <div class="flex flex-col items-center gap-1.5 pointer-events-none">
                                <div class="w-9 h-9 rounded-lg bg-slate-50 group-hover:bg-indigo-100 flex items-center justify-center transition">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-medium text-slate-700">
                                    @if($profile && count($profile->skills ?? []) > 0)
                                        Upload CV khác để cập nhật
                                    @else
                                        Kéo thả CV vào đây hoặc <span class="text-indigo-600">chọn file</span>
                                    @endif
                                </p>
                                <p class="text-[10px] text-slate-400">PDF hoặc TXT · tối đa 5MB</p>
                            </div>

                            {{-- Loading overlay --}}
                            <div id="cv-upload-loading" class="absolute inset-0 bg-white/90 rounded-xl flex flex-col items-center justify-center hidden">
                                <svg class="w-7 h-7 text-indigo-500 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <p id="cv-upload-status" class="text-xs font-medium text-slate-700">Đang đọc file...</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">AI đang phân tích kỹ năng</p>
                            </div>
                        </div>

                        {{-- Upload error --}}
                        <div id="cv-upload-error" class="hidden mt-2 px-3 py-2 bg-rose-50 border border-rose-100 rounded-lg">
                            <p class="text-[11px] text-rose-700" id="cv-upload-error-msg"></p>
                        </div>

                        {{-- Upload success --}}
                        <div id="cv-upload-success" class="hidden mt-2 px-3 py-2 bg-emerald-50 border border-emerald-100 rounded-lg">
                            <p class="text-[11px] text-emerald-700" id="cv-upload-success-msg"></p>
                        </div>
                    </div>

                    {{-- Recent matches --}}
                    @if($recentMatches->count() > 0)
                    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900">Việc đã gợi ý</h3>
                            <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">{{ $recentMatches->count() }} việc gần đây</span>
                        </div>
                        <ul class="divide-y divide-slate-50">
                            @foreach($recentMatches as $match)
                            @php $job = $match->jobPost; @endphp
                            <li class="hover:bg-slate-50/60 transition">
                                <a href="{{ $job->share_url ?? '#' }}" target="_blank" class="flex items-start gap-3 px-5 py-3.5">
                                    @if($job->company_logo_url)
                                        <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}"
                                            class="w-8 h-8 rounded-lg object-contain bg-white border border-slate-100 p-0.5 flex-shrink-0">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-xs font-bold text-indigo-600 flex-shrink-0">
                                            {{ $job->company_initials ?? '??' }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate">{{ $job->title }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $job->company_name }} · {{ $job->location }}</p>
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                            {{ $match->final_score >= 75 ? 'bg-emerald-50 text-emerald-700' : ($match->final_score >= 50 ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-500') }}">
                                            {{ $match->final_score }}%
                                        </span>
                                        @if($match->viewed_at)
                                            <p class="text-[9px] text-slate-400 mt-0.5">Đã xem</p>
                                        @endif
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        (function () {
            // ── Toggle button → optimistic UI + AJAX ─────────────
            var toggleBtn = document.getElementById('toggle-btn');
            var toggleTrack = document.getElementById('toggle-track');
            var toggleThumb = document.getElementById('toggle-thumb');
            var toggleSubtitle = document.getElementById('toggle-subtitle');
            var statsRow = document.getElementById('stats-row');
            var isActive = {{ ($alert?->is_active ?? false) ? 'true' : 'false' }};

            function renderToggle() {
                if (isActive) {
                    toggleTrack.style.backgroundColor = '#6366f1';
                    toggleThumb.style.transform = 'translateX(24px)';
                    if (toggleSubtitle) toggleSubtitle.innerHTML = 'Đang hoạt động · ' + ({{ $alert?->notification_frequency === 'instant' ? 'true' : 'false' }} ? 'Gửi ngay khi có việc mới' : 'Email hàng ngày lúc 8:00');
                    if (statsRow) statsRow.style.display = '';
                } else {
                    toggleTrack.style.backgroundColor = '#e2e8f0';
                    toggleThumb.style.transform = 'translateX(0px)';
                    if (toggleSubtitle) toggleSubtitle.innerHTML = 'AI gợi ý việc làm phù hợp mỗi ngày dựa trên CV của bạn';
                    if (statsRow) statsRow.style.display = 'none';
                }
            }

            if (toggleBtn) {
                renderToggle();
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    isActive = !isActive;
                    renderToggle();
                    fetch('{{ route("dashboard.job-alerts.toggle") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ is_active: isActive })
                    }).catch(function () {
                        isActive = !isActive;
                        renderToggle();
                        alert('Lỗi kết nối. Vui lòng thử lại.');
                    });
                });
            }

            // ── Threshold radio ───────────────────────────────────
            window.selectThreshold = function (val) {
                [80, 60, 40].forEach(function (t) {
                    var div = document.getElementById('threshold-' + t);
                    var input = document.querySelector('input[name="match_threshold"][value="' + t + '"]');
                    if (t == val) {
                        div.className = 'flex items-center justify-between py-2.5 px-4 rounded-xl border-2 border-indigo-500 bg-indigo-50 transition hover:border-slate-300';
                        div.querySelector('span').className = 'text-sm font-medium text-indigo-700';
                        input.checked = true;
                    } else {
                        div.className = 'flex items-center justify-between py-2.5 px-4 rounded-xl border-2 border-slate-200 transition hover:border-slate-300';
                        div.querySelector('span').className = 'text-sm font-medium text-slate-600';
                        input.checked = false;
                    }
                });
            };

            // ── Frequency radio ───────────────────────────────────
            window.selectFrequency = function (val) {
                ['daily', 'instant'].forEach(function (f) {
                    var div = document.getElementById('frequency-' + f);
                    var input = document.querySelector('input[name="notification_frequency"][value="' + f + '"]');
                    if (f == val) {
                        div.className = 'flex items-center gap-3 py-2.5 px-4 rounded-xl border-2 border-violet-500 bg-violet-50 transition hover:border-slate-300';
                        div.querySelector('span').className = 'text-sm font-medium block text-violet-700';
                        input.checked = true;
                    } else {
                        div.className = 'flex items-center gap-3 py-2.5 px-4 rounded-xl border-2 border-slate-200 transition hover:border-slate-300';
                        div.querySelector('span').className = 'text-sm font-medium block text-slate-600';
                        input.checked = false;
                    }
                });
            };

            // ── Categories checkbox ──────────────────────────────
            window.toggleCategory = function (key) {
                var input = document.querySelector('.cat-input[value="' + key + '"]');
                var btn = document.getElementById('cat-btn-' + key);
                input.checked = !input.checked;
                if (input.checked) {
                    btn.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border border-purple-500 bg-purple-500 text-white transition';
                } else {
                    btn.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border border-slate-200 text-slate-600 hover:border-purple-300 transition';
                }
            };

            // ── Job types checkbox ───────────────────────────────
            window.toggleJobType = function (key) {
                var input = document.querySelector('.type-input[value="' + key + '"]');
                var btn = document.getElementById('type-btn-' + key);
                input.checked = !input.checked;
                if (input.checked) {
                    btn.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border border-emerald-500 bg-emerald-500 text-white transition';
                } else {
                    btn.className = 'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border border-slate-200 text-slate-600 hover:border-emerald-300 transition';
                }
            };
            window.addLocation = function (value) {
                var trimmed = value.trim().replace(/,/g, '');
                if (!trimmed) return;
                var container = document.getElementById('location-tags');
                var existing = container.querySelector('input[name="preferred_locations[]"][value="' + trimmed + '"]');
                if (existing) return;
                var span = document.createElement('span');
                span.className = 'inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-full border border-blue-200';
                span.innerHTML = trimmed + '<button type="button" onclick="removeLocation(this)" class="hover:text-blue-900 font-bold leading-none">×</button><input type="hidden" name="preferred_locations[]" value="' + trimmed + '">';
                container.insertBefore(span, container.lastElementChild);
            };

            window.removeLocation = function (btn) {
                btn.closest('span').remove();
            };

            // ── Extract skills via AJAX ──────────────────────────
            window.extractSkills = function () {
                const btn = document.getElementById('extract-btn');
                if (!btn) return;
                btn.disabled = true;
                btn.textContent = 'Đang phân tích...';
                fetch('{{ route("dashboard.job-alerts.extract-skills") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Lỗi khi phân tích CV.');
                        btn.disabled = false;
                        btn.textContent = '↻ Cập nhật';
                    }
                })
                .catch(function() {
                    alert('Lỗi kết nối.');
                    btn.disabled = false;
                    btn.textContent = '↻ Cập nhật';
                });
            };

            // ── Upload CV: drag-drop + click-to-pick ────────────
            var dropzone   = document.getElementById('cv-dropzone');
            var fileInput  = document.getElementById('cv-file-input');
            var loadingEl  = document.getElementById('cv-upload-loading');
            var statusEl   = document.getElementById('cv-upload-status');
            var errorEl    = document.getElementById('cv-upload-error');
            var errorMsg   = document.getElementById('cv-upload-error-msg');
            var successEl  = document.getElementById('cv-upload-success');
            var successMsg = document.getElementById('cv-upload-success-msg');

            function hideMessages() {
                errorEl.classList.add('hidden');
                successEl.classList.add('hidden');
            }

            function setLoading(on, status) {
                if (on) {
                    dropzone.classList.add('pointer-events-none');
                    loadingEl.classList.remove('hidden');
                    if (status) statusEl.textContent = status;
                } else {
                    dropzone.classList.remove('pointer-events-none');
                    loadingEl.classList.add('hidden');
                }
            }

            function showError(msg) {
                hideMessages();
                errorMsg.textContent = msg;
                errorEl.classList.remove('hidden');
                setTimeout(function() { errorEl.classList.add('hidden'); }, 5000);
            }

            function showSuccess(msg) {
                hideMessages();
                successMsg.textContent = msg;
                successEl.classList.remove('hidden');
                setTimeout(function() { successEl.classList.add('hidden'); }, 4000);
            }

            function uploadFile(file) {
                if (!file) return;

                hideMessages();

                // Client-side validation
                var allowedTypes = ['application/pdf', 'text/plain'];
                var ext = (file.name.split('.').pop() || '').toLowerCase();
                if (allowedTypes.indexOf(file.type) === -1 && ['pdf', 'txt'].indexOf(ext) === -1) {
                    showError('Chỉ chấp nhận file PDF hoặc TXT.');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    showError('File tối đa 5MB.');
                    return;
                }

                setLoading(true, 'Đang tải lên...');

                var formData = new FormData();
                formData.append('cv_file', file);
                formData.append('_token', '{{ csrf_token() }}');

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("dashboard.job-alerts.upload-cv") }}', true);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                        var pct = Math.round((e.loaded / e.total) * 100);
                        statusEl.textContent = 'Đang tải lên... ' + pct + '%';
                    }
                };

                xhr.onload = function () {
                    var data;
                    try { data = JSON.parse(xhr.responseText); } catch (e) { data = {}; }

                    if (xhr.status >= 200 && xhr.status < 300 && data.success) {
                        setLoading(true, 'AI đang phân tích kỹ năng...');
                        showSuccess(data.message || 'Phân tích thành công!');
                        setTimeout(function () {
                            location.reload();
                        }, 1200);
                    } else {
                        setLoading(false);
                        showError(data.message || ('Lỗi ' + xhr.status));
                    }
                };

                xhr.onerror = function () {
                    setLoading(false);
                    showError('Lỗi kết nối. Vui lòng thử lại.');
                };

                xhr.send(formData);
            }

            if (dropzone && fileInput) {
                fileInput.addEventListener('change', function (e) {
                    var file = e.target.files && e.target.files[0];
                    if (file) uploadFile(file);
                    e.target.value = ''; // reset để upload lại cùng file
                });

                ['dragenter', 'dragover'].forEach(function (ev) {
                    dropzone.addEventListener(ev, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
                    });
                });

                ['dragleave', 'drop'].forEach(function (ev) {
                    dropzone.addEventListener(ev, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
                    });
                });

                dropzone.addEventListener('drop', function (e) {
                    var dt = e.dataTransfer;
                    if (dt && dt.files && dt.files.length) {
                        uploadFile(dt.files[0]);
                    }
                });
            }
        })();
    </script>
    @endpush
</x-app-layout>
