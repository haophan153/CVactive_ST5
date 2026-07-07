@extends('layouts.admin')
@section('title', 'Thêm danh mục Blog')
@section('page-title', 'Thêm danh mục')

@section('breadcrumb')
<a href="{{ route('admin.blog-categories.index') }}" class="text-gray-500 hover:text-gray-700">Danh mục Blog</a>
<svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 font-semibold">Mới</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.blog-categories.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Màu sắc <span class="text-red-500">*</span></label>
                @php $palette = ['indigo', 'rose', 'amber', 'emerald', 'sky', 'violet', 'teal', 'fuchsia']; @endphp
                <div class="flex gap-2">
                    @foreach($palette as $p)
                    <label class="cursor-pointer">
                        <input type="radio" name="color" value="{{ $p }}" {{ old('color', 'indigo') === $p ? 'checked' : '' }} class="sr-only peer">
                        <span class="block w-10 h-10 rounded-full ring-2 ring-transparent peer-checked:ring-indigo-500 bg-{{ $p }}-500 hover:scale-110 transition"></span>
                    </label>
                    @endforeach
                </div>
                @error('color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">{{ old('description') }}</textarea>
            </div>
            <div class="flex space-x-3 pt-2 border-t border-gray-100">
                <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">Tạo</button>
                <a href="{{ route('admin.blog-categories.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
