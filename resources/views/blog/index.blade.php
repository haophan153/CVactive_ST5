{{-- HERO --}}
@extends('layouts.app')

@push('styles')
<style>
    .hero-grid {
        background-image:
            linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
        background-size: 60px 60px;
    }
    .card-hover {
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.08);
    }
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endpush

@section('content')

    {{-- ══════════════════════ HERO (asymmetric 55/45 split, navy bg, no gradient slop) ══ --}}
    <section class="bg-[#0F172A] text-white pt-16 pb-20 relative overflow-hidden">
        <div class="hero-grid absolute inset-0 opacity-100"></div>
        <div class="max-w-6xl mx-auto px-6 relative z-10">

            <div class="grid lg:grid-cols-[55fr_45fr] gap-12 lg:gap-16 items-center">

                {{-- LEFT: Copy + search + stats --}}
                <div>
                    <div class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 px-3 py-1 rounded-full text-xs font-medium mb-6">
                        {{ $posts->total() }} bài viết
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight mb-4">
                        Blog <span class="text-indigo-400">CVactive</span>
                    </h1>
                    <p class="text-indigo-200 text-base max-w-[44ch] mb-8">
                        Cập nhật tin tức, mẹo viết CV và hướng dẫn nghề nghiệp hàng tuần.
                    </p>

                    {{-- Search form --}}
                    <form method="GET" action="{{ route('blog.index') }}" class="flex flex-col sm:flex-row gap-2 max-w-lg">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div class="relative flex-1">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                            <input type="search" name="q" value="{{ $term }}" placeholder="Tìm bài viết, chủ đề..."
                                class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white text-gray-900 placeholder-gray-400 border-0 focus:ring-2 focus:ring-indigo-300 shadow-xl text-sm">
                        </div>
                        <button type="submit" class="px-6 py-3.5 rounded-xl bg-indigo-500 text-white font-semibold hover:bg-indigo-400 active:scale-[0.98] transition shadow-xl shadow-indigo-900/40 text-sm shrink-0">
                            Tìm kiếm
                        </button>
                    </form>

                    {{-- Stats strip --}}
                    <div class="flex gap-8 mt-8">
                        <div>
                            <div class="text-3xl font-extrabold text-white">{{ number_format($stats['total_posts']) }}</div>
                            <div class="text-xs text-indigo-300 mt-1">Bài viết</div>
                        </div>
                        <div class="w-px bg-indigo-800/50"></div>
                        <div>
                            <div class="text-3xl font-extrabold text-white">{{ number_format($stats['total_views']) }}+</div>
                            <div class="text-xs text-indigo-300 mt-1">Lượt đọc</div>
                        </div>
                        <div class="w-px bg-indigo-800/50"></div>
                        <div>
                            <div class="text-3xl font-extrabold text-white">{{ number_format($stats['total_authors']) }}</div>
                            <div class="text-xs text-indigo-300 mt-1">Tác giả</div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Featured post card (desktop only) --}}
                @if($featured)
                <div class="hidden lg:block">
                    <a href="{{ route('blog.show', $featured->slug) }}" class="group block bg-white rounded-2xl overflow-hidden shadow-2xl shadow-indigo-950/30 card-hover">
                        <div class="aspect-[4/3] overflow-hidden">
                            @if($featured->featured_image)
                                <img src="{{ asset('storage/'.$featured->featured_image) }}" alt="{{ $featured->title }}"
                                    class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="inline-flex items-center gap-1.5 bg-amber-400 text-indigo-950 px-2.5 py-1 rounded-full text-[11px] font-bold mb-3">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.367 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.65 8.384c-.783-.57-.38-1.81.588-1.81H7.4a1 1 0 00.95-.69L9.05 2.927z"/></svg>
                                NỔI BẬT
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-indigo-600 transition">{{ $featured->title }}</h3>
                            <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $featured->excerpt_truncated }}</p>
                            <div class="mt-4 flex items-center justify-between text-xs text-gray-400">
                                <span class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                        {{ mb_substr($featured->author->name ?? 'A', 0, 1) }}
                                    </span>
                                    {{ $featured->author->name }}
                                </span>
                                <span>{{ $featured->published_at?->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════ CATEGORIES ══ --}}
    @if($categories->count())
    <section class="max-w-6xl mx-auto px-6 mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-900">Chủ đề phổ biến</h2>
            <a href="{{ route('blog.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-3">
            @php $activeCategory = request('category'); @endphp
            <a href="{{ route('blog.index') }}"
                class="group rounded-xl border-2 p-4 transition card-hover {{ !$activeCategory ? 'border-indigo-600 bg-indigo-50' : 'border-slate-100 bg-white hover:border-indigo-200 hover:shadow-sm' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ !$activeCategory ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-indigo-600' }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-slate-900 text-sm">Tất cả</div>
                        <div class="text-xs text-slate-500">{{ $stats['total_posts'] }} bài</div>
                    </div>
                </div>
            </a>
            @foreach($categories->take(7) as $cat)
                @php $colors = $cat->color_classes; @endphp
                <a href="{{ route('blog.index', ['category' => $cat->slug]) }}"
                    class="group rounded-xl border-2 p-4 transition card-hover {{ $activeCategory === $cat->slug ? "border-indigo-600 {$colors['bg']}" : 'border-slate-100 bg-white hover:border-slate-200 hover:shadow-sm' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $colors['bg'] }} {{ $colors['text'] }} flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900 text-sm truncate">{{ $cat->name }}</div>
                            <div class="text-xs text-slate-500">{{ $cat->posts_count }} bài</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════ MAIN + SIDEBAR ══ --}}
    <div class="max-w-6xl mx-auto px-6 mt-12 pb-16">
        <div class="grid lg:grid-cols-[1fr_320px] gap-10">

            {{-- LEFT: post grid --}}
            <div>
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">
                            @if($term)
                                Kết quả cho "{{ $term }}"
                            @elseif(request('category'))
                                {{ optional($categories->firstWhere('slug', request('category')))->name ?? 'Bài viết' }}
                            @else
                                Bài viết mới nhất
                            @endif
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">{{ $posts->total() }} bài viết được tìm thấy</p>
                    </div>
                </div>

                @if($posts->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-slate-200">
                    <div class="w-16 h-16 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900">Không tìm thấy bài viết nào</h3>
                    <p class="text-sm text-slate-500 mt-1">Hãy thử từ khóa khác hoặc chọn chủ đề khác.</p>
                    <a href="{{ route('blog.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 active:scale-[0.98] transition">Xem tất cả bài viết</a>
                </div>
                @else
                <div class="grid sm:grid-cols-2 gap-6">
                    @foreach($posts as $post)
                        @php $colors = $post->category?->color_classes ?? ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'solid' => 'bg-indigo-600']; @endphp
                        <article class="group bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden card-hover">
                            <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-[16/10] overflow-hidden relative">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-indigo-50 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/></svg>
                                    </div>
                                @endif
                                @if($post->is_featured)
                                    <div class="absolute top-3 left-3 inline-flex items-center gap-1 bg-amber-400 text-indigo-950 px-2.5 py-1 rounded-full text-[11px] font-bold shadow-lg">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.367 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.65 8.384c-.783-.57-.38-1.81.588-1.81H7.4a1 1 0 00.95-.69L9.05 2.927z"/></svg>
                                        Nổi bật
                                    </div>
                                @endif
                            </a>
                            <div class="p-5">
                                @if($post->category)
                                    <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                                        class="inline-flex items-center gap-1 {{ $colors['bg'] }} {{ $colors['text'] }} px-2.5 py-1 rounded-full text-xs font-semibold hover:opacity-80 transition">
                                        {{ $post->category->name }}
                                    </a>
                                @endif
                                <h3 class="text-base font-bold text-slate-900 mt-3 mb-2 line-clamp-2 leading-snug">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-indigo-600 transition">{{ $post->title }}</a>
                                </h3>
                                @if($post->excerpt)
                                    <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed">{{ $post->excerpt }}</p>
                                @endif
                                <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-50 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-[11px]">
                                            {{ mb_substr($post->author->name ?? 'A', 0, 1) }}
                                        </div>
                                        <span class="text-slate-700 font-medium">{{ $post->author->name ?? 'Admin' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-slate-400">
                                        <span>{{ $post->reading_time_label }}</span>
                                        <span class="text-slate-200">|</span>
                                        <span>{{ $post->published_at?->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">{{ $posts->links() }}</div>
                @endif
            </div>

            {{-- RIGHT: sidebar --}}
            <aside class="space-y-6">

                {{-- Popular --}}
                @if($popular->count())
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.24 17 7.317 17.91 9.393 18 11 18 12s-.1 2.5-1 3.5c.5-.5 1.5-1 2.5-1 .5 1.5 0 3-1 4z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900">Đọc nhiều nhất</h3>
                    </div>
                    <ol class="space-y-4">
                        @foreach($popular as $i => $p)
                            <li class="flex gap-3 group">
                                <span class="text-2xl font-extrabold {{ $i < 3 ? 'text-rose-500' : 'text-slate-200' }} leading-none w-6">
                                    {{ $i + 1 }}
                                </span>
                                <a href="{{ route('blog.show', $p->slug) }}" class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-slate-900 group-hover:text-indigo-600 line-clamp-2 leading-snug transition">{{ $p->title }}</h4>
                                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                        <span>{{ number_format($p->views_count) }} lượt xem</span>
                                        <span class="text-slate-200">|</span>
                                        <span>{{ $p->reading_time_label }}</span>
                                    </p>
                                </a>
                            </li>
                            @if(!$loop->last)<hr class="border-slate-50">@endif
                        @endforeach
                    </ol>
                </div>
                @endif

                {{-- Recent --}}
                @if($recent->count())
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900">Mới cập nhật</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach($recent as $r)
                            <a href="{{ route('blog.show', $r->slug) }}" class="flex gap-3 group">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gradient-to-br from-slate-100 to-indigo-50 flex-shrink-0">
                                    @if($r->featured_image)
                                        <img src="{{ asset('storage/'.$r->featured_image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-slate-900 group-hover:text-indigo-600 line-clamp-2 leading-snug transition">{{ $r->title }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">{{ $r->published_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- CTA card (navy bg, matches welcome page hero) --}}
                <div class="bg-[#0F172A] rounded-2xl p-6 text-white relative overflow-hidden">
                    <div class="absolute inset-0 hero-grid opacity-50"></div>
                    <div class="relative">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold leading-tight mb-2">Áp dụng ngay vào CV của bạn</h3>
                        <p class="text-sm text-indigo-200 leading-relaxed">Tạo CV chuyên nghiệp với template được HR hàng đầu khuyên dùng.</p>
                        <a href="{{ route('cv.create') }}"
                            class="mt-5 inline-flex items-center gap-2 bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-400 active:scale-[0.98] transition shadow-lg">
                            Tạo CV miễn phí
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Newsletter (slate tones, matches welcome page palette) --}}
                <div class="bg-slate-50 rounded-2xl border border-slate-100 p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <h3 class="font-bold text-slate-900">Nhận bài viết mới</h3>
                    </div>
                    <p class="text-sm text-slate-500 mb-4 leading-relaxed">Đăng ký để nhận bài viết mới nhất qua email mỗi tuần.</p>
                    <form class="space-y-2">
                        <input type="email" placeholder="email@example.com"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 transition">
                        <button type="button" class="w-full bg-indigo-600 text-white px-4 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 active:scale-[0.98] transition text-sm">
                            Đăng ký
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    </div>

@endsection
