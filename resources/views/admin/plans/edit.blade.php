@extends('layouts.admin')
@section('title', 'Sửa – ' . $plan->name)
@section('page-title', 'Chỉnh sửa gói')

@section('breadcrumb')
<a href="{{ route('admin.plans.index') }}" class="text-gray-500 hover:text-gray-700">Gói dịch vụ</a>
<svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 font-semibold">{{ $plan->name }}</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.plans.update', $plan) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên gói</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $plan->slug) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giá (₫)</label>
                    <input type="number" name="price" value="{{ old('price', $plan->price) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giới hạn CV</label>
                    <input type="number" name="cv_limit" value="{{ old('cv_limit', $plan->cv_limit) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tính năng</label>
                <textarea name="features" rows="6" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-mono">{{ old('features', is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
            </div>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-medium text-gray-700">Đang hoạt động</span>
            </label>

            @if($plan->users()->count() > 0)
            <label class="flex items-center space-x-2 cursor-pointer bg-amber-50 border border-amber-200 rounded-lg p-3">
                <input type="checkbox" name="confirmed" value="1" class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                <span class="text-xs text-amber-800">Tôi hiểu việc tắt gói này sẽ ảnh hưởng <strong>{{ $plan->users()->count() }}</strong> người dùng.</span>
            </label>
            @endif

            <div class="flex space-x-3 pt-2 border-t border-gray-100">
                <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">Lưu thay đổi</button>
                <a href="{{ route('admin.plans.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
