@extends('layouts.app')

@push('styles')
<style>
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('title', 'Quên mật khẩu | CVactive')
@section('description', 'Đặt lại mật khẩu CVactive của bạn qua email.')

@section('content')

    <section class="min-h-[calc(100vh-4rem)] bg-[#FAFAF9]">
        <div class="max-w-5xl mx-auto px-6 py-12 md:py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">

                {{-- LEFT: brand copy --}}
                <div class="hidden md:block">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold uppercase tracking-wider">
                        CVactive
                    </span>
                    <h1 class="mt-5 text-4xl lg:text-5xl font-black text-slate-900 leading-tight tracking-tight">
                        Không nhớ<br class="hidden sm:block"> mật khẩu?
                    </h1>
                    <p class="mt-4 text-slate-500 text-base leading-relaxed max-w-md">
                        Không thành vấn đề. Nhập email đã đăng ký và chúng tôi sẽ gửi liên kết đặt lại mật khẩu ngay.
                    </p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Liên kết có hiệu lực trong 60 phút
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Đặt lại mật khẩu mới tức thì
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            An toàn &amp; bảo mật tuyệt đối
                        </li>
                    </ul>
                </div>

                {{-- RIGHT: form --}}
                <div class="w-full">
                    <div class="bg-white rounded-2xl border border-slate-100 p-8 md:p-10 shadow-sm">
                        <div class="md:hidden mb-6 text-center">
                            <h1 class="text-2xl font-black text-slate-900">Quên mật khẩu?</h1>
                            <p class="mt-1 text-sm text-slate-500">Nhập email đã đăng ký</p>
                        </div>

                        <h2 class="hidden md:block text-xl font-bold text-slate-900">Đặt lại mật khẩu</h2>
                        <p class="hidden md:block mt-1 text-sm text-slate-500">Nhập email đã đăng ký để nhận liên kết đặt lại.</p>

                        {{-- Status message --}}
                        <x-auth-session-status class="mt-4" :status="session('status')" />

                        <form method="POST" action="{{ route('password.email') }}" class="space-y-4 mt-6">
                            @csrf

                            <div>
                                <x-input-label for="email" value="Email" class="text-slate-700" />
                                <x-text-input id="email"
                                    class="block mt-1.5 w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-300"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="ban@example.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <button type="submit"
                                    class="mt-2 w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm active:scale-[0.98] transition focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                Gửi liên kết đặt lại
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-600">
                            Nhớ mật khẩu rồi?
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                                Đăng nhập ngay
                            </a>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
