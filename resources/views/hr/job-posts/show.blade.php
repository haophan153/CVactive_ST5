@extends('layouts.app')

@section('title', $jobPost->title)

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('hr.job-posts.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại danh sách
            </a>
            <div class="flex gap-2">
                @if($jobPost->status === 'draft')
                    <form action="{{ route('hr.job-posts.publish', $jobPost) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Đăng tin
                        </button>
                    </form>
                @elseif($jobPost->status === 'published')
                    <form action="{{ route('hr.job-posts.close', $jobPost) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Đóng tin
                        </button>
                    </form>
                @endif
                <a href="{{ route('hr.job-posts.edit', $jobPost) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Chỉnh sửa
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Header -->
            <div class="bg-indigo-600 px-6 py-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $jobPost->title }}</h1>
                        <div class="mt-2 flex items-center gap-4 text-indigo-100">
                            @if($jobPost->company_name)
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $jobPost->company_name }}
                                </span>
                            @endif
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
                                    @endswitch
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @switch($jobPost->status)
                            @case('draft')
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-200 text-gray-800">
                                    Nháp
                                </span>
                                @break
                            @case('published')
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-200 text-green-800">
                                    Đã đăng
                                </span>
                                @break
                            @case('closed')
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-200 text-red-800">
                                    Đã đóng
                                </span>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <!-- Salary -->
                @if($jobPost->salary_min || $jobPost->salary_max)
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Mức lương</h3>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($jobPost->salary_min && $jobPost->salary_max)
                                {{ number_format($jobPost->salary_min) }} - {{ number_format($jobPost->salary_max) }} {{ $jobPost->salary_currency }}
                            @elseif($jobPost->salary_min)
                                Từ {{ number_format($jobPost->salary_min) }} {{ $jobPost->salary_currency }}
                            @else
                                Up to {{ number_format($jobPost->salary_max) }} {{ $jobPost->salary_currency }}
                            @endif
                        </p>
                    </div>
                @endif

                <!-- Company Info -->
                @if($jobPost->company_name || $jobPost->company_description || $jobPost->company_logo)
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Về công ty</h2>
                        <div class="flex items-start gap-4">
                            @if($jobPost->company_logo)
                                <img src="{{ asset('storage/' . $jobPost->company_logo) }}" alt="Logo" class="w-16 h-16 object-contain rounded-lg">
                            @endif
                            <div>
                                @if($jobPost->company_name)
                                    <p class="font-medium text-gray-900">{{ $jobPost->company_name }}</p>
                                @endif
                                @if($jobPost->company_description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $jobPost->company_description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Job Description -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Mô tả công việc</h2>
                    <div class="prose max-w-none text-gray-600">
                        {!! nl2br(e($jobPost->description)) !!}
                    </div>
                </div>

                <!-- Contact Info -->
                @if($jobPost->contact_email || $jobPost->contact_phone)
                    <div class="mb-6 p-4 bg-indigo-50 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Thông tin liên hệ</h2>
                        <div class="space-y-2">
                            @if($jobPost->contact_email)
                                <p class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $jobPost->contact_email }}
                                </p>
                            @endif
                            @if($jobPost->contact_phone)
                                <p class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $jobPost->contact_phone }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Meta Info -->
                <div class="text-sm text-gray-500 border-t pt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-medium">Ngày tạo:</span> {{ $jobPost->created_at->format('d/m/Y H:i') }}
                        </div>
                        @if($jobPost->published_at)
                            <div>
                                <span class="font-medium">Ngày đăng:</span> {{ $jobPost->published_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        @if($jobPost->expires_at)
                            <div>
                                <span class="font-medium">Hết hạn:</span> {{ $jobPost->expires_at->format('d/m/Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-between items-center">
                <form action="{{ route('hr.job-posts.destroy', $jobPost) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tin này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900">
                        Xóa tin
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
