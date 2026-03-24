@extends('layouts.app')

@section('title', 'Ứng viên: ' . $jobPost->title . ' - CVactive')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="{{ route('hr.job-posts.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại danh sách tin tuyển dụng
            </a>
        </div>

        <!-- Job Post Info -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $jobPost->title }}</h1>
                    <p class="text-gray-600">{{ $jobPost->company_name }}</p>
                    <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-500">
                        @if($jobPost->location)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                {{ $jobPost->location }}
                            </span>
                        @endif
                        @if($jobPost->job_type)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $jobPost->job_type }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-right flex items-center gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tổng ứng viên</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $applications->total() }}</p>
                    </div>
                    <a href="{{ route('hr.job-posts.search-cv', $jobPost) }}"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Tìm kiếm CV
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Chờ duyệt</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $applications->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Đang xem xét</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $applications->where('status', 'reviewing')->count() }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Đã duyệt</p>
                        <p class="text-2xl font-bold text-green-600">{{ $applications->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Từ chối</p>
                        <p class="text-2xl font-bold text-red-600">{{ $applications->where('status', 'rejected')->count() }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, email, số điện thoại..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="w-40">
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Đang xem xét</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                    Lọc
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('hr.job-posts.applications', $jobPost) }}" class="px-6 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                        Xóa lọc
                    </a>
                @endif
            </form>
        </div>

        <!-- Applications List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($applications->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ứng viên</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liên hệ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày nộp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($applications as $application)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <span class="text-indigo-600 font-medium">{{ substr($application->full_name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $application->full_name }}</div>
                                                @if($application->cv)
                                                    <div class="text-xs text-gray-500">CV: {{ $application->cv->title ?? '#'.$application->cv->id }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $application->email }}</div>
                                        @if($application->phone)
                                            <div class="text-sm text-gray-500">{{ $application->phone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $application->applied_at->format('d/m/Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $application->applied_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($application->status)
                                            @case('pending')
                                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Chờ duyệt</span>
                                                @break
                                            @case('reviewing')
                                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Đang xem xét</span>
                                                @break
                                            @case('approved')
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Đã duyệt</span>
                                                @break
                                            @case('rejected')
                                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Từ chối</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('hr.applications.show', $application) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $applications->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900">Chưa có đơn ứng tuyển nào</h3>
                    <p class="mt-2 text-sm text-gray-500">Các đơn ứng tuyển sẽ hiển thị ở đây khi có ứng viên nộp hồ sơ</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
