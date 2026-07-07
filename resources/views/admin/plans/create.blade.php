@extends('layouts.admin')
@section('title', 'Thêm gói dịch vụ')
@section('page-title', 'Thêm gói dịch vụ')

@section('breadcrumb')
<a href="{{ route('admin.plans.index') }}" class="text-gray-500 hover:text-gray-700">Gói dịch vụ</a>
<svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 font-semibold">Gói mới</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên gói <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Pro" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="pro" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá (₫) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" required min="0" step="1000" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giới hạn CV</label>
                    <input type="number" name="cv_limit" value="{{ old('cv_limit') }}" min="0" placeholder="Không giới hạn" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Để trống = không giới hạn.</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tính năng (mỗi dòng 1 feature)</label>
                <textarea name="features" rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500" placeholder="Xuất PDF không giới hạn
Tùy chỉnh template cao cấp
Hỗ trợ ưu tiên 24/7">{{ old('features') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Mỗi dòng là một tính năng riêng biệt.</p>
            </div>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-medium text-gray-700">Đang hoạt động (hiển thị cho user)</span>
            </label>

            <div class="flex space-x-3 pt-2 border-t border-gray-100">
                <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">Tạo gói</button>
                <a href="{{ route('admin.plans.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
