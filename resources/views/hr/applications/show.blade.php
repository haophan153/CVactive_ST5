@extends('layouts.admin')

@section('title', 'Chi tiết ứng viên - CVactive')

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="{{ route('hr.applications.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại danh sách ứng viên
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Application Info -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-indigo-600 px-6 py-4">
                        <h1 class="text-xl font-bold text-white">Thông tin ứng viên</h1>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="flex-shrink-0 h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-2xl text-indigo-600 font-bold">{{ substr($application->full_name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-gray-900">{{ $application->full_name }}</h2>
                                <p class="text-gray-600">{{ $application->email }}</p>
                                @if($application->phone)
                                    <p class="text-gray-600">{{ $application->phone }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vị trí ứng tuyển</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="font-medium text-gray-900">{{ $application->jobPost->title }}</p>
                                <p class="text-sm text-gray-600">{{ $application->jobPost->company_name }}</p>
                                <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-500">
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

                        @if($application->cover_letter)
                            <div class="border-t pt-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Thư giới thiệu</h3>
                                <div class="bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap">{{ $application->cover_letter }}</div>
                            </div>
                        @endif

                        @if($application->hasCvFile())
                            <div class="border-t pt-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">CV đính kèm</h3>
                                <a href="{{ route('hr.applications.cv.download', $application) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Tải xuống CV
                                </a>
                                <p class="text-xs text-gray-500 mt-2">
                                    <svg class="w-3 h-3 inline-block mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    File bảo mật - chỉ người được ủy quyền mới có thể tải
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h3 class="font-semibold text-gray-900">Trạng thái</h3>
                    </div>
                    <div class="px-6 py-6">
                        <form method="POST" action="{{ route('hr.applications.updateStatus', $application) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-4">
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                    <option value="reviewing" {{ $application->status == 'reviewing' ? 'selected' : '' }}>Đang xem xét</option>
                                    <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                    <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                                Cập nhật trạng thái
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h3 class="font-semibold text-gray-900">Ghi chú</h3>
                    </div>
                    <div class="px-6 py-6">
                        <form method="POST" action="{{ route('hr.applications.updateStatus', $application) }}">
                            @csrf
                            @method('PATCH')
                            <textarea name="notes" rows="4" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Thêm ghi chú về ứng viên...">{{ $application->notes }}</textarea>
                            <button type="submit" class="mt-3 w-full px-4 py-2 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-900 transition">
                                Lưu ghi chú
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Ngày nộp</p>
                                <p class="font-medium text-gray-900">{{ $application->applied_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Cập nhật cuối</p>
                                <p class="font-medium text-gray-900">{{ $application->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                            @if($application->user)
                                <div>
                                    <p class="text-sm text-gray-500">Tài khoản</p>
                                    <p class="font-medium text-gray-900">{{ $application->user->name ?? 'N/A' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Delete Card -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4">
                        <form method="POST" action="{{ route('hr.applications.destroy', $application) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn ứng tuyển này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-700 font-medium rounded-lg hover:bg-red-100 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Xóa đơn ứng tuyển
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
