<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thanh toán thành công</h2>
</x-slot>

<div class="py-12">
<div class="max-w-lg mx-auto px-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Success Banner --}}
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-center text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold">Thanh toán thành công!</h1>
            <p class="text-green-100 mt-1 text-sm">Gói {{ $payment->plan->name }} đã được kích hoạt</p>
        </div>

        {{-- Payment Info --}}
        <div class="p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Chi tiết giao dịch</h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Mã giao dịch</span>
                    <span class="font-mono font-medium text-gray-900 text-xs">{{ $payment->transaction_id ?? '#' . str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Gói dịch vụ</span>
                    <span class="font-semibold text-indigo-600">{{ $payment->plan->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Số tiền</span>
                    <span class="font-bold text-gray-900">{{ number_format($payment->amount, 0, ',', '.') }}₫</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Phương thức</span>
                    <span class="font-medium text-gray-700">{{ strtoupper($payment->payment_method) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500">Thời gian</span>
                    <span class="text-gray-700">{{ $payment->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Hiệu lực đến</span>
                    <span class="font-semibold text-green-600">{{ auth()->user()->plan_expires_at?->format('d/m/Y') ?? '—' }}</span>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <a href="{{ route('cv.create') }}"
                    class="block text-center w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                    🎉 Bắt đầu tạo CV ngay
                </a>
                <a href="{{ route('dashboard') }}"
                    class="block text-center w-full py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                    Về Dashboard
                </a>
                <a href="{{ route('payment.history') }}"
                    class="block text-center text-sm text-indigo-600 hover:underline">
                    Xem lịch sử thanh toán
                </a>
            </div>
        </div>
    </div>
</div>
</div>
</x-app-layout>
