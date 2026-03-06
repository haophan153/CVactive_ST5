<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thanh toán thất bại</h2>
</x-slot>

<div class="py-12">
<div class="max-w-lg mx-auto px-4 text-center">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Thanh toán không thành công</h1>
        <p class="text-gray-500 mb-2">Giao dịch của bạn đã bị hủy hoặc thất bại.</p>
        @if($reason ?? '')
        <p class="text-xs text-gray-400 mb-6">Mã lỗi: {{ $reason }}</p>
        @endif
        <p class="text-sm text-gray-500 mb-6">Không có khoản tiền nào bị trừ. Bạn có thể thử lại hoặc chọn phương thức khác.</p>

        <div class="space-y-3">
            <a href="{{ route('pricing') }}"
                class="block w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                Thử lại
            </a>
            <a href="{{ route('dashboard') }}"
                class="block w-full py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                Về Dashboard
            </a>
            <a href="{{ route('contact') }}" class="text-sm text-indigo-600 hover:underline">
                Liên hệ hỗ trợ
            </a>
        </div>
    </div>
</div>
</div>
</x-app-layout>
