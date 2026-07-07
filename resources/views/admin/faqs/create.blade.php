@extends('layouts.admin')
@section('title', 'Thêm FAQ')
@section('page-title', 'Thêm FAQ')

@section('breadcrumb')
<a href="{{ route('admin.faqs.index') }}" class="text-gray-500 hover:text-gray-700">FAQ</a>
<svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 font-semibold">Mới</span>
@endsection

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Câu hỏi <span class="text-red-500">*</span></label>
                <input type="text" name="question" value="{{ old('question') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('question')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Câu trả lời <span class="text-red-500">*</span></label>
                <textarea name="answer" rows="6" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('answer') }}</textarea>
                @error('answer')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                        @foreach(\App\Models\Faq::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm" placeholder="Tự động">
                </div>
            </div>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-medium text-gray-700">Hiển thị</span>
            </label>
            <div class="flex space-x-3 pt-2 border-t border-gray-100">
                <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">Tạo</button>
                <a href="{{ route('admin.faqs.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
