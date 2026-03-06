@extends('layouts.admin')
@section('title', 'Thêm Template')
@section('page-title', 'Thêm template mới')

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên template <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Classic Blue">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Blade view <span class="text-red-500">*</span></label>
                    <input type="text" name="blade_view" value="{{ old('blade_view') }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="cv-templates.classic-blue">
                    <p class="text-xs text-gray-400 mt-1">VD: cv-templates.my-template</p>
                    @error('blade_view')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Thumbnail upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                <div x-data="{ preview: null }" class="space-y-2">
                    <input type="file" name="thumbnail" accept="image/*" id="thumbnail-input"
                        @change="preview = URL.createObjectURL($event.target.files[0])"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <div x-show="preview" class="mt-2">
                        <img :src="preview" class="h-32 rounded-lg object-cover border border-gray-200">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP. Tỷ lệ A4 (3:4) được khuyến nghị.</p>
                @error('thumbnail')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center space-x-6">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    <span class="text-sm font-medium text-gray-700">Template Premium (PRO)</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-gray-700">Hiển thị công khai</span>
                </label>
            </div>

            <div class="flex space-x-3 pt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                    Tạo template
                </button>
                <a href="{{ route('admin.templates.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
