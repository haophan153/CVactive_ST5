@extends('layouts.admin')
@section('title', 'Quản lý Blog')
@section('page-title', 'Blog')

@section('breadcrumb')
<span class="text-gray-900 font-semibold">Blog</span>
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
    <p class="text-sm text-gray-500">{{ $posts->total() }} bài viết</p>
    <a href="{{ route('admin.blog.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Viết bài mới</span>
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề..."
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái</label>
            <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tất cả</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã đăng</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Danh mục</label>
            <select name="category" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tất cả</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tác giả</label>
            <select name="author" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tất cả</option>
                @foreach($authors as $a)
                <option value="{{ $a->id }}" {{ request('author') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Từ ngày</label>
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Đến ngày</label>
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Sắp xếp</label>
            <select name="sort" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="latest" {{ request('sort','latest')==='latest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="oldest" {{ request('sort')==='oldest' ? 'selected' : '' }}>Cũ nhất</option>
                <option value="views"  {{ request('sort')==='views'  ? 'selected' : '' }}>Nhiều view</option>
                <option value="title"  {{ request('sort')==='title'  ? 'selected' : '' }}>Tiêu đề A→Z</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Lọc</button>
        @if(request()->hasAny(['search','status','category','author','from','to','sort']))
        <a href="{{ route('admin.blog.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200">Reset</a>
        @endif
    </form>
</div>

{{-- Bulk bar --}}
<form x-ref="form" method="POST" action="{{ route('admin.blog.bulk') }}" class="mb-4 flex items-center gap-2" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="">
    <template x-for="id in selected" :key="id">
        <input type="hidden" name="ids[]" :value="id">
    </template>
    <span class="text-sm text-gray-600" x-text="'Đã chọn ' + selected.length + ' bài:'"></span>
    <button type="button" @click="applyBulk('publish')" class="px-3 py-1.5 text-sm bg-green-50 text-green-700 rounded-lg hover:bg-green-100">Đăng</button>
    <button type="button" @click="applyBulk('unpublish')" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Hủy đăng</button>
    <button type="button" @click="applyBulk('delete')" class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-lg hover:bg-red-100">Xóa</button>
    <button type="button" @click="selected = []; selectAll = false; document.querySelectorAll('.post-checkbox').forEach(c => c.checked = false)" class="ml-auto px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg">Bỏ chọn</button>
</form>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 w-10">
                        <input type="checkbox" x-model="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Bài viết</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Danh mục</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tác giả</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Lượt xem</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <input type="checkbox" value="{{ $post->id }}" x-model="selected" class="post-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            @if($post->featured_image)
                            <img src="{{ asset('storage/'.$post->featured_image) }}" class="w-12 h-9 object-cover rounded flex-shrink-0">
                            @else
                            <div class="w-12 h-9 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center text-gray-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 truncate max-w-xs">{{ $post->title }}</p>
                                <p class="text-xs text-gray-400">{{ $post->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $post->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $post->author->name }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $post->status === 'published' ? 'Đã đăng' : 'Nháp' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ number_format($post->views_count) }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            @if($post->status === 'published')
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition" title="Xem">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            @endif
                            <a href="{{ route('admin.blog.edit', $post) }}"
                                class="p-1.5 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition" title="Sửa">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                                onsubmit="return confirm('Xóa bài viết này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Chưa có bài viết nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($posts->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $posts->links() }}</div>
    @endif
</div>
</div>
@endsection
