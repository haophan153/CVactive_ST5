@extends('layouts.app')

@section('title', 'Hồ sơ ứng tuyển - CVactive')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Hồ sơ ứng tuyển</h1>
            <p class="mt-1 text-sm text-gray-600">Theo dõi trạng thái các đơn ứng tuyển của bạn</p>
        </div>

        @if($applications->count() > 0)
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $applications->total() }}</p>
                    <p class="text-sm text-gray-500">Tổng đơn</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ $applications->where('status', 'pending')->count() }}</p>
                    <p class="text-sm text-gray-500">Chờ duyệt</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $applications->where('status', 'reviewing')->count() }}</p>
                    <p class="text-sm text-gray-500">Đang xem xét</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $applications->where('status', 'approved')->count() }}</p>
                    <p class="text-sm text-gray-500">Đã được duyệt</p>
                </div>
            </div>

            <!-- Applications List -->
            <div class="space-y-4">
                @foreach($applications as $application)
                    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-md transition">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Job Info -->
                                <div class="flex-1">
                                    <div class="flex items-start gap-4">
                                        @if($application->jobPost->company_logo)
                                            <img src="{{ asset('storage/' . $application->jobPost->company_logo) }}" 
                                                 alt="{{ $application->jobPost->company_name }}"
                                                 class="w-12 h-12 object-contain rounded-lg bg-gray-50">
                                        @else
                                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('jobs.show', $application->jobPost) }}" class="hover:text-indigo-600">
                                                    {{ $application->jobPost->title }}
                                                </a>
                                            </h3>
                                            <p class="text-gray-600">{{ $application->jobPost->company_name ?: 'Công ty chưa cập nhật' }}</p>
                                            <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-500">
                                                @if($application->jobPost->location)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        </svg>
                                                        {{ $application->jobPost->location }}
                                                    </span>
                                                @endif
                                                @if($application->jobPost->job_type)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ $application->jobPost->job_type }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Actions -->
                                <div class="flex flex-col items-end gap-3">
                                    @switch($application->status)
                                        @case('pending')
                                            <span class="px-3 py-1 text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                Chờ duyệt
                                            </span>
                                            @break
                                        @case('reviewing')
                                            <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">
                                                Đang xem xét
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Đã được duyệt
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="px-3 py-1 text-sm font-medium bg-red-100 text-red-800 rounded-full">
                                                Từ chối
                                            </span>
                                            @break
                                    @endswitch

                                    <div class="text-sm text-gray-500">
                                        Nộp ngày {{ $application->applied_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>

                            <!-- CV Info -->
                            @if($application->cv || $application->cv_file)
                                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        @if($application->cv)
                                            <span>CV: {{ $application->cv->title ?? 'Hồ sơ CV #' . $application->cv->id }}</span>
                                        @elseif($application->cv_file)
                                            <span>CV đính kèm</span>
                                        @endif
                                    </div>
                                    @if($application->cv_file)
                                        <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank" 
                                           class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Tải xuống
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $applications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow py-16 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Bạn chưa ứng tuyển công việc nào</h3>
                <p class="mt-2 text-gray-500 max-w-sm mx-auto">
                    Hãy tìm kiếm công việc phù hợp và nộp hồ sơ để theo dõi trạng thái tại đây
                </p>
                <a href="{{ route('jobs.index') }}" class="mt-6 inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Tìm việc ngay
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
