{{-- 
    Navbar thống nhất - lấy style từ trang home
    Đặc điểm: max-w-6xl, indigo-500, md breakpoint, không hamburger, dropdown user (chỉ hiện ở desktop)
--}}
<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <img src="{{ asset('storage/avatars/logo/logo.png') }}" style="height:150px" alt="CVactive" class="pt-2 h-9 w-auto object-contain">
            </a>

            {{-- Menu items (desktop) --}}
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="{{ route('templates.index') }}" class="hover:text-slate-900 transition {{ request()->routeIs('templates.*') ? 'text-slate-900' : '' }}">Mẫu CV</a>
                <a href="{{ route('jobs.index') }}" class="hover:text-slate-900 transition {{ request()->routeIs('jobs.*') ? 'text-slate-900' : '' }}">Việc làm</a>
                <a href="{{ route('pricing') }}" class="hover:text-slate-900 transition {{ request()->routeIs('pricing') ? 'text-slate-900' : '' }}">Bảng giá</a>
                <a href="{{ route('blog.index') }}" class="hover:text-slate-900 transition {{ request()->routeIs('blog.*') ? 'text-slate-900' : '' }}">Blog</a>
                <a href="{{ route('faq') }}" class="hover:text-slate-900 transition {{ request()->routeIs('faq') ? 'text-slate-900' : '' }}">FAQ</a>
            </div>

            {{-- Right side: CTA / Auth --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden md:inline text-sm font-medium text-slate-600 hover:text-slate-900 transition">Dashboard</a>
                    <a href="{{ route('cv.create') }}"
                        class="px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-lg hover:bg-indigo-600 transition shadow-sm shadow-indigo-200">
                        + Tạo CV
                    </a>

                    {{-- User dropdown --}}
                    <div x-data="{ open: false }" @click.outside="open = false" @close-user-menu.window="open = false" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            :aria-expanded="open"
                            aria-haspopup="true"
                            class="flex items-center gap-2 rounded-full p-0.5 hover:ring-2 hover:ring-indigo-100 transition focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                     alt="{{ auth()->user()->name }}"
                                     class="h-9 w-9 rounded-full object-cover border border-slate-200">
                            @else
                                <span class="h-9 w-9 rounded-full bg-indigo-500 text-white text-sm font-semibold flex items-center justify-center border border-slate-200">
                                    {{ strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </span>
                            @endif
                            <svg class="h-4 w-4 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Dropdown panel --}}
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            x-cloak
                            class="absolute right-0 mt-2 w-64 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-slate-200 focus:outline-none z-50 overflow-hidden">

                            {{-- Header --}}
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            {{-- Items --}}
                            <div class="py-1">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('cv.create') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m9-7a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Tạo CV mới
                                </a>
                                <a href="{{ route('my-applications.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Việc làm đã ứng tuyển
                                </a>
                                <a href="{{ route('payment.history') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    Lịch sử thanh toán
                                </a>

                                @if(auth()->user()->isHR())
                                    <div class="my-1 border-t border-slate-100"></div>
                                    <a href="{{ route('hr.job-posts.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        Quản lý tin tuyển dụng
                                    </a>
                                @endif

                                @if(auth()->user()->role === 'admin')
                                    <div class="my-1 border-t border-slate-100"></div>
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Trang quản trị
                                    </a>
                                @endif

                                <div class="my-1 border-t border-slate-100"></div>

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Hồ sơ của tôi
                                </a>

                                {{-- Logout --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden md:inline text-sm font-medium text-slate-600 hover:text-slate-900 transition">Đăng nhập</a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-lg hover:bg-indigo-600 transition shadow-sm shadow-indigo-200">
                        Dùng miễn phí
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
