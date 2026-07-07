@extends('layouts.admin')
@section('title', 'Quản lý FAQ')
@section('page-title', 'FAQ')

@section('breadcrumb')
<span class="text-gray-900 font-semibold">FAQ</span>
@endsection

@section('content')

<div x-data="{ selected: [], applyBulk(action) {
    if (this.selected.length === 0) { alert('Chọn ít nhất 1.'); return; }
    if (!confirm('Áp dụng cho ' + this.selected.length + '?')) return;
    this.$refs.form.action.value = action;
    this.$refs.form.submit();
} }">

<div class="flex items-center justify-between mb-5">
    <p class="text-sm text-gray-500">Sắp xếp & nhóm theo danh mục.</p>
    <a href="{{ route('admin.faqs.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Thêm FAQ
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Câu hỏi / câu trả lời..."
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Danh mục</label>
            <select name="category" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                @foreach(\App\Models\Faq::CATEGORIES as $key => $label)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Lọc</button>
    </form>
</div>

<form x-ref="form" method="POST" action="{{ route('admin.faqs.bulk') }}" class="mb-3" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="">
    <div class="flex items-center gap-2">
        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
        <span class="text-sm text-gray-600" x-text="'Đã chọn ' + selected.length + ':'"></span>
        <button type="button" @click="applyBulk('activate')" class="px-3 py-1.5 text-sm bg-green-50 text-green-700 rounded-lg hover:bg-green-100">Kích hoạt</button>
        <button type="button" @click="applyBulk('deactivate')" class="px-3 py-1.5 text-sm bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100">Tắt</button>
        <button type="button" @click="applyBulk('delete')" class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-lg hover:bg-red-100">Xóa</button>
        <button type="button" @click="selected=[]; document.querySelectorAll('.faq-checkbox').forEach(c => c.checked=false)" class="ml-auto px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg">Bỏ chọn</button>
    </div>
</form>

@forelse($faqs as $category => $items)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                {{ \App\Models\Faq::CATEGORIES[$category] ?? $category }}
                <span class="text-xs text-gray-400 font-normal">({{ $items->count() }})</span>
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($items as $faq)
            <div class="flex items-start gap-3 px-5 py-4 hover:bg-gray-50 transition">
                <input type="checkbox" value="{{ $faq->id }}" x-model="selected" class="faq-checkbox mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-gray-400">#{{ $faq->sort_order }}</span>
                        <p class="font-medium text-gray-900">{{ $faq->question }}</p>
                        @if(!$faq->is_active)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500">Đã ẩn</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($faq->answer, 200) }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ number_format($faq->views_count) }} lượt xem</p>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="toggleFaq({{ $faq->id }})" class="p-1.5 rounded transition {{ $faq->is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="p-1.5 text-indigo-500 hover:bg-indigo-50 rounded">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Xóa?')" class="inline">
                        @csrf @method('DELETE')
                        <button class="p-1.5 text-red-500 hover:bg-red-50 rounded">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@empty
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">Chưa có FAQ nào.</div>
@endforelse
</div>

<script>
async function toggleFaq(id) {
    await fetch(`{{ url('admin/faqs') }}/${id}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    });
    location.reload();
}
</script>
@endsection
