<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thanh toán bị hủy</h2>
</x-slot>

<div class="py-12">
<div class="max-w-lg mx-auto px-4 text-center">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Đã hủy thanh toán</h1>
        <p class="text-gray-500 mb-6">Bạn đã hủy giao dịch cho gói <strong>{{ $payment->plan->name }}</strong>. Không có khoản tiền nào bị trừ.</p>

        <div class="space-y-3">
            <a href="{{ route('payment.checkout', $payment->plan) }}"
                class="block w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                Thử lại
            </a>
            <a href="{{ route('dashboard') }}"
                class="block w-full py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                Về Dashboard
            </a>
        </div>
    </div>
</div>
</div>
</x-app-layout>
