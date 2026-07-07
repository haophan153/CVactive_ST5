<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-slate-800 leading-tight">Lich su thanh toan</h2>
</x-slot>

<div class="py-12">
<div class="max-w-4xl mx-auto px-6">

    {{-- Current plan banner --}}
    @php $user = auth()->user(); @endphp
    @if($user->plan)
    <div class="bg-[#0F172A] text-white rounded-2xl p-5 mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-500/30 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="font-bold text-lg">Goi hien tai: {{ $user->plan->name }}</p>
                @if($user->plan_expires_at)
                    <p class="text-sm text-indigo-200">
                        {{ $user->plan_expires_at->isFuture() ? 'Het han: ' . $user->plan_expires_at->format('d/m/Y') : 'Da het han ' . $user->plan_expires_at->diffForHumans() }}
                    </p>
                @else
                    <p class="text-sm text-indigo-200">Khong gioi han thoi gian</p>
                @endif
            </div>
        </div>
        <a href="{{ route('pricing') }}" class="shrink-0 px-4 py-2 bg-white text-indigo-700 font-bold text-sm rounded-xl hover:bg-indigo-50 transition shadow-sm active:scale-[0.98]">
            Gia han / Nang cap
        </a>
    </div>
    @else
    <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-6 flex items-center justify-between gap-4">
        <div>
            <p class="font-semibold text-slate-700">Ban dang dung goi Free</p>
            <p class="text-sm text-slate-500">Nang cap de mo khoa toan bo tinh nang</p>
        </div>
        <a href="{{ route('pricing') }}" class="shrink-0 px-4 py-2 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 transition shadow-sm">
            Nang cap ngay
        </a>
    </div>
    @endif

    {{-- Payments table --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-900">Lich su giao dich</h3>
        </div>

        @if($payments->isEmpty())
        <div class="px-6 py-12 text-center">
            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <p class="text-sm text-slate-400">Chua co giao dich nao.</p>
            <a href="{{ route('pricing') }}" class="mt-3 inline-block text-sm text-indigo-600 font-medium hover:underline">Xem bang gia</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Ma GD</th>
                        <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Goi</th>
                        <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Phuong thuc</th>
                        <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">So tien</th>
                        <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Trang thai</th>
                        <th class="text-left px-4 py-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Ngay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($payments as $payment)
                    @php
                        $statusColor = match($payment->status) {
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'failed'    => 'bg-rose-100 text-rose-700',
                            'refunded'  => 'bg-slate-100 text-slate-600',
                            default     => 'bg-slate-100 text-slate-600',
                        };
                        $statusLabel = match($payment->status) {
                            'completed' => 'Thanh cong',
                            'pending'   => 'Cho xu ly',
                            'failed'    => 'That bai',
                            'refunded'  => 'Hoan tien',
                            default     => $payment->status,
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-3 font-mono text-xs text-slate-400">
                            #{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-indigo-700">{{ $payment->plan->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ strtoupper($payment->payment_method) }}</td>
                        <td class="px-4 py-3 font-bold text-slate-900">{{ number_format($payment->amount, 0, ',', '.') }}d</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex text-[11px] px-2.5 py-1 font-bold rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $payments->links() }}
        </div>
        @endif
        @endif
    </div>

    <p class="text-center text-xs text-slate-400 mt-6">
        Can ho tro ve thanh toan?
        <a href="{{ route('contact') }}" class="text-indigo-500 hover:underline">Lien he chung toi</a>
    </p>
</div>
</div>
</x-app-layout>
