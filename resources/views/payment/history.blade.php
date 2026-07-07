<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">Lịch sử thanh toán</h2>
                <p class="text-xs text-slate-400 font-medium">Theo dõi các giao dịch và gói dịch vụ của bạn</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- ── Current plan card ─────────────────────────────────── --}}
        @php $user = auth()->user(); @endphp
        @if($user->plan)
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 shadow-xl shadow-slate-900/20 overflow-hidden relative">
                <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-blue-600/10 blur-2xl"></div>
                <div class="absolute -bottom-8 -left-8 w-32 h-32 rounded-full bg-emerald-600/10 blur-2xl"></div>
                <div class="relative flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/20 border border-blue-400/20 flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Gói hiện tại</span>
                                @if($user->plan_expires_at && $user->plan_expires_at->isFuture() && $user->plan_expires_at->diffInDays(now()) <= 7)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-400 bg-amber-400/10 border border-amber-400/20 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                        Sắp hết hạn
                                    </span>
                                @endif
                            </div>
                            <p class="text-xl font-black text-white">{{ $user->plan->name }}</p>
                            @if($user->plan_expires_at)
                                <p class="text-sm text-slate-400 mt-0.5">
                                    @if($user->plan_expires_at->isFuture())
                                        Hết hạn: {{ $user->plan_expires_at->format('d/m/Y') }}
                                        ({{ $user->plan_expires_at->diffInDays(now()) }} ngày còn lại)
                                    @else
                                        Đã hết hạn {{ $user->plan_expires_at->diffForHumans() }}
                                    @endif
                                </p>
                            @else
                                <p class="text-sm text-slate-400 mt-0.5">Không giới hạn thời gian</p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('pricing') }}"
                        class="shrink-0 inline-flex items-center gap-2 bg-white text-slate-900 font-bold text-sm px-5 py-2.5 rounded-xl hover:bg-slate-100 active:scale-[0.98] transition shadow-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Gia hạn / Nâng cấp
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Bạn đang dùng gói Free</p>
                        <p class="text-sm text-slate-500">Nâng cấp để mở khóa toàn bộ tính năng</p>
                    </div>
                </div>
                <a href="{{ route('pricing') }}"
                    class="shrink-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-lg shadow-blue-900/20 active:scale-[0.98]">
                    Nâng cấp ngay
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif

        {{-- ── Transactions ─────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Lịch sử giao dịch</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $payments->total() }} giao dịch</p>
                </div>
                @if($payments->isNotEmpty())
                    <div class="flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $payments->where('status', 'completed')->count() }} thành công
                    </div>
                @endif
            </div>

            @if($payments->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-9 h-9 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-2">Chưa có giao dịch nào</h4>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto mb-6">Mua gói Premium để mở khóa các tính năng cao cấp và sử dụng không giới hạn.</p>
                    <a href="{{ route('pricing') }}"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-lg shadow-blue-900/20 active:scale-[0.98]">
                        Xem bảng giá
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50/70 border-b border-slate-100">
                            <tr>
                                <th class="text-left px-5 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Mã GD</th>
                                <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Gói</th>
                                <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Phương thức</th>
                                <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Số tiền</th>
                                <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Trạng thái</th>
                                <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Ngày</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($payments as $payment)
                            @php
                                $stMap = [
                                    'completed' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'icon' => 'M5 13l4 4L19 7', 'label' => 'Thành công'],
                                    'pending'   => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700',    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Chờ xử lý'],
                                    'failed'    => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700',        'icon' => 'M6 18L18 6M6 6l12 12', 'label' => 'Thất bại'],
                                    'refunded'  => ['bg' => 'bg-slate-50 border-slate-200 text-slate-600',      'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'label' => 'Hoàn tiền'],
                                ];
                                $st = $stMap[$payment->status] ?? ['bg' => 'bg-slate-50 border-slate-200 text-slate-600', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => $payment->status];
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition group">
                                <td class="px-5 py-4 font-mono text-xs text-slate-400 font-medium">
                                    #{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-bold text-slate-900">{{ $payment->plan->name ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-slate-600 font-medium">
                                        @if(strtolower($payment->payment_method) === 'vnpay')
                                            <span class="w-5 h-5 rounded bg-blue-600 flex items-center justify-center text-white text-[8px] font-black">V</span>
                                        @elseif(strtolower($payment->payment_method) === 'momo')
                                            <span class="w-5 h-5 rounded bg-pink-500 flex items-center justify-center text-white text-[8px] font-black">M</span>
                                        @else
                                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        @endif
                                        {{ strtoupper($payment->payment_method) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-black text-slate-900 tabular-nums">{{ number_format($payment->amount, 0, ',', '.') }}<span class="text-xs font-medium text-slate-400">đ</span></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] px-2.5 py-1 font-bold rounded-full border {{ $st['bg'] }}">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $st['icon'] }}"/></svg>
                                        {{ $st['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-slate-400 text-xs whitespace-nowrap">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($payments->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/40">
                    {{ $payments->links() }}
                </div>
                @endif
            @endif
        </div>

        {{-- ── Help note ───────────────────────────────────────── --}}
        <div class="text-center pb-4">
            <p class="text-xs text-slate-400 inline-flex items-center gap-1.5">
                Cần hỗ trợ về thanh toán?
                <a href="{{ route('contact') }}" class="text-blue-600 hover:text-blue-700 font-semibold underline underline-offset-2 transition">Liên hệ chúng tôi</a>
            </p>
        </div>
    </div>
</x-app-layout>
