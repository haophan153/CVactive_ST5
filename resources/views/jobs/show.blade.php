@extends('layouts.app')

@section('title', $jobPost->title . ' - CVactive')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="{{ route('jobs.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại danh sách việc làm
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Header -->
            <div class="bg-indigo-600 px-6 py-6">
                <div class="flex items-start gap-4">
                    @if($jobPost->company_logo)
                        <img src="{{ asset('storage/' . $jobPost->company_logo) }}" alt="{{ $jobPost->company_name }}" class="w-20 h-20 object-contain rounded-lg bg-white p-2">
                    @else
                        <div class="w-20 h-20 bg-white rounded-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-white">{{ $jobPost->title }}</h1>
                        <p class="text-indigo-100 mt-1">{{ $jobPost->company_name ?: 'Công ty chưa cập nhật' }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-4 text-indigo-100">
                            @if($jobPost->location)
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $jobPost->location }}
                                </span>
                            @endif
                            @if($jobPost->job_type)
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @switch($jobPost->job_type)
                                        @case('full-time') Toàn thời gian @break
                                        @case('part-time') Bán thời gian @break
                                        @case('contract') Hợp đồng @break
                                        @case('intern') Thực tập @break
                                        @case('freelance') Freelance @break
                                        @default {{ $jobPost->job_type }}
                                    @endswitch
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @if($jobPost->salary_min && $jobPost->salary_max)
                                    {{ number_format($jobPost->salary_min) }} - {{ number_format($jobPost->salary_max) }} {{ $jobPost->salary_currency }}
                                @elseif($jobPost->salary_min)
                                    Từ {{ number_format($jobPost->salary_min) }} {{ $jobPost->salary_currency }}
                                @elseif($jobPost->salary_max)
                                    Up to {{ number_format($jobPost->salary_max) }} {{ $jobPost->salary_currency }}
                                @else
                                    Thỏa thuận
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <!-- Company Info -->
                @if($jobPost->company_name || $jobPost->company_description)
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-2">Về công ty</h2>
                        @if($jobPost->company_name)
                            <p class="font-medium text-gray-900">{{ $jobPost->company_name }}</p>
                        @endif
                        @if($jobPost->company_description)
                            <p class="text-sm text-gray-600 mt-2">{{ $jobPost->company_description }}</p>
                        @endif
                    </div>
                @endif

                <!-- Job Description -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Mô tả công việc</h2>
                    <div class="prose max-w-none text-gray-600 whitespace-pre-wrap">{{ $jobPost->description }}</div>
                </div>

                <!-- Contact Info -->
                @if($jobPost->contact_email || $jobPost->contact_phone)
                    <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Thông tin liên hệ</h2>
                        <div class="space-y-2">
                            @if($jobPost->contact_email)
                                <p class="flex items-center gap-2 text-gray-700">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <a href="mailto:{{ $jobPost->contact_email }}" class="text-green-700 hover:underline">{{ $jobPost->contact_email }}</a>
                                </p>
                            @endif
                            @if($jobPost->contact_phone)
                                <p class="flex items-center gap-2 text-gray-700">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <a href="tel:{{ $jobPost->contact_phone }}" class="text-green-700 hover:underline">{{ $jobPost->contact_phone }}</a>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Meta Info -->
                <div class="text-sm text-gray-500 border-t pt-4">
                    <div class="flex flex-wrap gap-4">
                        <span>Đăng ngày: {{ $jobPost->published_at?->format('d/m/Y') }}</span>
                        @if($jobPost->expires_at)
                            <span>Hết hạn: {{ $jobPost->expires_at->format('d/m/Y') }}</span>
                        @endif
                        <span>Lượt xem: {{ $jobPost->views_count ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Form -->
        <div class="bg-white rounded-lg shadow mt-6 overflow-hidden" id="apply-form">
            <div class="bg-indigo-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Nộp hồ sơ ứng tuyển</h2>
                <p class="text-indigo-100 text-sm mt-1">Điền thông tin bên dưới để ứng tuyển vị trí này</p>
            </div>

            <div class="px-6 py-6">
                @auth
                    <form action="{{ route('jobs.apply', $jobPost) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <!-- User Info Display -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email đăng nhập</label>
                                <p class="text-gray-900 font-medium">{{ auth()->user()->email }}</p>
                            </div>
                            @if(auth()->user()->name)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Tên đăng nhập</label>
                                <p class="text-gray-900 font-medium">{{ auth()->user()->name }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chọn CV có sẵn</label>
                                <select name="cv_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">-- Chọn CV từ hồ sơ của bạn --</option>
                                    @php
                                        $cvs = \App\Models\Cv::where('user_id', auth()->id())->get();
                                    @endphp
                                    @foreach($cvs as $cv)
                                        <option value="{{ $cv->id }}">{{ $cv->title ?? 'CV #' . $cv->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tải lên CV (PDF, DOC, DOCX)</label>
                            <input type="file" name="cv_file" accept=".pdf,.doc,.docx" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-1">Kích thước tối đa: 5MB</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thư giới thiệu</label>
                            <textarea name="cover_letter" rows="4" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                placeholder="Giới thiệu về bản thân và lý do bạn phù hợp với vị trí này...">{{ old('cover_letter') }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Nộp hồ sơ
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Đăng nhập để ứng tuyển</h3>
                        <p class="mt-2 text-gray-500">Bạn cần đăng nhập để nộp hồ sơ ứng tuyển</p>
                        <div class="mt-6 flex justify-center gap-4">
                            <a href="{{ route('login') }}" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}" class="px-6 py-2 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition">
                                Đăng ký
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
