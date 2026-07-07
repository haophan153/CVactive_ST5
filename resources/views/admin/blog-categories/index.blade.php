@extends('layouts.admin')
@section('title', 'Danh mục Blog')
@section('page-title', 'Danh mục Blog')

@section('breadcrumb')
<span class="text-gray-900 font-semibold">Danh mục Blog</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-5">
    <p class="text-sm text-gray-500">Quản lý danh mục bài viết blog.</p>
    <a href="{{ route('admin.blog-categories.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Thêm danh mục
    </a>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($categories as $cat)
    @php $c = $cat->color_classes; @endphp
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div class="w-12 h-12 rounded-xl {{ $c['solid'] }} flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <span class="px-2 py-1 text-xs rounded-full {{ $c['bg'] }} {{ $c['text'] }} ring-1 {{ $c['ring'] }}">{{ $cat->posts_count }} bài</span>
        </div>
        <h3 class="font-bold text-lg text-gray-900 mt-3">{{ $cat->name }}</h3>
        <p class="text-xs text-gray-500 mt-1">/{{ $cat->slug }}</p>
        @if($cat->description)
        <p class="text-sm text-gray-600 mt-3 line-clamp-2">{{ $cat->description }}</p>
        @endif

        <div class="flex items-center justify-end gap-1 mt-4 pt-3 border-t border-gray-100">
            <a href="{{ route('admin.blog-categories.edit', $cat) }}" class="p-1.5 text-indigo-500 hover:bg-indigo-50 rounded">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
            @if($cat->posts_count === 0)
            <form action="{{ route('admin.blog-categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Xóa?')" class="inline">
                @csrf @method('DELETE')
                <button class="p-1.5 text-red-500 hover:bg-red-50 rounded">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/></svg>
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
        Chưa có danh mục nào. Tạo danh mục đầu tiên!
    </div>
    @endforelse
</div>

@endsection
