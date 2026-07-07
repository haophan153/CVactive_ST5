<x-app-layout>

    <div class="py-16">
        <div class="max-w-3xl mx-auto px-6">
            <div class="mb-14">
                <h1 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900">Chọn gói phù hợp với bạn</h1>
                <p class="text-slate-500 mt-3">Miễn phí để bắt đầu. Nâng cấp bất cứ lúc nào.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                <div class="rounded-2xl p-8 relative {{ $plan->slug === 'pro' ? 'border-2 border-indigo-600 bg-indigo-50/30' : 'border-2 border-slate-200 bg-white' }}">
                    @if($plan->slug === 'pro')
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs font-bold px-4 py-1 rounded-full">Phổ biến nhất</div>
                    @endif

                    <div class="text-sm font-semibold mb-1 {{ $plan->slug === 'pro' ? 'text-indigo-600' : 'text-slate-500' }}">{{ $plan->name }}</div>
                    <div class="flex items-baseline gap-1 mb-1">
                        <span class="text-4xl font-black text-slate-900 tracking-tight">{{ number_format($plan->price, 0, ',', '.') }}₫</span>
                        <span class="text-sm text-slate-400">/tháng</span>
                    </div>
                    @if($plan->price == 0)
                    <p class="text-sm text-emerald-600 mb-6">Mãi mãi miễn phí</p>
                    @else
                    <p class="text-sm text-slate-400 mb-6">Thanh toán hàng tháng</p>
                    @endif

                    <ul class="space-y-3 mb-8">
                        @foreach($plan->features ?? [] as $feature)
                        <li class="flex items-start gap-2.5 text-sm text-slate-700">
                            <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>

                    @auth
                        @if(auth()->user()->plan_id == $plan->id)
                        <button disabled class="w-full py-3 bg-slate-100 text-slate-400 font-semibold rounded-xl text-sm cursor-not-allowed">
                            Đang dùng
                        </button>
                        @elseif($plan->price == 0)
                        <a href="{{ route('dashboard') }}" class="block text-center w-full py-3 border-2 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50 font-semibold rounded-xl text-sm transition">
                            Dùng ngay
                        </a>
                        @else
                        <a href="{{ route('payment.checkout', $plan) }}"
                            class="block text-center w-full py-3 {{ $plan->slug === 'pro' ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50' }} font-semibold rounded-xl text-sm transition">
                            Nâng cấp
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
