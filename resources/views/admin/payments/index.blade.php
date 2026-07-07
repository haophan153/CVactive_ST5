@extends('layouts.admin')
@section('title', 'Quản lý Thanh toán')
@section('page-title', 'Thanh toán & Doanh thu')

@section('breadcrumb')
<span class="text-slate-900 font-bold">Thanh toán</span>
@endsection

@section('content')

{{-- Revenue Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-2xl p-5 shadow-lg shadow-emerald-500/20">
        <p class="text-xs opacity-80 font-medium">Tổng doanh thu</p>
        <p class="text-xl font-extrabold mt-1">{{ number_format($revenueStats['total'], 0, ',', '.') }}₫</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Tháng này</p>
        <p class="text-xl font-extrabold text-indigo-600 mt-1">{{ number_format($revenueStats['this_month'], 0, ',', '.') }}₫</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Hôm nay</p>
        <p class="text-xl font-extrabold text-blue-600 mt-1">{{ number_format($revenueStats['today'], 0, ',', '.') }}₫</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">GD thành công</p>
        <p class="text-xl font-extrabold text-violet-600 mt-1">{{ number_format($revenueStats['count']) }}</p>
        <p class="text-[10px] text-slate-400 mt-0.5">TB: {{ number_format($revenueStats['avg'], 0, ',', '.') }}₫/GD</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Conversion</p>
        <p class="text-xl font-extrabold {{ $revenueStats['conversion'] >= 50 ? 'text-emerald-600' : ($revenueStats['conversion'] >= 25 ? 'text-amber-600' : 'text-red-500') }} mt-1">{{ $revenueStats['conversion'] }}%</p>
        <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
            <div class="h-1.5 rounded-full {{ $revenueStats['conversion'] >= 50 ? 'bg-emerald-500' : ($revenueStats['conversion'] >= 25 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min(100, $revenueStats['conversion']) }}%"></div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-5 mb-6">

    {{-- Revenue chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-slate-900">Doanh thu 6 tháng gần nhất</h3>
                <p class="text-xs text-slate-400 mt-0.5">Cột highlight = tháng hiện tại</p>
            </div>
            <span class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">{{ number_format($revenueStats['this_month'], 0, ',', '.') }}₫ tháng này</span>
        </div>
        @php $maxRev = $monthlyRevenue->max('total') ?: 1; @endphp
        <div class="flex items-end space-x-4 h-44">
            @foreach($monthlyRevenue as $month)
                @php
                    $h = $maxRev > 0 ? max(8, ($month->total / $maxRev) * 100) : 8;
                    $isCurrent = $month->month === now()->format('Y-m');
                @endphp
                <div class="flex-1 flex flex-col items-center">
                    <span class="text-xs font-bold mb-1.5 {{ $isCurrent ? 'text-indigo-600' : 'text-slate-500' }}">{{ number_format($month->total / 1000, 1) }}K</span>
                    <div class="w-full rounded-t-xl relative overflow-hidden transition hover:opacity-80"
                        style="height: {{ $h }}%; background: {{ $isCurrent ? 'linear-gradient(180deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(180deg, #34d399 0%, #059669 100%)' }}">
                        <span class="absolute top-0 left-0 right-0 h-1 bg-white/30"></span>
                    </div>
                    <span class="text-xs mt-1.5 {{ $isCurrent ? 'font-bold text-indigo-700' : 'text-slate-400' }}">{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('m/Y') }}</span>
                    <span class="text-[10px] text-slate-400">{{ $month->count }} GD</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Top plans --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-slate-900">Top gói bán chạy</h3>
        </div>
        <div class="space-y-4">
            @forelse($topPlans as $i => $tp)
            @php $maxCount = $topPlans->max('count') ?: 1; $pct = round($tp->count / $maxCount * 100); @endphp
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        @if($i === 0) <span class="text-amber-500">★</span> @endif
                        {{ $tp->plan->name ?? '—' }}
                    </span>
                    <span class="text-slate-500 text-xs font-semibold">{{ $tp->count }} GD</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-all" style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Doanh thu: <strong class="text-emerald-600">{{ number_format($tp->revenue, 0, ',', '.') }}₫</strong></p>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-4">Chưa có dữ liệu.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-52">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tìm kiếm</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên / email người dùng..."
                    class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all placeholder-slate-400">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Trạng thái</label>
            <select name="status" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Thành công</option>
                <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Thất bại</option>
                <option value="refunded"  {{ request('status') === 'refunded'  ? 'selected' : '' }}>Hoàn tiền</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Gói</label>
            <select name="plan" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả gói</option>
                @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
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
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">Lọc</button>
        <a href="{{ route('admin.payments.export', request()->all()) }}" class="flex items-center gap-1.5 px-4 py-2 bg-emerald-50 text-emerald-700 text-sm font-bold rounded-xl hover:bg-emerald-100 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Xuất CSV
        </a>
    </form>
</div>

<div x-data="{ selected: [], applyBulk(action, value = '') {
    if (this.selected.length === 0) { alert('Chọn ít nhất 1 giao dịch.'); return; }
    if (!confirm('Áp dụng cho ' + this.selected.length + ' giao dịch?')) return;
    this.$refs.form.action.value = action;
    this.$refs.form.value.value = value;
    this.$refs.form.submit();
} }">

{{-- Bulk --}}
<form x-ref="form" method="POST" action="{{ route('admin.payments.bulk-status') }}" class="mb-3 flex items-center gap-3 p-3 bg-indigo-600/5 border border-indigo-200/60 rounded-2xl" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="bulk-status">
    <input type="hidden" name="value" value="">
    <template x-for="id in selected" :key="id">
        <input type="hidden" name="ids[]" :value="id">
    </template>
    <span class="text-sm font-semibold text-indigo-700" x-text="'Đã chọn ' + selected.length + ' giao dịch'"></span>
    <div class="w-px h-5 bg-indigo-200"></div>
    <select @change="applyBulk('status', $event.target.value); $event.target.value=''" class="text-sm bg-white border border-slate-200 rounded-xl px-3 py-1.5 font-medium">
        <option value="">Đổi trạng thái...</option>
        <option value="pending">Chờ xử lý</option>
        <option value="completed">Thành công</option>
        <option value="failed">Thất bại</option>
        <option value="refunded">Hoàn tiền</option>
    </select>
    <button type="button" @click="selected=[]; document.querySelectorAll('.pay-checkbox').forEach(c => c.checked = false)" class="ml-auto px-3 py-1.5 text-sm bg-white text-slate-500 border border-slate-200 rounded-xl hover:bg-slate-50">Bỏ chọn</button>
</form>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-900">{{ number_format($payments->total()) }} giao dịch</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 w-10">
                        <input type="checkbox" @change="
                            this.checked ? selected = [...document.querySelectorAll('.pay-checkbox')].map(c => c.value) : selected = [];
                            document.querySelectorAll('.pay-checkbox').forEach(c => c.checked = this.checked);
                        " class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Người dùng</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Gói</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Phương thức</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Số tiền</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Ngày</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($payments as $payment)
                @php
                    $statusColor = match($payment->status) {
                        'completed' => 'bg-emerald-50 text-emerald-600',
                        'pending'   => 'bg-amber-50 text-amber-600',
                        'failed'    => 'bg-red-50 text-red-600',
                        'refunded'  => 'bg-slate-100 text-slate-600',
                        default     => 'bg-slate-100 text-slate-600',
                    };
                    $statusLabel = match($payment->status) {
                        'completed' => 'Thành công', 'pending' => 'Chờ xử lý',
                        'failed' => 'Thất bại', 'refunded' => 'Hoàn tiền', default => $payment->status,
                    };
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-5 py-3.5">
                        <input type="checkbox" value="{{ $payment->id }}" x-model="selected" class="pay-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-3.5">
                        <p class="font-bold text-slate-900">{{ $payment->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $payment->user->email }}</p>
                    </td>
                    <td class="px-4 py-3.5 text-slate-700 font-bold">{{ $payment->plan->name }}</td>
                    <td class="px-4 py-3.5 text-slate-500 text-sm">{{ $payment->payment_method }}</td>
                    <td class="px-4 py-3.5 font-bold text-slate-900">{{ number_format($payment->amount, 0, ',', '.') }}₫</td>
                    <td class="px-4 py-3.5">
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-slate-400 text-xs">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-16 text-center text-slate-400">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <p class="font-medium">Chưa có giao dịch nào</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">{{ $payments->links() }}</div>
    @endif
</div>
</div>
@endsection
