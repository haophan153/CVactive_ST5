@extends('layouts.admin')
@section('title', 'Danh mục Blog')
@section('page-title', 'Danh mục Blog')

@section('breadcrumb')
<span class="text-slate-900 font-bold">Danh mục Blog</span>
@endsection

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <p class="text-sm font-semibold text-slate-500">Quản lý danh mục bài viết blog</p>
    <a href="{{ route('admin.blog-categories.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg> Thêm danh mục
    </a>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($categories as $cat)
    @php $c = $cat->color_classes; @endphp
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 hover:shadow-lg hover:border-indigo-200/60 transition-all duration-200">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-xl {{ $c['solid'] }} flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $c['bg'] }} {{ $c['text'] }}">{{ $cat->posts_count }} bài</span>
        </div>
        <h3 class="font-extrabold text-slate-900 text-base mt-1">{{ $cat->name }}</h3>
        <p class="text-xs text-slate-400 font-medium mt-0.5">/{{ $cat->slug }}</p>
        @if($cat->description)
        <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">{{ $cat->description }}</p>
        @endif

        <div class="flex items-center justify-end gap-1 mt-4 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.blog-categories.edit', $cat) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Sửa">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
            @if($cat->posts_count === 0)
            <form action="{{ route('admin.blog-categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Xóa danh mục này?')" class="inline">
                @csrf @method('DELETE')
                <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-slate-200/80 shadow-sm p-16 text-center text-slate-400">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        </div>
        <p class="font-semibold">Chưa có danh mục nào. Tạo danh mục đầu tiên!</p>
    </div>
    @endforelse
</div>

@endsection