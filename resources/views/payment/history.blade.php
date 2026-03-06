<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lịch sử thanh toán</h2>
</x-slot>

<div class="py-12">
<div class="max-w-4xl mx-auto px-4">

    {{-- Current Plan Banner --}}
    @php $user = auth()->user(); @endphp
    @if($user->plan)
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-5 mb-6 text-white flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="font-bold text-lg">Gói hiện tại: {{ $user->plan->name }}</p>
                @if($user->plan_expires_at)
                <p class="text-indigo-200 text-sm">
                    {{ $user->plan_expires_at->isFuture() ? 'Hết hạn: ' . $user->plan_expires_at->format('d/m/Y') : '⚠️ Đã hết hạn ' . $user->plan_expires_at->diffForHumans() }}
                </p>
                @else
                <p class="text-indigo-200 text-sm">Không giới hạn thời gian</p>
                @endif
            </div>
        </div>
        <a href="{{ route('pricing') }}" class="flex-shrink-0 px-4 py-2 bg-white text-indigo-700 font-bold text-sm rounded-xl hover:bg-indigo-50 transition">
            Gia hạn / Nâng cấp
        </a>
    </div>
    @else
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 mb-6 flex items-center justify-between">
        <div>
            <p class="font-semibold text-gray-700">Bạn đang dùng gói Free</p>
            <p class="text-sm text-gray-500">Nâng cấp để mở khóa toàn bộ tính năng</p>
        </div>
        <a href="{{ route('pricing') }}" class="px-4 py-2 bg-indigo-600 text-white font-bold text-sm rounded-xl hover:bg-indigo-700 transition">
            Nâng cấp ngay
        </a>
    </div>
    @endif

    {{-- Payments Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Lịch sử giao dịch</h3>
        </div>

        @if($payments->isEmpty())
        <div class="px-6 py-12 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <p class="text-gray-400 text-sm">Chưa có giao dịch nào.</p>
            <a href="{{ route('pricing') }}" class="mt-4 inline-block text-sm text-indigo-600 font-medium hover:underline">Xem bảng giá →</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Mã GD</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Gói</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Phương thức</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Số tiền</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Ngày</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payments as $payment)
                    @php
                    $statusColor = match($payment->status) {
                        'completed' => 'bg-green-100 text-green-700',
                        'pending'   => 'bg-yellow-100 text-yellow-700',
                        'failed'    => 'bg-red-100 text-red-700',
                        'refunded'  => 'bg-gray-100 text-gray-600',
                        default     => 'bg-gray-100 text-gray-600',
                    };
                    $statusLabel = match($payment->status) {
                        'completed' => '✓ Thành công',
                        'pending'   => '⏳ Chờ xử lý',
                        'failed'    => '✗ Thất bại',
                        'refunded'  => '↩ Hoàn tiền',
                        default     => $payment->status,
                    };
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 font-mono text-xs text-gray-500">
                            #{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-indigo-700">{{ $payment->plan->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ strtoupper($payment->payment_method) }}</td>
                        <td class="px-4 py-3 font-bold text-gray-900">{{ number_format($payment->amount, 0, ',', '.') }}₫</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $payments->links() }}
        </div>
        @endif
        @endif
    </div>

    <p class="text-center text-xs text-gray-400 mt-4">
        Cần hỗ trợ về thanh toán?
        <a href="{{ route('contact') }}" class="text-indigo-500 hover:underline">Liên hệ chúng tôi</a>
    </p>
</div>
</div>
</x-app-layout>
