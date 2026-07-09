@extends('layouts.app')

@push('styles')
<style>
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('title', 'Đặt lại mật khẩu | CVactive')
@section('description', 'Tạo mật khẩu mới cho tài khoản CVactive của bạn.')

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
                        Mật khẩu<br class="hidden sm:block"> mới
                    </h1>
                    <p class="mt-4 text-slate-500 text-base leading-relaxed max-w-md">
                        Chọn một mật khẩu mạnh để bảo vệ tài khoản của bạn. Mật khẩu phải có ít nhất 8 ký tự.
                    </p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Ít nhất 8 ký tự
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Kết hợp chữ hoa, chữ thường &amp; số
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Không dùng mật khẩu cũ
                        </li>
                    </ul>
                </div>

                {{-- RIGHT: form --}}
                <div class="w-full">
                    <div class="bg-white rounded-2xl border border-slate-100 p-8 md:p-10 shadow-sm">
                        <div class="md:hidden mb-6 text-center">
                            <h1 class="text-2xl font-black text-slate-900">Đặt lại mật khẩu</h1>
                            <p class="mt-1 text-sm text-slate-500">Tạo mật khẩu mới cho tài khoản</p>
                        </div>

                        <h2 class="hidden md:block text-xl font-bold text-slate-900">Tạo mật khẩu mới</h2>
                        <p class="hidden md:block mt-1 text-sm text-slate-500">Nhập mật khẩu mới và xác nhận bên dưới.</p>

                        {{-- Status message --}}
                        <x-auth-session-status class="mt-4" :status="session('status')" />

                        <form method="POST" action="{{ route('password.store') }}" class="space-y-4 mt-6">
                            @csrf

                            {{-- Token --}}
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            {{-- Email --}}
                            <div>
                                <x-input-label for="email" value="Email" class="text-slate-700" />
                                <x-text-input id="email"
                                    class="block mt-1.5 w-full rounded-xl border-slate-200 bg-slate-50 focus:border-indigo-400 focus:ring-indigo-300"
                                    type="email"
                                    name="email"
                                    :value="old('email', $request->email)"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    readonly />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- Password --}}
                            <div>
                                <x-input-label for="password" value="Mật khẩu mới" class="text-slate-700" />
                                <x-text-input id="password"
                                    class="block mt-1.5 w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-300"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Ít nhất 8 ký tự" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" class="text-slate-700" />
                                <x-text-input id="password_confirmation"
                                    class="block mt-1.5 w-full rounded-xl border-slate-200 focus:border-indigo-400 focus:ring-indigo-300"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Nhập lại mật khẩu" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <button type="submit"
                                    class="mt-2 w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm active:scale-[0.98] transition focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                Đặt lại mật khẩu
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-600">
                            Đã nhớ mật khẩu?
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
