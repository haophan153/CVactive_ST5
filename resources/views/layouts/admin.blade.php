<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – CVactive</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>[x-cloak]{display:none!important}</style>
    <style>
        /* ─── Font ─── */
        * { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }

        /* ─── Sidebar layout ─── */
        .admin-main{padding-left:16rem}
        .admin-sidebar{left:0;width:16rem}
        @media (max-width:1023px){ .admin-main{padding-left:0} .admin-sidebar{width:16rem} }


        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar{width:4px;height:4px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#334155;border-radius:999px}
        ::-webkit-scrollbar-thumb:hover{background:#475569}
    </style>
    <script>
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
        'Tổng quan' => [
            ['route' => 'admin.dashboard',       'label' => 'Dashboard',      'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ],
        'Quản lý' => [
            ['route' => 'admin.users.index',      'label' => 'Người dùng',     'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['route' => 'admin.job-posts.index',  'label' => 'Tin tuyển dụng', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ],
        'Nội dung' => [
            ['route' => 'admin.templates.index',       'label' => 'Templates',       'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
            ['route' => 'admin.blog.index',            'label' => 'Blog',            'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
            ['route' => 'admin.blog-categories.index', 'label' => 'Danh mục Blog',   'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
            ['route' => 'admin.faqs.index',           'label' => 'FAQ',             'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ],
        'Tài chính' => [
            ['route' => 'admin.payments.index',  'label' => 'Thanh toán',     'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['route' => 'admin.plans.index',     'label' => 'Gói dịch vụ',    'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
        ],
        'Hệ thống' => [
            ['route' => 'admin.contacts.index',  'label' => 'Hộp thư liên hệ',  'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ['route' => 'admin.settings.index',   'label' => 'Cài đặt',          'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ],
    ];

    $isAdmin = auth()->user()->role === 'admin';
@endphp
<body class="h-full bg-slate-50"     x-data="{ sidebarOpen: false, sidebarCollapsed: false, userMenu: false, notifOpen: false, quickCreate: false, searchOpen: false, searchQ: '', searchResults: [], searching: false }">

<div class="min-h-full flex">

    {{-- ═══════════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════════════ --}}
    <aside class="admin-sidebar bg-slate-900 flex flex-col fixed top-0 bottom-0 left-0 z-50 transition-all duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="relative h-16 flex items-center px-4 flex-shrink-0 overflow-hidden">
            <a href="{{ $isAdmin ? route('admin.dashboard') : route('hr.job-posts.index') }}" class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-500/30">
                    <span class="text-white font-extrabold text-sm">CV</span>
                </div>
                <div class="flex items-center gap-2 min-w-0">
                    <span class="font-bold text-white text-base tracking-tight">CV<span class="text-indigo-400">active</span></span>
                    @if($isAdmin)
                    <span class="shrink-0 text-[10px] font-semibold bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded-full border border-indigo-500/30">Admin</span>
                    @else
                    <span class="shrink-0 text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded-full border border-emerald-500/30">HR</span>
                    @endif
                </div>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-0.5 scrollbar-thin">
            @if($isAdmin)
                @foreach($navGroups as $group => $items)
                    <div class="mb-3">
                        <p class="px-3 mb-1 text-[10px] font-bold tracking-widest text-slate-500 uppercase">{{ $group }}</p>
                        @foreach($items as $item)
                            @php
                                $isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index','.*',$item['route']));
                            @endphp
                            <a href="{{ route($item['route']) }}"
                                title="{{ $item['label'] }}"
                                class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 relative
                                {{ $isActive
                                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25'
                                    : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800' }}">
                                {{-- Active indicator bar --}}
                                @if($isActive)
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-0.5 h-5 bg-white/60 rounded-r-full"></span>
                                @endif
                                <svg class="w-5 h-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                                </svg>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </nav>

        {{-- Bottom actions --}}
        <div class="flex-shrink-0 border-t border-slate-700/50 p-2 space-y-0.5">
            <a href="{{ route('dashboard') }}"
                title="Về trang chính"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition-all"
                :class="sidebarCollapsed ? 'justify-start px-3' : 'gap-3'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak>Về trang chính</span>
                <span x-show="sidebarCollapsed" x-cloak class="text-xs font-medium text-slate-400">Trang chính</span>
            </a>
        </div>

        {{-- User avatar --}}
        <div class="flex-shrink-0 p-2 border-t border-slate-700/50">
            <div class="relative" @click.outside="userMenu = false">
                <button @click="userMenu = !userMenu"
                    class="w-full flex items-center gap-3 rounded-lg p-2 hover:bg-slate-800 transition-all">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-8 h-8 rounded-lg object-cover flex-shrink-0 shadow-md shadow-indigo-500/20">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-md shadow-indigo-500/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0 text-left">
                        <p class="text-sm font-semibold text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate leading-tight">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-500 flex-shrink-0 transition-transform" :class="userMenu ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </button>

                <div x-show="userMenu" x-cloak x-transition.opacity.duration-150
                    class="absolute bottom-0 left-0 mb-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="font-bold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Trang cá nhân
                        </a>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3"/></svg>
                            Về trang chính
                        </a>
                    </div>
                    <div class="border-t border-slate-100 pt-1 pb-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 w-full transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

    {{-- ═══════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════════ --}}
    <div class="admin-main flex-1 flex flex-col min-w-0 transition-all duration-300">

        {{-- Top bar --}}
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-200/80 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30 gap-3">

            {{-- Left: hamburger + breadcrumb --}}
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700 flex-shrink-0 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <nav class="hidden sm:flex items-center text-sm gap-1.5 min-w-0" aria-label="Breadcrumb">
                    <a href="{{ $isAdmin ? route('admin.dashboard') : route('hr.job-posts.index') }}" class="text-slate-400 hover:text-slate-600 flex-shrink-0 p-1 rounded hover:bg-slate-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3"/></svg>
                    </a>
                    @hasSection('breadcrumb')
                        <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        @yield('breadcrumb')
                    @else
                        <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-slate-900 font-bold truncate">@yield('page-title', 'Admin Panel')</span>
                    @endif
                </nav>

                <h1 class="sm:hidden text-base font-bold text-slate-900 truncate">@yield('page-title', 'Admin')</h1>
            </div>

            {{-- Right: search + notifications + quick create + date --}}
            <div class="flex items-center gap-1.5 lg:gap-2 flex-shrink-0">

                {{-- Global search --}}
                @if($isAdmin)
                <div class="relative hidden md:block" @click.outside="searchOpen = false">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
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
                            class="w-48 lg:w-72 pl-9 pr-3 py-2 text-sm bg-slate-100 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:bg-white placeholder-slate-400 transition-all">
                    </div>

                    {{-- Search dropdown --}}
                    <div x-show="searchOpen" x-cloak x-transition.opacity.duration-150
                        class="absolute right-0 mt-2 w-[28rem] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 max-h-96 overflow-y-auto">
                        <div x-show="searching" class="p-6 text-center text-sm text-slate-400">
                            <svg class="animate-spin h-5 w-5 mx-auto text-indigo-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <p class="mt-2">Đang tìm kiếm...</p>
                        </div>
                        <div x-show="!searching && searchResults.length === 0" class="p-8 text-center text-sm text-slate-400">
                            <svg class="w-8 h-8 mx-auto text-slate-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Không có kết quả cho "<span class="font-medium text-slate-600" x-text="searchQ"></span>"
                        </div>
                        <div x-show="!searching && searchResults.length > 0">
                            <template x-for="group in searchResults" :key="group.label">
                                <div class="border-b border-slate-100 last:border-0">
                                    <p class="px-4 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50" x-text="group.label"></p>
                                    <template x-for="item in group.items" :key="item.url">
                                        <a :href="item.url" class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-50 transition-colors">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 truncate" x-text="item.title"></p>
                                                <p class="text-xs text-slate-400 truncate" x-text="item.subtitle"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Notifications --}}
                @if($isAdmin && ($unreadContacts + $pendingPayments) > 0)
                <div class="relative" @click.outside="notifOpen = false">
                    <button @click="notifOpen = !notifOpen" class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full flex items-center justify-center shadow-sm">{{ $unreadContacts + $pendingPayments }}</span>
                    </button>
                    <div x-show="notifOpen" x-cloak x-transition.opacity.duration-150
                        class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50">
                        <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900">Thông báo</h3>
                            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-full">{{ $unreadContacts + $pendingPayments }} mục mới</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                            @if($unreadContacts > 0)
                            <a href="{{ route('admin.contacts.index') }}" class="flex items-start gap-3 px-4 py-3.5 hover:bg-slate-50 transition-colors">
                                <span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900">{{ $unreadContacts }} liên hệ chưa đọc</p>
                                    <p class="text-xs text-slate-400">Tin nhắn từ form liên hệ chờ xử lý</p>
                                </div>
                            </a>
                            @endif
                            @if($pendingPayments > 0)
                            <a href="{{ route('admin.payments.index') }}" class="flex items-start gap-3 px-4 py-3.5 hover:bg-slate-50 transition-colors">
                                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900">{{ $pendingPayments }} thanh toán chờ</p>
                                    <p class="text-xs text-slate-400">Giao dịch đang chờ xác nhận</p>
                                </div>
                            </a>
                            @endif
                        </div>
                        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route('admin.contacts.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Xem tất cả</a>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Quick create --}}
                @if($isAdmin)
                <div class="relative" @click.outside="quickCreate = false">
                    <button @click="quickCreate = !quickCreate"
                        class="flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/30">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden lg:inline">Tạo mới</span>
                    </button>
                    <div x-show="quickCreate" x-cloak x-transition.opacity.duration-150
                        class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50">
                        <div class="px-3 py-1.5">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-2">Nội dung</p>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>Người dùng
                        </a>
                        <a href="{{ route('admin.templates.create') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>Template
                        </a>
                        <a href="{{ route('admin.blog.create') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>Bài viết
                        </a>
                        <div class="border-t border-slate-100 mt-1 pt-1">
                            <a href="{{ route('admin.plans.create') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>Gói dịch vụ
                            </a>
                            <a href="{{ route('admin.faqs.create') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>FAQ
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <span class="hidden xl:inline text-xs text-slate-400 bg-slate-100 px-2.5 py-1.5 rounded-lg font-medium">{{ now()->format('d/m/Y') }}</span>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity
            class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-sm">
            <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-600 hover:text-emerald-800 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif
        @if(session('error'))
        <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.opacity
            class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl shadow-sm">
            <span class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
            <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800 transition-colors">
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
