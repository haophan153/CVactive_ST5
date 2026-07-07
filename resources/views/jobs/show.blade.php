@extends('layouts.app')

@section('title', $jobPost->title . ' - CVactive')

@push('styles')
<style>
    .prose-job { font-size: 15px; line-height: 1.75; color: #374151; }
    .prose-job h3 { font-size: 15px; font-weight: 700; color: #111827; margin: 20px 0 8px; }
    .prose-job ul { list-style: none; padding-left: 0; margin: 8px 0; }
    .prose-job ul li { padding: 4px 0 4px 20px; position: relative; }
    .prose-job ul li::before { content: '✓'; position: absolute; left: 0; color: #4F46E5; font-weight: 700; }
    .prose-job p { margin: 10px 0; }
    .prose-job strong { color: #111827; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- TOP NAV --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-indigo-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Danh sách việc làm
            </a>
            <div class="flex items-center gap-3">
                <button onclick="navigator.clipboard.writeText('{{ request()->fullUrl() }}'); this.textContent='Đã copy!'"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-indigo-600 transition px-3 py-1.5 rounded-lg hover:bg-indigo-50">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    Chia sẻ
                </button>
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-indigo-600 transition px-3 py-1.5 rounded-lg hover:bg-indigo-50">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Lưu tin
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8 pb-16">
        <div class="grid lg:grid-cols-12 gap-8">
            {{-- LEFT: main content --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- JOB HEADER CARD --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Header banner --}}
                    <div class="bg-gradient-to-r from-indigo-600 via-violet-600 to-purple-700 px-6 pt-8 pb-6">
                        <div class="flex items-start gap-4">
                            {{-- Company logo --}}
                            <div class="flex-shrink-0">
                                @if($jobPost->company_logo_url)
                                    <img src="{{ $jobPost->company_logo_url }}" alt="{{ $jobPost->company_name }}"
                                         class="w-16 h-16 object-contain rounded-2xl bg-white shadow-lg p-1.5">
                                @else
                                    <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl font-extrabold text-white">{{ $jobPost->company_initials }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Title + meta --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <div>
                                        <h1 class="text-2xl font-extrabold text-white leading-tight">{{ $jobPost->title }}</h1>
                                        <p class="mt-1 text-indigo-100 font-medium">
                                            {{ $jobPost->company_name ?: 'Công ty chưa cập nhật' }}
                                        </p>
                                    </div>
                                    @if($jobPost->is_hot)
                                        <span class="flex-shrink-0 inline-flex items-center gap-1 text-xs font-bold text-white px-3 py-1 rounded-full" style="background:linear-gradient(135deg,#ef4444,#f97316)">
                                            🔥 VIỆC HOT
                                        </span>
                                    @endif
                                </div>

                                {{-- Quick info chips --}}
                                <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                                    @if($jobPost->location)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur text-white text-xs font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            {{ $jobPost->location }}
                                        </span>
                                    @endif

                                    @php $typeInfo = $jobPost->type_info; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur text-white text-xs font-medium">
                                        {{ $typeInfo['label'] }}
                                    </span>

                                    @php $expInfo = $jobPost->experience_info; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur text-white text-xs font-medium">
                                        {{ $expInfo['label'] }}
                                    </span>

                                    @if($jobPost->is_remote)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/30 text-emerald-200 text-xs font-medium backdrop-blur">
                                            🌍 Remote
                                        </span>
                                    @endif

                                    @if($jobPost->is_urgent)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/30 text-red-200 text-xs font-medium backdrop-blur">
                                            ⏰ Sắp hết hạn
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Salary highlight --}}
                        @if($jobPost->salary_min || $jobPost->salary_max)
                            <div class="mt-5 flex items-center gap-3 bg-white/15 backdrop-blur rounded-xl p-4 border border-white/20">
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs text-indigo-200 font-medium">Mức lương</div>
                                    <div class="text-xl font-extrabold text-white">{{ $jobPost->salary_label }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Header footer meta --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-sm">
                        <div class="flex flex-wrap items-center gap-4 text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Đăng {{ $jobPost->published_at?->diffForHumans() }}
                            </span>
                            @if($jobPost->expires_at)
                                <span class="flex items-center gap-1.5 {{ $jobPost->is_urgent ? 'text-red-600 font-semibold' : '' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Hết hạn {{ $jobPost->expires_at->format('d/m/Y') }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                {{ $jobPost->views_count ?? 0 }} lượt xem
                            </span>
                            @if($jobPost->applications_count > 0)
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ $jobPost->applications_count }} ứng tuyển
                                </span>
                            @endif
                        </div>
                        <a href="#apply" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                            Ứng tuyển ngay
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>

                {{-- JOB CONTENT --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
                    {{-- Company info --}}
                    @if($jobPost->company_name || $jobPost->company_description)
                        <div class="mb-7 pb-7 border-b border-gray-100">
                            <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <span class="w-1 h-5 bg-indigo-600 rounded-full"></span>
                                Về công ty
                            </h2>
                            <p class="font-semibold text-gray-900">{{ $jobPost->company_name }}</p>
                            @if($jobPost->company_description)
                                <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $jobPost->company_description }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Job description --}}
                    @if($jobPost->description)
                        <div class="mb-7 pb-7 border-b border-gray-100">
                            <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <span class="w-1 h-5 bg-indigo-600 rounded-full"></span>
                                Mô tả công việc
                            </h2>
                            <div class="prose-job">{!! nl2br(e($jobPost->description)) !!}</div>
                        </div>
                    @endif

                    {{-- Benefits/Perks --}}
                    <div class="mb-7 pb-7 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="w-1 h-5 bg-emerald-500 rounded-full"></span>
                            Phúc lợi & Quyền lợi
                        </h2>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach([
                                'Lương tháng 13 + Thưởng hiệu suất',
                                'Bảo hiểm xã hội, y tế, thất nghiệp',
                                'Môi trường làm việc chuyên nghiệp',
                                'Cơ hội đào tạo & thăng tiến',
                                'Du lịch, team building hàng năm',
                                'Trang thiết bị làm việc hiện đại',
                            ] as $perk)
                                <div class="flex items-center gap-2.5 text-sm text-gray-700">
                                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    {{ $perk }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Contact --}}
                    @if($jobPost->contact_email || $jobPost->contact_phone)
                        <div class="rounded-xl bg-gradient-to-r from-indigo-50 to-violet-50 border border-indigo-100 p-5">
                            <h2 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Liên hệ ứng tuyển
                            </h2>
                            <div class="flex flex-wrap gap-4">
                                @if($jobPost->contact_email)
                                    <a href="mailto:{{ $jobPost->contact_email }}" class="inline-flex items-center gap-2 text-indigo-700 font-medium text-sm hover:text-indigo-900 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ $jobPost->contact_email }}
                                    </a>
                                @endif
                                @if($jobPost->contact_phone)
                                    <a href="tel:{{ $jobPost->contact_phone }}" class="inline-flex items-center gap-2 text-indigo-700 font-medium text-sm hover:text-indigo-900 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $jobPost->contact_phone }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- APPLY FORM --}}
                <div id="apply" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-5">
                        <h2 class="text-xl font-bold text-white">Nộp hồ sơ ứng tuyển</h2>
                        <p class="text-indigo-200 text-sm mt-1">Điền thông tin bên dưới để ứng tuyển vị trí này</p>
                    </div>
                    <div class="p-6 lg:p-8">
                        @auth
                            <form action="{{ route('jobs.apply', $jobPost) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                <div class="grid sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Họ và tên <span class="text-red-500">*</span></label>
                                        <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                                    </div>
                                </div>

                                <div class="grid sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Số điện thoại</label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Chọn CV có sẵn</label>
                                        <select name="cv_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                                            <option value="">-- Tạo CV mới --</option>
                                            @foreach(\App\Models\Cv::where('user_id', auth()->id())->get() as $cv)
                                                <option value="{{ $cv->id }}" {{ old('cv_id') == $cv->id ? 'selected' : '' }}>{{ $cv->title ?? 'CV #' . $cv->id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tải lên CV (PDF, DOC, DOCX)</label>
                                    <input type="file" name="cv_file" accept=".pdf,.doc,.docx"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                    <p class="text-xs text-gray-400 mt-1.5">Kích thước tối đa: 5MB</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Thư giới thiệu</label>
                                    <textarea name="cover_letter" rows="4"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition resize-none"
                                        placeholder="Giới thiệu ngắn về bản thân và lý do phù hợp với vị trí này...">{{ old('cover_letter') }}</textarea>
                                </div>

                                <div class="pt-2">
                                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-600/25 text-sm">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Gửi hồ sơ ứng tuyển
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-8">
                                <div class="w-16 h-16 mx-auto bg-indigo-50 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Đăng nhập để ứng tuyển</h3>
                                <p class="text-sm text-gray-500 mt-2 mb-6 max-w-sm mx-auto">Bạn cần đăng nhập tài khoản để có thể nộp hồ sơ ứng tuyển.</p>
                                <div class="flex flex-col sm:flex-row justify-center gap-3">
                                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->path()) }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition text-sm">
                                        Đăng nhập
                                    </a>
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition text-sm">
                                        Tạo tài khoản mới
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                {{-- Session messages --}}
                @if(session('success'))
                    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">
                        <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-medium text-sm">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                        <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-medium text-sm">{{ session('error') }}</p>
                    </div>
                @endif
                </div>

            {{-- RIGHT: sidebar --}}
            <aside class="lg:col-span-4 space-y-5">
                {{-- Apply sticky card --}}
                <div class="sticky top-6 space-y-5">
                    {{-- Salary summary --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="w-1 h-5 bg-indigo-600 rounded-full"></span>
                            Thông tin lương
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Lương tối thiểu</span>
                                <span class="font-semibold text-gray-900">{{ $jobPost->salary_range['min'] ?? 'Thương lượng' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Lương tối đa</span>
                                <span class="font-semibold text-gray-900">{{ $jobPost->salary_range['max'] ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Loại công việc</span>
                                <span class="font-semibold text-gray-900">{{ $jobPost->type_info['label'] }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Cấp bậc</span>
                                <span class="font-semibold text-gray-900">{{ $jobPost->experience_info['label'] }}</span>
                            </div>
                            @if($jobPost->category)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Ngành</span>
                                <span class="font-semibold text-gray-900">{{ $jobPost->category_info['label'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <div class="flex flex-wrap gap-2">
                            @if($jobPost->job_type)
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-full">{{ $jobPost->type_info['label'] }}</span>
                            @endif
                            @if($jobPost->experience_level)
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">{{ $jobPost->experience_info['label'] }}</span>
                            @endif
                            @if($jobPost->is_remote)
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full">🌍 Remote</span>
                            @endif
                            @if($jobPost->is_hot)
                                <span class="px-3 py-1 text-white text-xs font-bold rounded-full" style="background:linear-gradient(135deg,#ef4444,#f97316)">🔥 HOT</span>
                            @endif
                            @if($jobPost->is_urgent)
                                <span class="px-3 py-1 bg-red-50 text-red-700 text-xs font-medium rounded-full">⏰ Gấp</span>
                            @endif
                            @if($jobPost->location)
                                <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">📍 {{ $jobPost->location }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white p-6">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </div>
                            <h3 class="text-lg font-bold leading-tight">Sẵn sàng ứng tuyển?</h3>
                            <p class="text-sm text-indigo-100 mt-2">Tạo CV chuyên nghiệp chỉ trong vài phút để gây ấn tượng với nhà tuyển dụng.</p>
                            <a href="{{ route('templates.index') }}" class="mt-4 inline-flex items-center gap-2 bg-white text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-amber-300 hover:text-indigo-950 transition">
                                Tạo CV ngay
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        {{-- RELATED JOBS --}}
        @if($relatedJobs->count() > 0)
        <div class="mt-10">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-indigo-600 rounded-full"></span>
                Việc làm liên quan
            </h2>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($relatedJobs as $job)
                    <article class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-indigo-200 p-5 transition group">
                        <a href="{{ route('jobs.show', $job) }}" class="block">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    @if($job->company_logo_url)
                                        <img src="{{ $job->company_logo_url }}" class="w-12 h-12 object-contain rounded-xl bg-white p-1 shadow-sm">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-50 to-violet-50 rounded-xl flex items-center justify-center">
                                            <span class="text-lg font-bold text-indigo-400">{{ $job->company_initials }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-1">{{ $job->title }}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $job->company_name }}</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                        @if($job->salary_min || $job->salary_max)
                                            <span class="font-semibold text-green-600">{{ $job->salary_label }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Thương lượng</span>
                                        @endif
                                        @if($job->location)
                                            <span class="text-gray-400">· {{ $job->location }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
