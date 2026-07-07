@extends('layouts.admin')
@section('title', 'Viết bài mới')
@section('page-title', 'Viết bài mới')

@section('breadcrumb')
<a href="{{ route('admin.blog.index') }}" class="text-gray-500 hover:text-gray-700">Blog</a>
<svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 font-semibold">Bài mới</span>
@endsection

@section('content')

<form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="grid lg:grid-cols-3 gap-5">

    {{-- Editor --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg font-semibold focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Nhập tiêu đề hấp dẫn...">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tóm tắt</label>
                <textarea name="excerpt" rows="2" maxlength="500"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                    placeholder="Mô tả ngắn...">{{ old('excerpt') }}</textarea>
                @error('excerpt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung <span class="text-red-500">*</span></label>
                <x-trix-editor name="content" :value="old('content')" placeholder="Bắt đầu viết bài..." />
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-4">Xuất bản</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Chọn --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Lưu nháp</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Đăng ngay</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-2">
                <button type="submit" name="status" value="published" class="py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                    Đăng bài
                </button>
                <button type="submit" name="status" value="draft" class="py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">
                    Lưu nháp
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5" x-data="{ preview: null }">
            <h3 class="font-semibold text-gray-900 mb-3">Ảnh đại diện</h3>
            <div x-show="!preview" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-indigo-400 transition"
                @click="$refs.imgInput.click()">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-xs text-gray-400">Click để chọn ảnh</p>
            </div>
            <div x-show="preview" class="relative">
                <img :src="preview" class="w-full rounded-lg object-cover aspect-video">
                <button type="button" @click="preview = null; $refs.imgInput.value = ''"
                    class="absolute top-2 right-2 bg-white rounded-full p-1 shadow text-gray-600 hover:text-red-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <input type="file" name="featured_image" accept="image/*" x-ref="imgInput" class="hidden"
                @change="preview = URL.createObjectURL($event.target.files[0])">
        </div>
    </div>
</div>
</form>
@endsection
