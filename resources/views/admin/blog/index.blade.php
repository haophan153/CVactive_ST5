@extends('layouts.admin')
@section('title', 'Quản lý Blog')
@section('page-title', 'Blog')

@section('breadcrumb')
<span class="text-slate-900 font-bold">Blog</span>
@endsection

@section('content')

<div x-data="{ selected: [], selectAll: false, applyBulk(action) {
    if (this.selected.length === 0) { alert('Chọn ít nhất 1 bài viết.'); return; }
    let msg = action === 'delete' ? 'Xóa ' + this.selected.length + ' bài viết?' : 'Cập nhật trạng thái cho ' + this.selected.length + ' bài viết?';
    if (!confirm(msg)) return;
    this.$refs.form.action.value = action;
    this.$refs.form.submit();
} }" x-init="$watch('selectAll', () => {
    document.querySelectorAll('.post-checkbox').forEach(c => { c.checked = selectAll; });
    this.selected = selectAll ? [...document.querySelectorAll('.post-checkbox')].map(c => c.value) : [];
})">

{{-- Toolbar --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <p class="text-sm font-semibold text-slate-500">{{ number_format($posts->total()) }} bài viết</p>
    <a href="{{ route('admin.blog.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Viết bài mới
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-52">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tìm kiếm</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề..."
                    class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all placeholder-slate-400">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Trạng thái</label>
            <select name="status" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã đăng</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Danh mục</label>
            <select name="category" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tác giả</label>
            <select name="author" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                @foreach($authors as $a)
                <option value="{{ $a->id }}" {{ request('author') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Từ ngày</label>
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Đến ngày</label>
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sắp xếp</label>
            <select name="sort" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="latest" {{ request('sort','latest')==='latest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="oldest" {{ request('sort')==='oldest' ? 'selected' : '' }}>Cũ nhất</option>
                <option value="views"  {{ request('sort')==='views'  ? 'selected' : '' }}>Nhiều view</option>
                <option value="title"  {{ request('sort')==='title'  ? 'selected' : '' }}>Tiêu đề A→Z</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">Lọc</button>
        @if(request()->hasAny(['search','status','category','author','from','to','sort']))
        <a href="{{ route('admin.blog.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-all">Reset</a>
        @endif
    </form>
</div>

{{-- Bulk bar --}}
<form x-ref="form" method="POST" action="{{ route('admin.blog.bulk') }}" class="mb-4 flex items-center gap-3 p-3 bg-indigo-600/5 border border-indigo-200/60 rounded-2xl" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="">
    <template x-for="id in selected" :key="id">
        <input type="hidden" name="ids[]" :value="id">
    </template>
    <span class="text-sm font-semibold text-indigo-700" x-text="'Đã chọn ' + selected.length + ' bài'"></span>
    <div class="w-px h-5 bg-indigo-200"></div>
    <button type="button" @click="applyBulk('publish')" class="px-3 py-1.5 text-sm bg-emerald-50 text-emerald-700 font-semibold rounded-xl hover:bg-emerald-100 transition-colors">Đăng</button>
    <button type="button" @click="applyBulk('unpublish')" class="px-3 py-1.5 text-sm bg-slate-100 text-slate-600 font-semibold rounded-xl hover:bg-slate-200 transition-colors">Hủy đăng</button>
    <button type="button" @click="applyBulk('delete')" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 font-semibold rounded-xl hover:bg-red-100 transition-colors">Xóa</button>
    <button type="button" @click="selected = []; selectAll = false; document.querySelectorAll('.post-checkbox').forEach(c => c.checked = false)" class="ml-auto px-3 py-1.5 text-sm bg-white text-slate-500 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Bỏ chọn</button>
</form>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 w-10">
                        <input type="checkbox" x-model="selectAll" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Bài viết</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Danh mục</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Tác giả</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Lượt xem</th>
                    <th class="text-right px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($posts as $post)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-5 py-3.5">
                        <input type="checkbox" value="{{ $post->id }}" x-model="selected" class="post-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            @if($post->featured_image)
                            <img src="{{ asset('storage/'.$post->featured_image) }}" class="w-12 h-9 object-cover rounded-lg flex-shrink-0 shadow-sm">
                            @else
                            <div class="w-12 h-9 bg-slate-100 rounded-lg flex-shrink-0 flex items-center justify-center text-slate-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 truncate max-w-xs">{{ $post->title }}</p>
                                <p class="text-xs text-slate-400">{{ $post->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-slate-500 text-sm">{{ $post->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3.5 text-slate-500 text-sm">{{ $post->author->name }}</td>
                    <td class="px-4 py-3.5">
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $post->status === 'published' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                            {{ $post->status === 'published' ? 'Đã đăng' : 'Nháp' }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-slate-500 font-medium">{{ number_format($post->views_count) }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            @if($post->status === 'published')
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all" title="Xem">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            @endif
                            <a href="{{ route('admin.blog.edit', $post) }}"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Sửa">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                                onsubmit="return confirm('Xóa bài viết này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-16 text-center text-slate-400">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1"/></svg>
                    </div>
                    <p class="font-medium">Chưa có bài viết nào</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">{{ $posts->links() }}</div>
    @endif
</div>
</div>
@endsection
