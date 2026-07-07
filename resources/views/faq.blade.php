{{-- ============================== HERO ============================== --}}
@extends('layouts.app')

@section('content')

    <section class="bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 60px 60px;"></div>
        <div class="relative max-w-4xl mx-auto px-6 pt-16 pb-20 text-center">
            {{-- Breadcrumb --}}
            <nav class="flex items-center justify-center gap-2 text-xs text-slate-400 mb-8">
                <a href="{{ route('home') }}" class="hover:text-white transition">Trang chủ</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-300">FAQ</span>
            </nav>

            <span class="inline-block text-xs font-semibold tracking-[0.18em] uppercase text-indigo-400 mb-4">Trung tâm hỗ trợ</span>
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4 leading-[1.1]">
                Chúng tôi có thể <span class="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">giúp gì cho bạn?</span>
            </h1>
            <p class="text-slate-400 text-base md:text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
                Tìm câu trả lời nhanh chóng cho những câu hỏi thường gặp về CVactive.
                Không tìm thấy? Đội ngũ hỗ trợ luôn sẵn sàng.
            </p>

            {{-- Search box --}}
            <form action="{{ route('faq') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="relative">
                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search ?? '' }}"
                        placeholder="Tìm kiếm câu hỏi, ví dụ: tạo CV, xuất PDF, thanh toán..."
                        class="w-full pl-14 pr-32 py-4 rounded-2xl bg-white/5 border border-white/10 text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm">
                    <button type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 px-5 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-xl hover:bg-indigo-400 transition shadow-lg shadow-indigo-900/40">
                        Tìm kiếm
                    </button>
                </div>
                @if($search)
                    <div class="mt-4 text-sm text-slate-400">
                        Đang hiển thị kết quả cho <span class="text-white font-medium">"{{ $search }}"</span>
                        · <a href="{{ route('faq') }}" class="text-indigo-400 hover:underline">Xóa bộ lọc</a>
                    </div>
                @endif
            </form>

            {{-- Quick stats --}}
            <div class="grid grid-cols-3 gap-4 max-w-xl mx-auto mt-12">
                <div>
                    <div class="text-2xl md:text-3xl font-extrabold text-white">{{ $totalFaqs }}+</div>
                    <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Câu hỏi</div>
                </div>
                <div class="border-x border-white/10">
                    <div class="text-2xl md:text-3xl font-extrabold text-white">24/7</div>
                    <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Hỗ trợ</div>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-extrabold text-white">{{ count($categories) }}</div>
                    <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Chủ đề</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== POPULAR + CATEGORIES ===================== --}}
    <section class="bg-slate-50 py-16 px-6 border-b border-slate-100">
        <div class="max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-3 gap-8">

                {{-- Categories list --}}
                <div class="lg:col-span-2">
                    <div class="flex items-end justify-between mb-6">
                        <div>
                            <span class="text-xs font-semibold tracking-[0.18em] uppercase text-indigo-500">Khám phá</span>
                            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mt-1">Theo chủ đề</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $catIcons = [
                                'general'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                                'account'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                                'cv'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                                'payment'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
                                'job'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                                'security' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                            ];
                        @endphp
                        <a href="{{ route('faq', ['category' => 'all']) }}"
                            class="group p-5 bg-white rounded-xl border {{ $selectedCategory === 'all' ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-100' }} hover:border-indigo-300 hover:shadow-md transition">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3 group-hover:bg-indigo-500 group-hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            </div>
                            <p class="font-semibold text-slate-900 text-sm">Tất cả</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $totalFaqs }} câu hỏi</p>
                        </a>
                        @foreach($categories as $key => $label)
                            @php $count = $byCategory[$key]->count() ?? 0; @endphp
                            <a href="{{ route('faq', ['category' => $key]) }}"
                                class="group p-5 bg-white rounded-xl border {{ $selectedCategory === $key ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-100' }} hover:border-indigo-300 hover:shadow-md transition">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3 group-hover:bg-indigo-500 group-hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $catIcons[$key] ?? '' !!}</svg>
                                </div>
                                <p class="font-semibold text-slate-900 text-sm">{{ $label }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $count }} câu hỏi</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Popular FAQs --}}
                <div>
                    <div class="mb-6">
                        <span class="text-xs font-semibold tracking-[0.18em] uppercase text-indigo-500">Xu hướng</span>
                        <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mt-1">Phổ biến nhất</h2>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                        @forelse($popularFaqs as $idx => $p)
                            <a href="#faq-{{ $p->id }}"
                                class="flex items-start gap-3 px-5 py-4 {{ $idx !== count($popularFaqs) - 1 ? 'border-b border-slate-100' : '' }} hover:bg-slate-50 transition group">
                                <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-500 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-indigo-500 group-hover:text-white transition">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 line-clamp-2 leading-snug">{{ $p->question }}</p>
                                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ number_format($p->views_count) }} lượt xem
                                    </p>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-slate-400">
                                Chưa có dữ liệu
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====================== FAQ LIST + FILTERS ====================== --}}
    <section class="py-16 px-6">
        <div class="max-w-4xl mx-auto"
             x-data="{
                openId: null,
                search: '',
                setOpen(id) { this.openId = this.openId === id ? null : id }
             }">

            {{-- Section heading & filter chips --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="text-xs font-semibold tracking-[0.18em] uppercase text-indigo-500">Câu hỏi</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mt-1">
                        @if($selectedCategory !== 'all')
                            {{ $categories[$selectedCategory] ?? 'Tất cả' }}
                        @else
                            Tất cả câu hỏi
                        @endif
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Hiển thị <span class="font-medium text-slate-700">{{ $faqs->count() }}</span> câu hỏi
                    </p>
                </div>

                {{-- Quick search within section --}}
                <div class="relative w-full md:w-72">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Lọc nhanh..."
                        class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>
            </div>

            {{-- Filter chips (quick category switch) --}}
            <div class="flex flex-wrap gap-2 mb-8">
                <a href="{{ route('faq') }}"
                    class="px-4 py-2 rounded-full text-xs font-medium transition {{ $selectedCategory === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-400' }}">
                    Tất cả
                </a>
                @foreach($categories as $key => $label)
                    <a href="{{ route('faq', ['category' => $key]) }}"
                        class="px-4 py-2 rounded-full text-xs font-medium transition {{ $selectedCategory === $key ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-400' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- FAQ Accordion --}}
            @if($faqs->count() > 0)
                <div class="space-y-3">
                    @foreach($faqs as $faq)
                        <div id="faq-{{ $faq->id }}"
                            x-show="search === '' || '{{ strtolower(addslashes($faq->question . ' ' . strip_tags($faq->answer))) }}'.includes(search.toLowerCase())"
                            x-data="{ open: false }"
                            x-transition.opacity
                            class="bg-white rounded-xl border border-slate-200 hover:border-indigo-200 transition overflow-hidden">

                            {{-- Question button --}}
                            <button @click="open = !open; setOpen({{ $faq->id }})"
                                :aria-expanded="open"
                                class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left group">

                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    {{-- Category badge --}}
                                    <span class="hidden md:inline-flex px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider shrink-0">
                                        {{ $categories[$faq->category] ?? 'Khác' }}
                                    </span>

                                    {{-- Question --}}
                                    <h3 class="text-base font-semibold text-slate-900 group-hover:text-indigo-600 transition leading-snug">
                                        {{ $faq->question }}
                                    </h3>
                                </div>

                                {{-- Chevron --}}
                                <div class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-indigo-50 flex items-center justify-center shrink-0 transition"
                                    :class="{ '!bg-indigo-500': open }">
                                    <svg class="w-4 h-4 text-slate-500 transition"
                                         :class="{ 'rotate-180 text-white': open }"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </button>

                            {{-- Answer --}}
                            <div x-show="open"
                                x-collapse
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="border-t border-slate-100">
                                <div class="px-6 py-5 bg-slate-50/50">
                                    <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed text-[15px]">
                                        {!! nl2br(e($faq->answer)) !!}
                                    </div>

                                    {{-- Helpful / Footer --}}
                                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex items-center gap-2 text-xs text-slate-500">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            {{ number_format($faq->views_count) }} lượt xem
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-500 mr-1">Có hữu ích?</span>
                                            <button class="px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg hover:border-emerald-400 hover:bg-emerald-50 hover:text-emerald-700 transition">
                                                👍 Có
                                            </button>
                                            <button class="px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg hover:border-rose-400 hover:bg-rose-50 hover:text-rose-700 transition">
                                                👎 Không
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty state for in-section filter --}}
                <div x-show="search !== '' && $refs.noMatch"
                     x-cloak
                     class="text-center py-12 text-slate-400">
                    Không tìm thấy kết quả phù hợp
                </div>
            @else
                {{-- Empty state --}}
                <div class="text-center py-16 bg-slate-50 rounded-2xl">
                    <div class="w-16 h-16 mx-auto rounded-full bg-white flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-1">Không tìm thấy kết quả</h3>
                    <p class="text-sm text-slate-500 mb-4">
                        @if($search)
                            Không có câu hỏi nào khớp với từ khóa "{{ $search }}"
                        @else
                            Chưa có câu hỏi nào trong chủ đề này
                        @endif
                    </p>
                    <a href="{{ route('faq') }}" class="inline-block px-5 py-2 bg-indigo-500 text-white text-sm font-medium rounded-lg hover:bg-indigo-600 transition">
                        Xem tất cả câu hỏi
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ====================== HELP CHANNELS / CTA ====================== --}}
    <section class="py-16 px-6 bg-slate-50 border-t border-slate-100">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <span class="text-xs font-semibold tracking-[0.18em] uppercase text-indigo-500">Vẫn cần trợ giúp?</span>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mt-2">Chọn cách bạn muốn liên hệ</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-5">
                {{-- Contact --}}
                <a href="{{ route('contact') }}"
                    class="group bg-white rounded-2xl p-7 border border-slate-100 hover:border-indigo-300 hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-5 group-hover:bg-indigo-500 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Gửi tin nhắn</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Phản hồi trong vòng 24 giờ qua email hỗ trợ.</p>
                </a>

                {{-- Email --}}
                <a href="mailto:support@cvactive.vn"
                    class="group bg-white rounded-2xl p-7 border border-slate-100 hover:border-indigo-300 hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-5 group-hover:bg-emerald-500 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Email trực tiếp</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">support@cvactive.vn — phản hồi nhanh chóng.</p>
                </a>

                {{-- Blog / Docs --}}
                <a href="{{ route('blog.index') }}"
                    class="group bg-white rounded-2xl p-7 border border-slate-100 hover:border-indigo-300 hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center mb-5 group-hover:bg-purple-500 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Đọc blog</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">Hướng dẫn chi tiết, mẹo viết CV và nhiều hơn nữa.</p>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================ CTA ============================ --}}
    <section class="bg-slate-900 py-16 px-6">
        <div class="max-w-3xl mx-auto text-center text-white">
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-4">Sẵn sàng tạo CV chuyên nghiệp?</h2>
            <p class="text-slate-400 mb-8">Hàng nghìn người đã thành công với CVactive. Đến lượt bạn.</p>
            <a href="{{ route('cv.create') }}"
                class="inline-flex items-center gap-2 px-8 py-4 bg-indigo-500 text-white font-semibold rounded-xl hover:bg-indigo-400 transition shadow-lg shadow-indigo-900/40">
                Tạo CV miễn phí ngay
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </section>

    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

@endsection