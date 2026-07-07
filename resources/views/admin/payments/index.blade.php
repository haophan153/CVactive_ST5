@extends('layouts.admin')
@section('title', 'Quản lý Thanh toán')
@section('page-title', 'Thanh toán & Doanh thu')

@section('breadcrumb')
<span class="text-gray-900 font-semibold">Thanh toán</span>
@endsection

@section('content')

{{-- Revenue Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-xl p-5 shadow-sm">
        <p class="text-xs opacity-80">Tổng doanh thu</p>
        <p class="text-xl font-extrabold mt-1">{{ number_format($revenueStats['total'], 0, ',', '.') }}₫</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Tháng này</p>
        <p class="text-xl font-extrabold text-indigo-600 mt-1">{{ number_format($revenueStats['this_month'], 0, ',', '.') }}₫</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Hôm nay</p>
        <p class="text-xl font-extrabold text-blue-600 mt-1">{{ number_format($revenueStats['today'], 0, ',', '.') }}₫</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">GD thành công</p>
        <p class="text-xl font-extrabold text-purple-600 mt-1">{{ number_format($revenueStats['count']) }}</p>
        <p class="text-[10px] text-gray-400 mt-1">TB: {{ number_format($revenueStats['avg'], 0, ',', '.') }}₫/GD</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Conversion</p>
        <p class="text-xl font-extrabold {{ $revenueStats['conversion'] >= 50 ? 'text-emerald-600' : ($revenueStats['conversion'] >= 25 ? 'text-amber-600' : 'text-rose-600') }} mt-1">{{ $revenueStats['conversion'] }}%</p>
        <div class="w-full bg-gray-100 rounded-full h-1 mt-2">
            <div class="h-1 rounded-full {{ $revenueStats['conversion'] >= 50 ? 'bg-emerald-500' : ($revenueStats['conversion'] >= 25 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ min(100, $revenueStats['conversion']) }}%"></div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-5 mb-6">

    {{-- Revenue chart with gradient --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-900">Doanh thu 6 tháng gần nhất</h3>
                <p class="text-xs text-gray-400">Cột highlight = tháng hiện tại</p>
            </div>
            <span class="text-sm font-bold text-emerald-600">{{ number_format($revenueStats['this_month'], 0, ',', '.') }}₫ tháng này</span>
        </div>
        @php $maxRev = $monthlyRevenue->max('total') ?: 1; @endphp
        <div class="flex items-end space-x-3 h-44">
            @foreach($monthlyRevenue as $month)
                @php
                    $h = $maxRev > 0 ? max(6, ($month->total / $maxRev) * 100) : 6;
                    $isCurrent = $month->month === now()->format('Y-m');
                @endphp
                <div class="flex-1 flex flex-col items-center">
                    <span class="text-xs font-semibold mb-1 {{ $isCurrent ? 'text-indigo-600' : 'text-gray-600' }}">{{ number_format($month->total / 1000, 1) }}K</span>
                    <div class="w-full rounded-t-md relative overflow-hidden transition hover:opacity-90"
                        style="height: {{ $h }}%; background: {{ $isCurrent ? 'linear-gradient(180deg, #6366f1 0%, #4338ca 100%)' : 'linear-gradient(180deg, #34d399 0%, #059669 100%)' }}">
                        <span class="absolute top-0 left-0 right-0 h-1 bg-white/40"></span>
                    </div>
                    <span class="text-xs mt-1 {{ $isCurrent ? 'font-bold text-indigo-700' : 'text-gray-400' }}">{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('m/Y') }}</span>
                    <span class="text-[10px] text-gray-400">{{ $month->count }} GD</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Top plans --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Top gói bán chạy</h3>
        <div class="space-y-3">
            @forelse($topPlans as $i => $tp)
            @php $maxCount = $topPlans->max('count') ?: 1; $pct = round($tp->count / $maxCount * 100); @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700 font-medium flex items-center gap-1">
                        @if($i === 0) <span class="text-amber-500">★</span> @endif
                        {{ $tp->plan->name ?? '—' }}
                    </span>
                    <span class="text-gray-500 text-xs">{{ $tp->count }} GD</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500" style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400 mt-0.5">Doanh thu: <strong class="text-emerald-600">{{ number_format($tp->revenue, 0, ',', '.') }}₫</strong></p>
            </div>
            @empty
            <p class="text-sm text-gray-400">Chưa có dữ liệu.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Filters + Export --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên / email người dùng..."
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái</label>
            <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Thành công</option>
                <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Thất bại</option>
                <option value="refunded"  {{ request('status') === 'refunded'  ? 'selected' : '' }}>Hoàn tiền</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Gói</label>
            <select name="plan" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả gói</option>
                @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
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
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Lọc</button>
        <a href="{{ route('admin.payments.export', request()->all()) }}" class="px-4 py-2 bg-emerald-50 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-100 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Xuất CSV
        </a>
    </form>
</div>

{{-- Bulk --}}
<div x-data="{ selected: [], applyBulk(action, value = '') {
    if (this.selected.length === 0) { alert('Chọn ít nhất 1 giao dịch.'); return; }
    if (!confirm('Áp dụng cho ' + this.selected.length + ' giao dịch?')) return;
    this.$refs.form.action.value = action;
    this.$refs.form.value.value = value;
    this.$refs.form.submit();
} }">

<form x-ref="form" method="POST" action="{{ route('admin.payments.bulk-status') }}" class="mb-3 flex items-center gap-2" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="bulk-status">
    <input type="hidden" name="value" value="">
    <template x-for="id in selected" :key="id">
        <input type="hidden" name="ids[]" :value="id">
    </template>
    <span class="text-sm text-gray-600" x-text="'Đã chọn ' + selected.length + ' giao dịch:'"></span>
    <select @change="applyBulk('status', $event.target.value); $event.target.value=''" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
        <option value="">Đổi trạng thái...</option>
        <option value="pending">Chờ xử lý</option>
        <option value="completed">Thành công</option>
        <option value="failed">Thất bại</option>
        <option value="refunded">Hoàn tiền</option>
    </select>
    <button type="button" @click="selected=[]; document.querySelectorAll('.pay-checkbox').forEach(c => c.checked = false)" class="ml-auto px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg">Bỏ chọn</button>
</form>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">{{ number_format($payments->total()) }} giao dịch</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 w-10">
                        <input type="checkbox" @change="
                            this.checked ? selected = [...document.querySelectorAll('.pay-checkbox')].map(c => c.value) : selected = [];
                            document.querySelectorAll('.pay-checkbox').forEach(c => c.checked = this.checked);
                        " class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Người dùng</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Gói</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Phương thức</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Số tiền</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Ngày</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $payment)
                @php
                    $statusColor = match($payment->status) {
                        'completed' => 'bg-green-100 text-green-700',
                        'pending'   => 'bg-yellow-100 text-yellow-700',
                        'failed'    => 'bg-red-100 text-red-700',
                        'refunded'  => 'bg-gray-100 text-gray-600',
                        default     => 'bg-gray-100 text-gray-600',
                    };
                    $statusLabel = match($payment->status) {
                        'completed' => 'Thành công', 'pending' => 'Chờ xử lý',
                        'failed' => 'Thất bại', 'refunded' => 'Hoàn tiền', default => $payment->status,
                    };
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <input type="checkbox" value="{{ $payment->id }}" x-model="selected" class="pay-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">{{ $payment->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $payment->user->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-700 font-medium">{{ $payment->plan->name }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $payment->payment_method }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900">{{ number_format($payment->amount, 0, ',', '.') }}₫</td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">Chưa có giao dịch nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $payments->links() }}</div>
    @endif
</div>
</div>
@endsection
