@extends('layouts.app')

@section('title', 'Đăng tin tuyển dụng')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('hr.job-posts.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại danh sách
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Đăng tin tuyển dụng</h1>

            <form method="POST" action="{{ route('hr.job-posts.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề tin tuyển dụng <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ví dụ: Tuyển dụng Lập trình viên PHP" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Mô tả công việc <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="6"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Mô tả chi tiết về công việc, yêu cầu, quyền lợi..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Job Type & Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="job_type" class="block text-sm font-medium text-gray-700">Loại hình công việc</label>
                            <select name="job_type" id="job_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Chọn loại hình</option>
                                <option value="full-time" {{ old('job_type') == 'full-time' ? 'selected' : '' }}>Toàn thời gian</option>
                                <option value="part-time" {{ old('job_type') == 'part-time' ? 'selected' : '' }}>Bán thời gian</option>
                                <option value="contract" {{ old('job_type') == 'contract' ? 'selected' : '' }}>Hợp đồng</option>
                                <option value="intern" {{ old('job_type') == 'intern' ? 'selected' : '' }}>Thực tập</option>
                                <option value="freelance" {{ old('job_type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                            </select>
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Địa điểm</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ví dụ: Hồ Chí Minh">
                        </div>
                    </div>

                    <!-- Salary -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mức lương (VND)</label>
                        <div class="grid grid-cols-2 gap-4 mt-1">
                            <div>
                                <input type="number" name="salary_min" value="{{ old('salary_min') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Từ">
                            </div>
                            <div>
                                <input type="number" name="salary_max" value="{{ old('salary_max') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Đến">
                            </div>
                        </div>
                    </div>

                    <!-- Company Info -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin công ty</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-700">Tên công ty</label>
                                <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="company_description" class="block text-sm font-medium text-gray-700">Giới thiệu công ty</label>
                                <textarea name="company_description" id="company_description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('company_description') }}</textarea>
                            </div>
                            <div>
                                <label for="company_logo" class="block text-sm font-medium text-gray-700">Logo công ty</label>
                                <input type="file" name="company_logo" id="company_logo" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin liên hệ</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700">Email liên hệ</label>
                                <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', auth()->user()->email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                                <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Expiry -->
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700">Ngày hết hạn</label>
                        <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <a href="{{ route('hr.job-posts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Hủy
                    </a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Lưu nháp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
