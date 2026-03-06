<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Thanh toán</h2>
</x-slot>

<div class="py-12">
<div class="max-w-4xl mx-auto px-4">

    {{-- Breadcrumb --}}
    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('pricing') }}" class="hover:text-indigo-600">Bảng giá</a>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">Thanh toán</span>
    </nav>

    <div class="grid lg:grid-cols-5 gap-6">

        {{-- Payment Methods --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Chọn phương thức thanh toán</h2>

                <form action="{{ route('payment.process', $plan) }}" method="POST" id="payment-form">
                    @csrf

                    <div class="space-y-3" x-data="{ method: 'vnpay' }">

                        {{-- VNPay --}}
                        <label class="block cursor-pointer">
                            <input type="radio" name="method" value="vnpay" x-model="method" class="sr-only">
                            <div :class="method === 'vnpay' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-300' : 'border-gray-200 hover:border-gray-300'"
                                class="flex items-center p-4 rounded-xl border-2 transition">
                                <div class="flex-1 flex items-center space-x-3">
                                    <div class="w-12 h-8 bg-red-600 rounded-md flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-xs font-extrabold tracking-tight">VNPay</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-800">VNPay QR</p>
                                        <p class="text-xs text-gray-400">Quét mã QR, ATM nội địa, Visa/Master</p>
                                    </div>
                                </div>
                                <div :class="method === 'vnpay' ? 'bg-indigo-600' : 'border-2 border-gray-300'"
                                    class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0">
                                    <div x-show="method === 'vnpay'" class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>

                        {{-- MoMo --}}
                        <label class="block cursor-pointer">
                            <input type="radio" name="method" value="momo" x-model="method" class="sr-only">
                            <div :class="method === 'momo' ? 'border-pink-500 bg-pink-50 ring-2 ring-pink-300' : 'border-gray-200 hover:border-gray-300'"
                                class="flex items-center p-4 rounded-xl border-2 transition">
                                <div class="flex-1 flex items-center space-x-3">
                                    <div class="w-12 h-8 bg-gradient-to-r from-pink-500 to-rose-600 rounded-md flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-xs font-extrabold">MoMo</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-800">Ví MoMo</p>
                                        <p class="text-xs text-gray-400">Thanh toán qua ứng dụng MoMo</p>
                                    </div>
                                </div>
                                <div :class="method === 'momo' ? 'bg-pink-500' : 'border-2 border-gray-300'"
                                    class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0">
                                    <div x-show="method === 'momo'" class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>

                        {{-- Bank Transfer --}}
                        <label class="block cursor-pointer">
                            <input type="radio" name="method" value="bank_transfer" x-model="method" class="sr-only">
                            <div :class="method === 'bank_transfer' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-300' : 'border-gray-200 hover:border-gray-300'"
                                class="flex items-center p-4 rounded-xl border-2 transition">
                                <div class="flex-1 flex items-center space-x-3">
                                    <div class="w-12 h-8 bg-blue-600 rounded-md flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-gray-800">Chuyển khoản ngân hàng</p>
                                        <p class="text-xs text-gray-400">Xác nhận thủ công trong 1–2 giờ làm việc</p>
                                    </div>
                                </div>
                                <div :class="method === 'bank_transfer' ? 'bg-blue-600' : 'border-2 border-gray-300'"
                                    class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0">
                                    <div x-show="method === 'bank_transfer'" class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </label>

                        <div class="pt-4">
                            <button type="submit"
                                class="w-full py-3.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition text-base flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Thanh toán {{ number_format($plan->price, 0, ',', '.') }}₫</span>
                            </button>
                            <p class="text-center text-xs text-gray-400 mt-3 flex items-center justify-center space-x-1">
                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Giao dịch được mã hóa SSL 256-bit</span>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-20">
                <h3 class="font-bold text-gray-900 mb-4">Tóm tắt đơn hàng</h3>

                <div class="flex items-center space-x-3 p-3 bg-indigo-50 rounded-xl mb-4">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-indigo-700">Gói {{ $plan->name }}</p>
                        <p class="text-xs text-indigo-500">Thời hạn 1 tháng</p>
                    </div>
                </div>

                <div class="space-y-2 text-sm mb-4">
                    @foreach($plan->features ?? [] as $feature)
                    <div class="flex items-center space-x-2 text-gray-600">
                        <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Giá gói</span>
                        <span>{{ number_format($plan->price, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Thuế VAT (10%)</span>
                        <span>Đã bao gồm</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-900 text-base pt-2 border-t border-gray-100">
                        <span>Tổng cộng</span>
                        <span class="text-indigo-600">{{ number_format($plan->price, 0, ',', '.') }}₫</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 text-center">
                        Bằng cách thanh toán, bạn đồng ý với <a href="#" class="text-indigo-500 hover:underline">Điều khoản dịch vụ</a> của CVactive.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</x-app-layout>
