@extends('layouts.admin')
@section('title', 'Hộp thư liên hệ')
@section('page-title', 'Hộp thư liên hệ')

@section('breadcrumb')
<span class="text-slate-900 font-bold">Liên hệ</span>
@endsection

@section('content')

<div x-data="{ selected: [], applyBulk(action) {
    if (this.selected.length === 0) { alert('Chọn ít nhất 1 liên hệ.'); return; }
    if (!confirm('Áp dụng cho ' + this.selected.length + ' liên hệ?')) return;
    this.$refs.form.action.value = action;
    this.$refs.form.submit();
} }">

{{-- Stat cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Tổng</p>
        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-rose-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Chưa đọc</p>
        <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ number_format($stats['unread']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-emerald-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Hôm nay</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['today']) }}</p>
    </div>
</div>

{{-- Search --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-52">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tìm tên / email / subject</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập từ khóa..."
                    class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all placeholder-slate-400">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Trạng thái</label>
            <select name="is_read" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                <option value="unread" {{ request('is_read') === 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                <option value="read"   {{ request('is_read') === 'read'   ? 'selected' : '' }}>Đã đọc</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">Lọc</button>
    </form>
</div>

{{-- Bulk bar --}}
<form x-ref="form" method="POST" action="{{ route('admin.contacts.bulk') }}" class="mb-4 flex items-center gap-3 p-3 bg-indigo-600/5 border border-indigo-200/60 rounded-2xl" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="">
    <template x-for="id in selected" :key="id">
        <input type="hidden" name="ids[]" :value="id">
    </template>
    <span class="text-sm font-semibold text-indigo-700" x-text="'Đã chọn ' + selected.length + ' liên hệ'"></span>
    <div class="w-px h-5 bg-indigo-200"></div>
    <button type="button" @click="applyBulk('mark_read')" class="px-3 py-1.5 text-sm bg-emerald-50 text-emerald-700 font-semibold rounded-xl hover:bg-emerald-100 transition-colors">Đánh dấu đã đọc</button>
    <button type="button" @click="applyBulk('mark_unread')" class="px-3 py-1.5 text-sm bg-amber-50 text-amber-700 font-semibold rounded-xl hover:bg-amber-100 transition-colors">Đánh dấu chưa đọc</button>
    <button type="button" @click="applyBulk('delete')" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 font-semibold rounded-xl hover:bg-red-100 transition-colors">Xóa</button>
    <button type="button" @click="selected=[]; document.querySelectorAll('.contact-checkbox').forEach(c => c.checked=false)" class="ml-auto px-3 py-1.5 text-sm bg-white text-slate-500 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Bỏ chọn</button>
</form>

{{-- Inbox 2 col --}}
<div class="grid lg:grid-cols-5 gap-5">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-50 max-h-[640px] overflow-y-auto">
            @forelse($contacts as $c)
            <label class="flex items-start gap-3 px-4 py-3.5 hover:bg-slate-50 cursor-pointer transition {{ $selected && $selected->id === $c->id ? 'bg-indigo-50/60' : '' }}">
                <input type="checkbox" value="{{ $c->id }}" x-model="selected" class="contact-checkbox mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 flex-shrink-0">
                <a href="{{ route('admin.contacts.show', $c) }}" class="flex-1 min-w-0 block">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-bold {{ $c->is_read ? 'text-slate-600' : 'text-slate-900' }} truncate">{{ $c->name }}</p>
                        <span class="text-xs text-slate-400 flex-shrink-0">{{ $c->created_at->diffForHumans(null, true) }}</span>
                    </div>
                    <p class="text-xs text-slate-500 truncate mt-0.5 font-medium">{{ $c->subject }}</p>
                    <p class="text-xs text-slate-400 truncate mt-0.5">{{ $c->email }}</p>
                    @if(!$c->is_read)
                    <span class="inline-block mt-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
                    @endif
                </a>
            </label>
            @empty
            <p class="p-8 text-center text-slate-400 text-sm">Chưa có liên hệ nào.</p>
            @endforelse
        </div>
        @if($contacts->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $contacts->links() }}</div>
        @endif
    </div>

    <div class="lg:col-span-3">
        @if($selected)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Từ</p>
                    <p class="font-bold text-slate-900">{{ $selected->name }} <span class="text-slate-400 font-normal text-sm">&lt;{{ $selected->email }}&gt;</span></p>
                </div>
                <a href="mailto:{{ $selected->email }}?subject=Re: {{ rawurlencode($selected->subject) }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-indigo-500/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                    Trả lời
                </a>
            </div>
            <div class="p-6">
                <h2 class="text-lg font-extrabold text-slate-900">{{ $selected->subject }}</h2>
                <p class="text-xs text-slate-400 mt-1.5">{{ $selected->created_at->format('d/m/Y H:i') }}</p>
                <div class="mt-5 prose prose-sm max-w-none whitespace-pre-line text-slate-700 border-t pt-4 leading-relaxed">{{ $selected->message }}</div>
            </div>
            <div class="px-6 py-3.5 border-t border-slate-100 flex items-center justify-between bg-slate-50">
                <form action="{{ route('admin.contacts.toggle-read', $selected) }}" method="POST">
                    @csrf
                    <button class="text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors">{{ $selected->is_read ? 'Đánh dấu chưa đọc' : 'Đánh dấu đã đọc' }}</button>
                </form>
                <form action="{{ route('admin.contacts.destroy', $selected) }}" method="POST" onsubmit="return confirm('Xóa liên hệ này?')">
                    @csrf @method('DELETE')
                    <button class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors">Xóa</button>
                </form>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-16 text-center text-slate-400">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
            </div>
            <p class="font-semibold text-slate-500">Chọn một liên hệ để xem chi tiết</p>
        </div>
        @endif
    </div>
</div>
</div>

@endsection
