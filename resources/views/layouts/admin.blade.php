<!DOCTYPE html>
<html lang="vi" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – CVactive</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">

<div class="min-h-full flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 flex flex-col fixed inset-y-0 left-0 z-50 transition-transform duration-300"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="flex items-center h-16 px-5 border-b border-gray-700 flex-shrink-0">
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('hr.job-posts.index') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('storage/avatars/logo/logo.png') }}" alt="CVactive" class="h-7 w-auto object-contain brightness-0 invert">
                <span class="font-bold text-white text-lg">CV<span class="text-indigo-400">active</span></span>
            </a>
            @if(auth()->user()->role === 'admin')
            <span class="ml-2 text-xs bg-indigo-700 text-indigo-200 px-2 py-0.5 rounded font-medium">Admin</span>
            @else
            <span class="ml-2 text-xs bg-emerald-700 text-emerald-200 px-2 py-0.5 rounded font-medium">HR</span>
            @endif
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
            @php
            $navItems = [
                ['route' => 'admin.dashboard',      'label' => 'Tổng quan',         'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'admin.users.index',    'label' => 'Người dùng',        'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['route' => 'admin.templates.index','label' => 'Templates',         'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                ['route' => 'admin.blog.index',     'label' => 'Blog',              'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                ['route' => 'admin.payments.index', 'label' => 'Thanh toán',        'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ];
            @endphp

            {{-- Chỉ Admin mới thấy menu quản trị --}}
            @if(auth()->user()->role === 'admin')
            @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index','.*',$item['route'])); @endphp
            <a href="{{ route($item['route']) }}"
                class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ $isActive ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $item['icon'] }}"/>
                </svg>
                <span>{{ $item['label'] }}</span>
            </a>
            @endforeach
            @endif

            <div class="pt-4 mt-4 border-t border-gray-700">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Về trang chính</span>
                </a>
            </div>
        </nav>

        {{-- User Info --}}
        <div class="flex-shrink-0 p-4 border-t border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-300 transition" title="Đăng xuất">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    {{-- Main --}}
    <div class="flex-1 flex flex-col lg:pl-64">

        {{-- Top bar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Admin Panel')</h1>
            </div>
            <div class="flex items-center space-x-3 text-sm text-gray-500">
                <span>{{ now()->format('d/m/Y') }}</span>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mx-6 mt-4 flex items-center space-x-2 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mx-6 mt-4 flex items-center space-x-2 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
