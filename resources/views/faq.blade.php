{{-- ========================================= HERO ========================================= --}}
@extends('layouts.app')

@push('styles')
<style>
    .hero-texture::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
        pointer-events: none;
        z-index: 1;
    }
    [x-cloak] { display: none !important; }
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('content')

    <section class="relative bg-[#0F172A] text-white pt-20 pb-24 overflow-hidden">
        <div class="hero-texture absolute inset-0"></div>
        <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">

            <span class="inline-block text-[11px] font-semibold tracking-[0.2em] uppercase text-indigo-400 mb-6">Trung tâm hỗ trợ</span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4 leading-[1.1]">
                Chúng tôi có thể giúp gì<br class="hidden sm:block"> cho bạn?
            </h1>
            <p class="text-slate-400 text-base md:text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
                Tìm câu trả lời nhanh cho những câu hỏi thường gặp. Không tìm thấy? Đội ngũ hỗ trợ luôn sẵn sàng.
            </p>

            {{-- Search --}}
            <form action="{{ route('faq') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="relative">
                    <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ $search ?? '' }}"
                        placeholder="Tìm kiếm câu hỏi, ví dụ: tạo CV, xuất PDF, thanh toán..."
                        class="w-full pl-14 pr-36 py-4 rounded-2xl bg-white/5 border border-white/10 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
                    <button type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 px-5 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-xl hover:bg-indigo-400 active:scale-[0.98] transition shadow-lg">
                        Tìm kiếm
                    </button>
                </div>
                @if($search)
                    <div class="mt-4 text-sm text-slate-400">
                        Kết quả cho <span class="text-white font-medium">"{{ $search }}"</span>
                        · <a href="{{ route('faq') }}" class="text-indigo-400 hover:underline">Xóa bộ lọc</a>
                    </div>
                @endif
            </form>

            {{-- Quick stats --}}
            <div class="grid grid-cols-3 gap-4 max-w-xl mx-auto mt-12">
                <div>
                    <div class="text-2xl md:text-3xl font-black text-white">{{ $totalFaqs }}+</div>
                    <div class="text-[11px] text-slate-400 mt-1 uppercase tracking-wider">Câu hỏi</div>
                </div>
                <div class="border-x border-white/10">
                    <div class="text-2xl md:text-3xl font-black text-white">24/7</div>
                    <div class="text-[11px] text-slate-400 mt-1 uppercase tracking-wider">Hỗ trợ</div>
                </div>
                <div>
                    <div class="text-2xl md:text-3xl font-black text-white">{{ count($categories) }}</div>
                    <div class="text-[11px] text-slate-400 mt-1 uppercase tracking-wider">Chủ đề</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================================= CATEGORIES + POPULAR ======================================= --}}
    <section class="py-16 px-6 bg-white border-b border-slate-100">
        <div class="max-w-5xl mx-auto">
            <div class="grid lg:grid-cols-3 gap-10">

                {{-- Categories grid --}}
                <div class="lg:col-span-2">
                    <div class="mb-6">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-indigo-600">Khám phá</span>
                        <h2 class="text-2xl font-black text-slate-900 mt-1">Theo chủ đề</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <a href="{{ route('faq', ['category' => 'all']) }}"
                            class="p-5 bg-white rounded-xl border {{ $selectedCategory === 'all' ? 'border-indigo-600 ring-2 ring-indigo-100' : 'border-slate-100 hover:border-indigo-200 hover:shadow-sm' }} transition">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            </div>
                            <p class="font-semibold text-sm text-slate-900">Tất cả</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $totalFaqs }} câu hỏi</p>
                        </a>
                        @foreach($categories as $key => $label)
                        <a href="{{ route('faq', ['category' => $key]) }}"
                            class="p-5 bg-white rounded-xl border {{ $selectedCategory === $key ? 'border-indigo-600 ring-2 ring-indigo-100' : 'border-slate-100 hover:border-indigo-200 hover:shadow-sm' }} transition">
                            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.826 1.958-3.28 3.772-3.28m4 0c1.814 0 3.223.454 3.772 1.28M12 14c-3.314 0-6-2.686-6-6s2.686-6 6-6 6 2.686 6 6-2.686 6-6 6zm-1 4a9 9 0 100-18 9 9 0 0018 0z"/></svg>
                            </div>
                            <p class="font-semibold text-sm text-slate-900">{{ $label }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $byCategory[$key]->count() ?? 0 }} câu hỏi</p>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Popular --}}
                <div>
                    <div class="mb-6">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-indigo-600">Xu hướng</span>
                        <h2 class="text-2xl font-black text-slate-900 mt-1">Phổ biến nhất</h2>
                    </div>
                    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
                        @forelse($popularFaqs as $idx => $p)
                        <a href="#faq-{{ $p->id }}"
                            class="flex items-start gap-3 px-5 py-4 {{ $idx < count($popularFaqs) - 1 ? 'border-b border-slate-100' : '' }} hover:bg-slate-50 transition group">
                            <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-500 text-xs font-bold flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-indigo-600 group-hover:text-white transition">{{ $idx + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 line-clamp-2 leading-snug">{{ $p->question }}</p>
                                <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ number_format($p->views_count) }} lượt xem
                                </p>
                            </div>
                        </a>
                        @empty
                        <p class="px-5 py-8 text-sm text-slate-400 text-center">Chưa có dữ liệu</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================================= FAQ ACCORDION ======================================= --}}
    <section class="py-16 px-6" x-data="{ openId: null, search: '', setOpen(id) { this.openId = this.openId === id ? null : id } }">

        <div class="max-w-3xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-widest text-indigo-600">Câu hỏi</span>
                    <h2 class="text-2xl font-black text-slate-900 mt-1">
                        @if($selectedCategory !== 'all')
                            {{ $categories[$selectedCategory] ?? 'Tất cả' }}
                        @else
                            Tất cả câu hỏi
                        @endif
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $faqs->count() }} câu hỏi</p>
                </div>

                {{-- Quick search --}}
                <div class="relative w-full md:w-72">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search"
                        placeholder="Lọc nhanh..."
                        class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 transition bg-white">
                </div>
            </div>

            {{-- Filter chips --}}
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

            {{-- Accordion --}}
            @if($faqs->count() > 0)
            <div class="space-y-3">
                @foreach($faqs as $faq)
                <div id="faq-{{ $faq->id }}"
                    x-show="search === '' || '{{ strtolower(addslashes($faq->question . ' ' . strip_tags($faq->answer))) }}'.includes(search.toLowerCase())"
                    x-data="{ open: false }"
                    class="bg-white rounded-xl border border-slate-200 hover:border-indigo-200 transition overflow-hidden">

                    <button @click="open = !open; setOpen({{ $faq->id }})"
                        :aria-expanded="open"
                        class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left group">

                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <span class="hidden md:inline-flex px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-wider shrink-0">
                                {{ $categories[$faq->category] ?? 'Khác' }}
                            </span>
                            <h3 class="text-base font-semibold text-slate-900 group-hover:text-indigo-600 transition leading-snug">
                                {{ $faq->question }}
                            </h3>
                        </div>

                        <div class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-indigo-50 flex items-center justify-center shrink-0 transition"
                            :class="{ 'bg-indigo-500': open }">
                            <svg class="w-4 h-4 text-slate-500 transition"
                                 :class="{ 'rotate-180 text-white': open }"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>

                    <div x-show="open"
                        class="border-t border-slate-100">
                        <div class="px-6 py-5 bg-slate-50/50">
                            <p class="text-[15px] text-slate-600 leading-relaxed">{!! nl2br(e($faq->answer)) !!}</p>
                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-1.5 text-xs text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ number_format($faq->views_count) }} lượt xem
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
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
                <a href="{{ route('faq') }}" class="inline-block px-5 py-2 bg-indigo-500 text-white text-sm font-medium rounded-lg hover:bg-indigo-600 transition">Xem tất cả câu hỏi</a>
            </div>
            @endif
        </div>
    </section>

    {{-- ======================================= HELP CHANNELS ======================================= --}}
    <section class="py-16 px-6 bg-slate-50 border-t border-slate-100">
        <div class="max-w-3xl mx-auto text-center mb-10">
            <span class="text-[11px] font-bold uppercase tracking-widest text-indigo-600">Vẫn cần trợ giúp?</span>
            <h2 class="text-2xl font-black text-slate-900 mt-2">Chọn cách liên hệ bạn thích</h2>
        </div>

        <div class="max-w-3xl mx-auto grid sm:grid-cols-3 gap-5">
            <a href="{{ route('contact') }}"
                class="bg-white rounded-2xl p-7 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition text-center group">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto mb-5 group-hover:bg-indigo-500 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900 mb-2">Gửi tin nhắn</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Phản hồi trong 24 giờ qua email.</p>
            </a>

            <a href="mailto:support@cvactive.vn"
                class="bg-white rounded-2xl p-7 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition text-center group">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-5 group-hover:bg-emerald-500 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900 mb-2">Email trực tiếp</h3>
                <p class="text-sm text-slate-500 leading-relaxed">support@cvactive.vn</p>
            </a>

            <a href="{{ route('blog.index') }}"
                class="bg-white rounded-2xl p-7 border border-slate-100 hover:border-indigo-200 hover:shadow-md transition text-center group">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto mb-5 group-hover:bg-indigo-500 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900 mb-2">Đọc blog</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Hướng dẫn, mẹo viết CV và hơn thế nữa.</p>
            </a>
        </div>
    </section>

    {{-- ======================================= CTA ======================================= --}}
    <section class="py-16 px-6 bg-[#0F172A]">
        <div class="max-w-2xl mx-auto text-center text-white">
            <h2 class="text-3xl md:text-4xl font-black tracking-tight mb-4">Sẵn sàng tạo CV chuyên nghiệp?</h2>
            <p class="text-slate-400 mb-8">Hàng nghìn người đã thành công với CVactive. Đến lượt bạn.</p>
            <a href="{{ route('cv.create') }}"
                class="inline-flex items-center gap-2 px-10 py-4 bg-indigo-500 text-white font-semibold rounded-xl hover:bg-indigo-400 active:scale-[0.98] transition shadow-xl shadow-indigo-900/40">
                Tạo CV miễn phí
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </section>

@endsection
