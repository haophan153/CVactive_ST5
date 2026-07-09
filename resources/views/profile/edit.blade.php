@php
    use Illuminate\Support\Carbon;
    $greeting = match(true) {
        Carbon::now()->hour < 12 => 'Buổi sáng tốt lành',
        Carbon::now()->hour < 18 => 'Buổi chiều vui vẻ',
        default => 'Buổi tối tốt lành',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">Hồ sơ của tôi</h2>
                <p class="text-xs text-slate-400 font-medium">{{ $greeting }}, {{ auth()->user()->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- ── Profile header card ─────────────────────────────── --}}
        @php $u = auth()->user(); @endphp
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 shadow-xl shadow-slate-900/20 overflow-hidden relative">
            <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-blue-600/10 blur-2xl"></div>
            <div class="relative flex flex-wrap items-center gap-5">
                <div class="shrink-0">
                    @if($u->avatar_url)
                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                            class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white/10 shadow-xl">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-blue-600 text-white text-2xl font-black flex items-center justify-center ring-4 ring-white/10 shadow-xl">
                            {{ strtoupper(mb_substr($u->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-black text-white">{{ $u->name }}</h3>
                    <p class="text-sm text-slate-400 mt-0.5">{{ $u->email }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        @if($u->role !== 'user')
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-400 bg-amber-400/10 border border-amber-400/20 px-2.5 py-1 rounded-full uppercase tracking-wider">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ $u->role === 'admin' ? 'Quản trị viên' : 'Nhà tuyển dụng' }}
                            </span>
                        @endif
                        @if($u->plan)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-400 bg-blue-400/10 border border-blue-400/20 px-2.5 py-1 rounded-full uppercase tracking-wider">
                                {{ $u->plan->name }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-400 bg-white/5 border border-white/10 px-2.5 py-1 rounded-full">
                            Tham gia {{ $u->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tab navigation ─────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
            <div class="flex border-b border-slate-100" x-data="{ tab: 'info' }">
                <button @click="tab = 'info'"
                    :class="tab === 'info' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Thông tin cá nhân
                </button>
                <button @click="tab = 'avatar'"
                    :class="tab === 'avatar' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Ảnh đại diện
                </button>
                <button @click="tab = 'password'"
                    :class="tab === 'password' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Mật khẩu
                </button>
                <button @click="tab = 'danger'"
                    :class="tab === 'danger' ? 'text-rose-600 border-b-2 border-rose-600 bg-rose-50/50' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                    class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Xóa tài khoản
                </button>
            </div>

            {{-- ── Tab: Info ─────────────────────────────────────── --}}
            <div x-show="tab === 'info'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="p-6 sm:p-8">
                    <div class="max-w-lg">
                        <div class="mb-6">
                            <h3 class="text-base font-bold text-slate-900">Thông tin cá nhân</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Cập nhật thông tin tài khoản và địa chỉ email của bạn.</p>
                        </div>

                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                            @csrf
                            @method('patch')

                            <div>
                                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Họ và tên</label>
                                <input id="name" name="name" type="text"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                @error('name')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                                <input id="email" name="email" type="email"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                    value="{{ old('email', $user->email) }}" required autocomplete="username">
                                @error('email')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                                        <p class="text-sm text-amber-800">
                                            Địa chỉ email của bạn chưa được xác minh.
                                            <button form="send-verification" class="underline hover:text-amber-900 font-semibold">
                                                Gửi lại email xác minh.
                                            </button>
                                        </p>
                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 text-sm font-bold text-emerald-700 flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Email xác minh đã được gửi.
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-lg shadow-blue-900/20 active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Lưu thay đổi
                                </button>

                                @if (session('status') === 'profile-updated')
                                    <p x-data="{ show: true }" x-show="show" x-transition
                                        x-init="setTimeout(() => show = false, 3000)"
                                        class="text-sm font-bold text-emerald-600 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Đã lưu.
                                    </p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Avatar ───────────────────────────────────── --}}
            <div x-show="tab === 'avatar'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="p-6 sm:p-8">
                    <div class="max-w-lg">
                        <div class="mb-6">
                            <h3 class="text-base font-bold text-slate-900">Ảnh đại diện</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Tải lên ảnh đại diện của bạn. JPG, PNG hoặc WebP, tối đa 2MB.</p>
                        </div>

                        <div class="flex items-center gap-6 mb-6 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="shrink-0">
                                @if($u->avatar_url)
                                    <img id="avatar-preview" src="{{ $u->avatar_url }}" alt="{{ $u->name }}"
                                        class="w-24 h-24 rounded-2xl object-cover ring-4 ring-white shadow-lg">
                                @else
                                    <div id="avatar-placeholder"
                                        class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white text-3xl font-black flex items-center justify-center ring-4 ring-white shadow-lg">
                                        {{ strtoupper(mb_substr($u->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Ảnh hiện tại</p>
                                @if($u->avatar_url)
                                    <p id="avatar-source-label" class="text-sm text-slate-700 truncate font-medium">{{ $u->avatar_is_remote ? 'Lấy từ Google của bạn' : 'Ảnh bạn đã tải lên' }}</p>
                                @else
                                    <p class="text-sm text-slate-500">Chưa có ảnh đại diện.</p>
                                @endif
                                <p class="text-xs text-slate-400 mt-1">Mỗi lần đăng nhập Google, ảnh sẽ được cập nhật tự động.</p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('profile.avatar') }}"
                            enctype="multipart/form-data"
                            class="space-y-5"
                            x-data="{ fileName: '', previewUrl: '' }">
                            @csrf

                            <div>
                                <label for="avatar" class="block text-sm font-semibold text-slate-700 mb-1.5">Chọn ảnh mới</label>
                                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    @change="fileName = $event.target.files[0]?.name || ''; previewUrl = ($event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : '')"
                                    class="block w-full text-sm text-slate-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl
                                           file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700
                                           hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl
                                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                    required>
                                <p class="mt-1.5 text-xs text-slate-500" x-show="fileName" x-text="'Đã chọn: ' + fileName"></p>
                                @error('avatar')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Local preview shown only after user picks a file --}}
                            <div x-show="previewUrl" x-transition class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                                <img :src="previewUrl" alt="Preview" class="w-12 h-12 rounded-lg object-cover ring-2 ring-emerald-200">
                                <p class="text-sm text-emerald-700 font-semibold">Ảnh xem trước — sẽ được lưu khi bạn nhấn nút bên dưới.</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-lg shadow-blue-900/20 active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Tải lên
                                </button>

                                @if($u->avatar_url && ! $u->avatar_is_remote)
                                    <button type="button"
                                        onclick="if(confirm('Xóa ảnh đại diện? Lần đăng nhập Google tiếp theo sẽ tự động lấy lại ảnh từ Google.')) document.getElementById('avatar-remove-form').submit();"
                                        class="inline-flex items-center gap-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-sm px-5 py-2.5 rounded-xl transition border border-rose-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Xóa ảnh
                                    </button>
                                @endif

                                @if (session('status') === 'avatar-updated')
                                    <p x-data="{ show: true }" x-show="show" x-transition
                                        x-init="setTimeout(() => show = false, 3000)"
                                        class="text-sm font-bold text-emerald-600 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Đã cập nhật ảnh đại diện.
                                    </p>
                                @elseif (session('status') === 'avatar-removed')
                                    <p x-data="{ show: true }" x-show="show" x-transition
                                        x-init="setTimeout(() => show = false, 3000)"
                                        class="text-sm font-bold text-emerald-600 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Đã xóa ảnh đại diện.
                                    </p>
                                @endif
                            </div>
                        </form>

                        @if($u->avatar_url && ! $u->avatar_is_remote)
                            <form id="avatar-remove-form" method="post" action="{{ route('profile.avatar.remove') }}" class="hidden">
                                @csrf @method('delete')
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Tab: Password ─────────────────────────────────── --}}
            <div x-show="tab === 'password'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="p-6 sm:p-8">
                    <div class="max-w-lg">
                        <div class="mb-6">
                            <h3 class="text-base font-bold text-slate-900">Cập nhật mật khẩu</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Sử dụng mật khẩu mạnh để bảo mật tài khoản của bạn.</p>
                        </div>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                            @csrf
                            @method('put')

                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Mật khẩu hiện tại</label>
                                <input id="current_password" name="current_password" type="password"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                    autocomplete="current-password">
                                @error('current_password')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Mật khẩu mới</label>
                                <input id="password" name="password" type="password"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                    autocomplete="new-password">
                                @error('password')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Xác nhận mật khẩu mới</label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                    autocomplete="new-password">
                                @error('password_confirmation')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-lg shadow-blue-900/20 active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Đổi mật khẩu
                                </button>

                                @if (session('status') === 'password-updated')
                                    <p class="text-sm font-bold text-emerald-600 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Đã đổi mật khẩu.
                                    </p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Danger ──────────────────────────────────── --}}
            <div x-show="tab === 'danger'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="p-6 sm:p-8">
                    <div class="max-w-lg">
                        <div class="mb-6">
                            <h3 class="text-base font-bold text-rose-700">Xóa tài khoản</h3>
                            <p class="text-sm text-slate-500 mt-0.5 leading-relaxed">Khi tài khoản bị xóa, tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn. Hãy chắc chắn rằng bạn đã tải các dữ liệu cần thiết trước khi tiếp tục.</p>
                        </div>

                        <form method="post" action="{{ route('profile.destroy') }}"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản? Hành động này KHÔNG THỂ hoàn tác.')"
                            class="space-y-5">
                            @csrf
                            @method('delete')

                            <div>
                                <label for="del_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Nhập mật khẩu để xác nhận</label>
                                <input id="del_password" name="password" type="password"
                                    class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-400 transition"
                                    autocomplete="current-password">
                                @error('password')
                                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl transition shadow-lg shadow-rose-900/20 active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Xóa tài khoản vĩnh viễn
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
