@extends('layouts.admin')
@section('title', 'Quản lý FAQ')
@section('page-title', 'FAQ')

@section('breadcrumb')
<span class="text-slate-900 font-bold">FAQ</span>
@endsection

@section('content')

<div x-data="{ selected: [], applyBulk(action) {
    if (this.selected.length === 0) { alert('Chọn ít nhất 1.'); return; }
    if (!confirm('Áp dụng cho ' + this.selected.length + '?')) return;
    this.$refs.form.action.value = action;
    this.$refs.form.submit();
} }">

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <p class="text-sm font-semibold text-slate-500">Sắp xếp & nhóm theo danh mục</p>
    <a href="{{ route('admin.faqs.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg> Thêm FAQ
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-52">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tìm kiếm</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Câu hỏi / câu trả lời..."
                    class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all placeholder-slate-400">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Danh mục</label>
            <select name="category" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                @foreach(\App\Models\Faq::CATEGORIES as $key => $label)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">Lọc</button>
    </form>
</div>

<form x-ref="form" method="POST" action="{{ route('admin.faqs.bulk') }}" class="mb-4 flex items-center gap-3 p-3 bg-indigo-600/5 border border-indigo-200/60 rounded-2xl" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="">
    <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
    <span class="text-sm font-semibold text-indigo-700" x-text="'Đã chọn ' + selected.length + ' FAQ'"></span>
    <div class="w-px h-5 bg-indigo-200"></div>
    <button type="button" @click="applyBulk('activate')" class="px-3 py-1.5 text-sm bg-emerald-50 text-emerald-700 font-semibold rounded-xl hover:bg-emerald-100 transition-colors">Kích hoạt</button>
    <button type="button" @click="applyBulk('deactivate')" class="px-3 py-1.5 text-sm bg-amber-50 text-amber-700 font-semibold rounded-xl hover:bg-amber-100 transition-colors">Tắt</button>
    <button type="button" @click="applyBulk('delete')" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 font-semibold rounded-xl hover:bg-red-100 transition-colors">Xóa</button>
    <button type="button" @click="selected=[]; document.querySelectorAll('.faq-checkbox').forEach(c => c.checked=false)" class="ml-auto px-3 py-1.5 text-sm bg-white text-slate-500 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Bỏ chọn</button>
</form>

@forelse($faqs as $category => $items)
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm mb-4 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h3 class="font-bold text-slate-900 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            {{ \App\Models\Faq::CATEGORIES[$category] ?? $category }}
            <span class="text-xs text-slate-400 font-semibold">({{ $items->count() }})</span>
        </h3>
    </div>
    <div class="divide-y divide-slate-50">
        @foreach($items as $faq)
        <div class="flex items-start gap-3 px-5 py-4 hover:bg-slate-50/60 transition-colors">
            <input type="checkbox" value="{{ $faq->id }}" x-model="selected" class="faq-checkbox mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 flex-shrink-0">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span class="text-[10px] font-extrabold text-slate-300">#{{ $faq->sort_order }}</span>
                    <p class="font-bold text-slate-900">{{ $faq->question }}</p>
                    @if(!$faq->is_active)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-500">Đã ẩn</span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed">{{ Str::limit($faq->answer, 200) }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ number_format($faq->views_count) }} lượt xem</p>
            </div>
            <div class="flex items-center gap-1 flex-shrink-0">
                <button onclick="toggleFaq({{ $faq->id }})" class="p-2 rounded-lg transition-all {{ $faq->is_active ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-100' }}" title="{{ $faq->is_active ? 'Tắt' : 'Kích hoạt' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
                <a href="{{ route('admin.faqs.edit', $faq) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Sửa">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Xóa?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-16 text-center text-slate-400">
    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <p class="font-semibold">Chưa có FAQ nào</p>
</div>
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