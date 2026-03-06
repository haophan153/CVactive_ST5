<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chuyển khoản ngân hàng</h2>
</x-slot>

<div class="py-12">
<div class="max-w-2xl mx-auto px-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="bg-blue-600 p-6 text-white">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold">Thông tin chuyển khoản</h1>
                    <p class="text-blue-200 text-sm">Mã đơn hàng: #{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 flex items-start space-x-3">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-yellow-700">Tài khoản sẽ được nâng cấp sau khi chúng tôi xác nhận giao dịch (1–2 giờ trong giờ làm việc). Vui lòng giữ lại biên lai.</p>
            </div>

            {{-- Bank Info --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                @php
                $bankInfo = [
                    ['label' => 'Ngân hàng',         'value' => 'Vietcombank (VCB)'],
                    ['label' => 'Số tài khoản',      'value' => '1234567890'],
                    ['label' => 'Chủ tài khoản',     'value' => 'CONG TY CVACTIVE'],
                    ['label' => 'Chi nhánh',         'value' => 'Hà Nội'],
                    ['label' => 'Số tiền',           'value' => number_format($payment->amount, 0, ',', '.') . '₫'],
                    ['label' => 'Nội dung CK',       'value' => 'CVactive ' . str_pad($payment->id, 8, '0', STR_PAD_LEFT)],
                ];
                @endphp
                @foreach($bankInfo as $item)
                <div class="bg-gray-50 rounded-xl p-4 group relative">
                    <p class="text-xs text-gray-400 mb-1">{{ $item['label'] }}</p>
                    <p class="font-semibold text-gray-900 {{ in_array($item['label'], ['Số tài khoản','Nội dung CK','Số tiền']) ? 'text-indigo-700 font-mono' : '' }}">
                        {{ $item['value'] }}
                    </p>
                    @if(in_array($item['label'], ['Số tài khoản','Nội dung CK']))
                    <button onclick="navigator.clipboard.writeText('{{ $item['value'] }}'); this.textContent='✓ Đã copy'"
                        class="mt-2 text-xs text-indigo-500 hover:text-indigo-700 transition">
                        Copy
                    </button>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- QR Code Placeholder --}}
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center mb-6">
                <p class="text-sm text-gray-400 mb-2">Mã QR chuyển khoản tự động</p>
                <div class="w-32 h-32 bg-gray-100 rounded-xl mx-auto flex items-center justify-center text-gray-300">
                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <p class="text-xs text-gray-400 mt-2">Quét bằng app ngân hàng để điền tự động</p>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('dashboard') }}" class="flex-1 text-center py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                    Tôi đã chuyển khoản
                </a>
                <a href="{{ route('pricing') }}" class="flex-1 text-center py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                    Đổi phương thức
                </a>
            </div>
        </div>
    </div>
</div>
</div>
</x-app-layout>
