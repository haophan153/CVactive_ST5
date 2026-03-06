<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bảng giá</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Chọn gói phù hợp với bạn</h1>
                <p class="text-gray-500 text-lg">Miễn phí để bắt đầu. Nâng cấp bất cứ lúc nào.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                <div class="bg-white rounded-2xl border-2 p-8 shadow-sm relative {{ $plan->slug === 'pro' ? 'border-indigo-600 shadow-lg scale-105' : 'border-gray-100' }}">
                    @if($plan->slug === 'pro')
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs font-bold px-4 py-1 rounded-full">Phổ biến nhất</div>
                    @endif

                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                    <div class="flex items-baseline mb-1">
                        <span class="text-4xl font-extrabold text-gray-900">{{ number_format($plan->price, 0, ',', '.') }}₫</span>
                        <span class="text-gray-400 ml-1 text-sm">/tháng</span>
                    </div>
                    @if($plan->price == 0)
                    <p class="text-green-600 text-sm mb-6">Mãi mãi miễn phí</p>
                    @else
                    <p class="text-gray-400 text-sm mb-6">Thanh toán hàng tháng</p>
                    @endif

                    <ul class="space-y-3 mb-8">
                        @foreach($plan->features ?? [] as $feature)
                        <li class="flex items-start space-x-2 text-sm text-gray-700">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>

                    @auth
                    @if(auth()->user()->plan_id == $plan->id)
                    <button disabled class="w-full py-3 bg-gray-100 text-gray-500 font-semibold rounded-xl text-sm cursor-not-allowed">
                        ✓ Gói hiện tại
                    </button>
                    @elseif($plan->price == 0)
                    <a href="{{ route('dashboard') }}" class="block text-center w-full py-3 border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-semibold rounded-xl text-sm transition">
                        Dùng ngay
                    </a>
                    @else
                    <a href="{{ route('payment.checkout', $plan) }}"
                        class="block text-center w-full py-3 {{ $plan->slug === 'pro' ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50' }} font-semibold rounded-xl text-sm transition">
                        Nâng cấp →
                    </a>
                    @endif
                    @else
                    <a href="{{ route('register') }}" class="block text-center w-full py-3 {{ $plan->slug === 'pro' ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50' }} font-semibold rounded-xl text-sm transition">
                        Bắt đầu ngay
                    </a>
                    @endauth
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
