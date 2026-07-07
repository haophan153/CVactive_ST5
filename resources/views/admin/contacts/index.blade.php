@extends('layouts.admin')
@section('title', 'Hộp thư liên hệ')
@section('page-title', 'Hộp thư liên hệ')

@section('breadcrumb')
<span class="text-gray-900 font-semibold">Liên hệ</span>
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
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Tổng</p>
        <p class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-rose-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Chưa đọc</p>
        <p class="text-2xl font-extrabold text-rose-600">{{ number_format($stats['unread']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-emerald-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Hôm nay</p>
        <p class="text-2xl font-extrabold text-emerald-600">{{ number_format($stats['today']) }}</p>
    </div>
</div>

{{-- Search --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm tên / email / subject</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập từ khóa..."
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái</label>
            <select name="is_read" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                <option value="unread" {{ request('is_read') === 'unread' ? 'selected' : '' }}>Chưa đọc</option>
                <option value="read"   {{ request('is_read') === 'read'   ? 'selected' : '' }}>Đã đọc</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Lọc</button>
    </form>
</div>

{{-- Bulk bar --}}
<form x-ref="form" method="POST" action="{{ route('admin.contacts.bulk') }}" class="mb-3 flex items-center gap-2" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="">
    <template x-for="id in selected" :key="id">
        <input type="hidden" name="ids[]" :value="id">
    </template>
    <span class="text-sm text-gray-600" x-text="'Đã chọn ' + selected.length + ':'"></span>
    <button type="button" @click="applyBulk('mark_read')" class="px-3 py-1.5 text-sm bg-green-50 text-green-700 rounded-lg hover:bg-green-100">Đánh dấu đã đọc</button>
    <button type="button" @click="applyBulk('mark_unread')" class="px-3 py-1.5 text-sm bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100">Đánh dấu chưa đọc</button>
    <button type="button" @click="applyBulk('delete')" class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-lg hover:bg-red-100">Xóa</button>
    <button type="button" @click="selected=[]; document.querySelectorAll('.contact-checkbox').forEach(c => c.checked=false)" class="ml-auto px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg">Bỏ chọn</button>
</form>

{{-- Inbox 2 col --}}
<div class="grid lg:grid-cols-5 gap-5">
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-50 max-h-[640px] overflow-y-auto">
            @forelse($contacts as $c)
            <label class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition {{ $selected && $selected->id === $c->id ? 'bg-indigo-50' : '' }}">
                <input type="checkbox" value="{{ $c->id }}" x-model="selected" class="contact-checkbox mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <a href="{{ route('admin.contacts.show', $c) }}" class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium {{ $c->is_read ? 'text-gray-700' : 'text-gray-900 font-bold' }} truncate">{{ $c->name }}</p>
                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $c->created_at->diffForHumans(null, true) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 truncate mt-0.5">{{ $c->subject }}</p>
                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ $c->email }}</p>
                    @if(!$c->is_read)
                    <span class="inline-block mt-1 w-2 h-2 bg-rose-500 rounded-full"></span>
                    @endif
                </a>
            </label>
            @empty
            <p class="p-8 text-center text-gray-400 text-sm">Chưa có liên hệ nào.</p>
            @endforelse
        </div>
        @if($contacts->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 text-xs">{{ $contacts->links() }}</div>
        @endif
    </div>

    <div class="lg:col-span-3">
        @if($selected)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Từ</p>
                    <p class="font-bold text-gray-900">{{ $selected->name }} <span class="text-gray-400 font-normal">&lt;{{ $selected->email }}&gt;</span></p>
                </div>
                <a href="mailto:{{ $selected->email }}?subject=Re: {{ rawurlencode($selected->subject) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                    Trả lời
                </a>
            </div>
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900">{{ $selected->subject }}</h2>
                <p class="text-xs text-gray-400 mt-1">{{ $selected->created_at->format('d/m/Y H:i') }}</p>
                <div class="mt-4 prose prose-sm max-w-none whitespace-pre-line text-gray-700 border-t pt-4">{{ $selected->message }}</div>
            </div>
            <div class="px-6 py-3 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                <form action="{{ route('admin.contacts.toggle-read', $selected) }}" method="POST">
                    @csrf
                    <button class="text-sm text-gray-600 hover:text-gray-900">{{ $selected->is_read ? 'Đánh dấu chưa đọc' : 'Đánh dấu đã đọc' }}</button>
                </form>
                <form action="{{ route('admin.contacts.destroy', $selected) }}" method="POST" onsubmit="return confirm('Xóa liên hệ này?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-red-600 hover:text-red-800">Xóa</button>
                </form>
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
            Chọn một liên hệ để xem chi tiết.
        </div>
        @endif
    </div>
</div>
</div>

@endsection
