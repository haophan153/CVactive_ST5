@extends('layouts.app')

@section('title', 'Đăng ký — CVactive')
@section('description', 'Tạo tài khoản CVactive miễn phí để bắt đầu thiết kế CV chuyên nghiệp của bạn.')

@section('content')

    <section class="min-h-[calc(100vh-4rem)] bg-gradient-to-b from-white via-indigo-50/40 to-white">
        <div class="max-w-6xl mx-auto px-6 py-12 md:py-20">
            <div class="grid md:grid-cols-2 gap-10 items-center">

                {{-- Cột trái: thương hiệu --}}
                <div class="hidden md:block">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold uppercase tracking-wider">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                        CVactive
                    </span>
                    <h1 class="mt-4 text-4xl lg:text-5xl font-bold text-slate-900 leading-tight">
                        Bắt đầu hành trình <br>
                        <span class="text-indigo-500">CV của bạn</span>
                    </h1>
                    <p class="mt-4 text-slate-600 text-base leading-relaxed max-w-md">
                        Tạo tài khoản miễn phí, lựa chọn mẫu CV phù hợp và tạo ấn tượng với nhà tuyển dụng chỉ trong vài phút.
                    </p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-indigo-500 mt-0.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Miễn phí, không cần thẻ tín dụng
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-indigo-500 mt-0.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Hàng chục mẫu CV chuyên nghiệp
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-indigo-500 mt-0.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tùy chỉnh mọi thứ theo ý thích
                        </li>
                    </ul>
                </div>

                {{-- Cột phải: form --}}
                <div class="w-full">
                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 p-8 md:p-10">
                        <div class="md:hidden mb-6 text-center">
                            <h1 class="text-2xl font-bold text-slate-900">Đăng ký</h1>
                            <p class="mt-1 text-sm text-slate-500">Tạo tài khoản miễn phí.</p>
                        </div>

                        <h2 class="hidden md:block text-2xl font-bold text-slate-900">Tạo tài khoản</h2>
                        <p class="hidden md:block mt-1 text-sm text-slate-500">Chỉ mất chưa đầy một phút.</p>

                        {{-- Google Register --}}
                        <a href="{{ route('auth.google') }}"
                           class="mt-6 w-full inline-flex items-center justify-center gap-3 px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Đăng ký với Google</span>
                        </a>

                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                            <div class="relative flex justify-center text-xs uppercase tracking-wider">
                                <span class="bg-white px-3 text-slate-400">hoặc đăng ký bằng email</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="name" value="Họ và tên" class="text-slate-700" />
                                <x-text-input id="name"
                                              class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"
                                              type="text"
                                              name="name"
                                              :value="old('name')"
                                              required
                                              autofocus
                                              autocomplete="name"
                                              placeholder="Nguyễn Văn A" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" value="Email" class="text-slate-700" />
                                <x-text-input id="email"
                                              class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"
                                              type="email"
                                              name="email"
                                              :value="old('email')"
                                              required
                                              autocomplete="username"
                                              placeholder="ban@example.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" value="Mật khẩu" class="text-slate-700" />
                                <x-text-input id="password"
                                              class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"
                                              type="password"
                                              name="password"
                                              required
                                              autocomplete="new-password"
                                              placeholder="Tối thiểu 8 ký tự" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" class="text-slate-700" />
                                <x-text-input id="password_confirmation"
                                              class="block mt-1.5 w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500"
                                              type="password"
                                              name="password_confirmation"
                                              required
                                              autocomplete="new-password"
                                              placeholder="Nhập lại mật khẩu" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <button type="submit"
                                    class="mt-2 w-full px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm shadow-indigo-200 transition focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                                Tạo tài khoản
                            </button>
                        </form>

                        <p class="mt-6 text-center text-sm text-slate-600">
                            Đã có tài khoản?
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold hover:underline">
                                Đăng nhập
                            </a>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection