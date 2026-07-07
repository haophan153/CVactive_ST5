<!DOCTYPE html>
<html lang="vi" class="h-full bg-gray-100{{ isset($_COOKIE['admin_sidebar_collapsed']) && $_COOKIE['admin_sidebar_collapsed'] === '1' ? ' admin-collapsed' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – CVactive</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    <style>
        /* Default sidebar offset (expanded). Server-rendered via $sidebarCollapsed. */
        .admin-main{padding-left:16rem}
        .admin-sidebar{left:0;width:16rem}
        @media (max-width:1023px){
            .admin-main{padding-left:0}
            .admin-sidebar{width:16rem}
        }

        /* Collapsed state — narrower but still shows icon + label (12rem ≈ 192px).
           Toggled by server-side class on <html> so layout is correct BEFORE Alpine loads. */
        .admin-collapsed .admin-main{padding-left:12rem}
        .admin-collapsed .admin-sidebar{width:12rem}
        @media (max-width:1023px){
            .admin-collapsed .admin-main{padding-left:0}
            .admin-collapsed .admin-sidebar{width:16rem}
        }

        /* Subtle visual separation so the empty area doesn't feel "broken" */
        body{background:linear-gradient(135deg,#f9fafb 0%,#f3f4f6 100%)}

        /* Smooth transition when Alpine toggles the class */
        .admin-main,.admin-sidebar{transition:padding-left .3s ease,width .3s ease}
    </style>
    <script>
        // Belt-and-braces fallback: also set the class on <html> after first paint.
        // The server-side cookie approach already handles this, so this only matters if
        // a stale browser somehow lost the cookie but kept localStorage.
        (function () {
            try {
                if (document.documentElement.classList.contains('admin-collapsed')) return;
                var keys = ['_x_admin_sidebar_collapsed','admin_sidebar_collapsed'];
                for (var i = 0; i < keys.length; i++) {
                    var raw = localStorage.getItem(keys[i]);
                    if (raw === null) continue;
                    var val;
                    try { val = JSON.parse(raw); } catch (e) { val = raw; }
                    if (val === true || val === 'true' || val === 1 || val === '1') {
                        document.documentElement.classList.add('admin-collapsed');
                        break;
                    }
                }
            } catch (e) {}
        })();
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
</head>
@php
    use Illuminate\Support\Facades\DB;

    $unreadContacts = 0;
    $pendingPayments = 0;
    $openJobs        = 0;
    if (auth()->check() && auth()->user()->role === 'admin') {
        try {
            $unreadContacts = DB::table('contacts')->where('is_read', false)->count();
            $pendingPayments = DB::table('payments')->where('status', 'pending')->count();
            $openJobs       = DB::table('job_posts')->where('status', 'published')->count();
        } catch (\Throwable $e) { /* tables may not exist yet */ }
    }

    $navGroups = [
        'Quản lý' => [
            ['route' => 'admin.dashboard',      'label' => 'Tổng quan',    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'admin.users.index',    'label' => 'Người dùng',   'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['route' => 'admin.job-posts.index','label' => 'Tin tuyển dụng','icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ],
        'Nội dung' => [
            ['route' => 'admin.templates.index',      'label' => 'Templates',      'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
            ['route' => 'admin.blog.index',           'label' => 'Blog',           'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
            ['route' => 'admin.blog-categories.index','label' => 'Danh mục Blog', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
            ['route' => 'admin.faqs.index',           'label' => 'FAQ',            'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ],
        'Tài chính' => [
            ['route' => 'admin.payments.index', 'label' => 'Thanh toán', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['route' => 'admin.plans.index',    'label' => 'Gói dịch vụ','icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
        ],
        'Hệ thống' => [
            ['route' => 'admin.contacts.index', 'label' => 'Hộp thư liên hệ','icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ['route' => 'admin.settings.index', 'label' => 'Cài đặt',         'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ],
    ];

    $isAdmin = auth()->user()->role === 'admin';
@endphp
<body class="h-full" x-data="{ sidebarOpen: false, sidebarCollapsed: $persist(false).as('admin_sidebar_collapsed'), userMenu: false, notifOpen: false, quickCreate: false, searchOpen: false, searchQ: '', searchResults: [], searching: false }"
    @toggle-sidebar.window="sidebarCollapsed = !sidebarCollapsed; document.cookie='admin_sidebar_collapsed='+(sidebarCollapsed?'1':'0')+';path=/;max-age=31536000;samesite=lax'"
    x-effect="document.documentElement.classList.toggle('admin-collapsed', sidebarCollapsed)">

<div class="min-h-full flex">

    {{-- Sidebar --}}
    <aside
        class="admin-sidebar bg-gray-900 flex flex-col fixed top-0 bottom-0 left-0 z-50 transition-all duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="flex items-center h-16 px-5 border-b border-gray-700 flex-shrink-0">
            <a href="{{ $isAdmin ? route('admin.dashboard') : route('hr.job-posts.index') }}" class="flex items-center gap-2.5 flex-1 min-w-0">
                <img src="{{ asset('storage/avatars/logo/logo.png') }}" alt="CVactive" class="h-7 w-auto object-contain brightness-0 invert flex-shrink-0">
                <span x-show="!sidebarCollapsed" x-cloak class="font-bold text-white text-lg whitespace-nowrap">CV<span class="text-indigo-400">active</span></span>
            </a>
            @if($isAdmin)
            <span x-show="!sidebarCollapsed" x-cloak class="ml-2 text-xs bg-indigo-700 text-indigo-200 px-2 py-0.5 rounded font-medium">Admin</span>
            @else
            <span x-show="!sidebarCollapsed" x-cloak class="ml-2 text-xs bg-emerald-700 text-emerald-200 px-2 py-0.5 rounded font-medium">HR</span>
            @endif
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-4">
            @if($isAdmin)
                @foreach($navGroups as $group => $items)
                    <div>
                        <p x-show="!sidebarCollapsed" x-cloak class="px-3 mb-2 text-[10px] font-bold tracking-wider text-gray-500 uppercase">{{ $group }}</p>
                        <p x-show="sidebarCollapsed" x-cloak class="border-t border-gray-700 mb-2"></p>
                        <div class="space-y-1">
                            @foreach($items as $item)
                                @php
                                    $isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index','.*',$item['route']));
                                @endphp
                                <a href="{{ route($item['route']) }}"
                                    title="{{ $item['label'] }}"
                                    class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition {{ $isActive ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                                    :class="sidebarCollapsed ? 'space-x-2' : 'space-x-3'">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                                    </svg>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </nav>

        {{-- Bottom: collapse toggle + back link --}}
        <div class="flex-shrink-0 border-t border-gray-700 p-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                title="Về trang chính"
                class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition"
                :class="sidebarCollapsed ? 'space-x-2' : 'space-x-3'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span class="truncate">Về trang chính</span>
            </a>

            {{-- Collapse button (desktop only) --}}
            <button @click="sidebarCollapsed = !sidebarCollapsed; document.cookie = 'admin_sidebar_collapsed=' + (sidebarCollapsed ? '1' : '0') + ';path=/;max-age=31536000;samesite=lax'"
                :title="sidebarCollapsed ? 'Mở rộng' : 'Thu gọn'"
                class="hidden lg:flex w-full items-center px-3 py-2 rounded-lg text-xs font-medium text-gray-500 hover:bg-gray-800 hover:text-white transition"
                :class="sidebarCollapsed ? 'space-x-2 justify-start' : 'justify-between'">
                <span class="flex items-center space-x-2">
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Thu gọn</span>
                </span>
            </button>
        </div>

        {{-- User info + avatar dropdown --}}
        <div class="flex-shrink-0 p-3 border-t border-gray-700">
            <div class="relative" @click.outside="userMenu = false">
                <button @click="userMenu = !userMenu" class="w-full flex items-center rounded-lg p-2 hover:bg-gray-800 transition"
                    :class="sidebarCollapsed ? 'justify-center' : 'space-x-3'">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div x-show="!sidebarCollapsed" x-cloak class="flex-1 min-w-0 text-left">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <svg x-show="!sidebarCollapsed" x-cloak class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </button>

                <div x-show="userMenu" x-cloak x-transition
                    class="absolute bottom-full left-0 mb-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50"
                    :class="sidebarCollapsed ? 'lg:left-full lg:bottom-0 lg:mb-0 lg:ml-2' : ''">
                    <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Trang cá nhân</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/></svg>
                        <span>Về trang chính</span>
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"></div>

    {{-- Main --}}
    <div class="admin-main flex-1 flex flex-col min-w-0 transition-all duration-300">

        {{-- Top bar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30 gap-3">

            {{-- Left: hamburger + breadcrumb --}}
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <nav class="hidden sm:flex items-center text-sm gap-1 min-w-0" aria-label="Breadcrumb">
                    <a href="{{ $isAdmin ? route('admin.dashboard') : route('hr.job-posts.index') }}" class="text-gray-500 hover:text-gray-700 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3"/></svg>
                    </a>
                    @hasSection('breadcrumb')
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        @yield('breadcrumb')
                    @else
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-gray-900 font-semibold truncate">@yield('page-title', 'Admin Panel')</span>
                    @endif
                </nav>

                <h1 class="sm:hidden text-base font-semibold text-gray-900 truncate">@yield('page-title', 'Admin')</h1>
            </div>

            {{-- Right: search + notifications + quick create + date --}}
            <div class="flex items-center gap-2 lg:gap-3 flex-shrink-0">

                {{-- Global search --}}
                @if($isAdmin)
                <div class="relative hidden md:block" @click.outside="searchOpen = false">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" x-model="searchQ"
                            @input.debounce.250ms="
                                if (searchQ.length >= 2) {
                                    searching = true;
                                    fetch('{{ route('admin.search') }}?q=' + encodeURIComponent(searchQ), {
                                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                                    })
                                    .then(r => r.json())
                                    .then(d => { searchResults = d.results || []; searching = false; searchOpen = true; })
                                    .catch(() => { searching = false; });
                                } else { searchResults = []; searchOpen = false; }
                            "
                            @focus="if (searchResults.length > 0) searchOpen = true"
                            placeholder="Tìm user, job, template..."
                            class="w-48 lg:w-72 pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Dropdown --}}
                    <div x-show="searchOpen" x-cloak x-transition.opacity
                        class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50 max-h-96 overflow-y-auto">
                        <div x-show="searching" class="p-4 text-center text-sm text-gray-500">
                            <svg class="animate-spin h-5 w-5 mx-auto text-indigo-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <p class="mt-2">Đang tìm kiếm...</p>
                        </div>
                        <div x-show="!searching && searchResults.length === 0" class="p-6 text-center text-sm text-gray-500">
                            Không có kết quả cho "<span x-text="searchQ"></span>"
                        </div>
                        <div x-show="!searching && searchResults.length > 0">
                            <template x-for="group in searchResults" :key="group.label">
                                <div class="border-b border-gray-100 last:border-0">
                                    <p class="px-4 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-wider bg-gray-50" x-text="group.label"></p>
                                    <template x-for="item in group.items" :key="item.url">
                                        <a :href="item.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 transition">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate" x-text="item.title"></p>
                                                <p class="text-xs text-gray-500 truncate" x-text="item.subtitle"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Quick create --}}
                @if($isAdmin)
                <div class="relative" @click.outside="quickCreate = false">
                    <button @click="quickCreate = !quickCreate"
                        class="flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden lg:inline">Tạo mới</span>
                    </button>
                    <div x-show="quickCreate" x-cloak x-transition.opacity
                        class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-xl border border-gray-200 py-1 z-50">
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>Người dùng
                        </a>
                        <a href="{{ route('admin.templates.create') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>Template
                        </a>
                        <a href="{{ route('admin.blog.create') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>Bài viết
                        </a>
                        <a href="{{ route('admin.plans.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>Gói dịch vụ
                        </a>
                        <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>FAQ
                        </a>
                    </div>
                </div>
                @endif

                {{-- Notifications --}}
                @if($isAdmin)
                <div class="relative" @click.outside="notifOpen = false">
                    <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($unreadContacts + $pendingPayments > 0)
                        <span class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full flex items-center justify-center">{{ $unreadContacts + $pendingPayments }}</span>
                        @endif
                    </button>
                    <div x-show="notifOpen" x-cloak x-transition.opacity
                        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">Thông báo</h3>
                            <span class="text-xs text-gray-500">{{ $unreadContacts + $pendingPayments }} mục mới</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <a href="{{ route('admin.contacts.index') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50">
                                <span class="w-9 h-9 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $unreadContacts }} liên hệ chưa đọc</p>
                                    <p class="text-xs text-gray-500">Tin nhắn từ form liên hệ chờ xử lý</p>
                                </div>
                            </a>
                            <a href="{{ route('admin.payments.index') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50">
                                <span class="w-9 h-9 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $pendingPayments }} thanh toán chờ</p>
                                    <p class="text-xs text-gray-500">Giao dịch đang chờ xác nhận</p>
                                </div>
                            </a>
                            <a href="{{ route('admin.job-posts.index') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                <span class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $openJobs }} tin đang tuyển</p>
                                    <p class="text-xs text-gray-500">Tin tuyển dụng đang mở</p>
                                </div>
                            </a>
                        </div>
                        <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 text-center">
                            <a href="{{ route('admin.contacts.index') }}" class="text-xs font-medium text-indigo-600 hover:underline">Xem tất cả →</a>
                        </div>
                    </div>
                </div>
                @endif

                <span class="hidden xl:inline text-sm text-gray-500">{{ now()->format('d/m/Y') }}</span>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity
            class="mx-6 mt-4 flex items-center space-x-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-green-700 hover:text-green-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif
        @if(session('error'))
        <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity
            class="mx-6 mt-4 flex items-center space-x-2 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="ml-auto text-red-700 hover:text-red-900">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 p-4 lg:p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
