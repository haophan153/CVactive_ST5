{{-- HERO --}}
@extends('layouts.app')

@section('content')

    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] bg-indigo-600 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-violet-600 rounded-full blur-[80px]"></div>
        </div>
        <div class="relative max-w-6xl mx-auto px-4 py-14 lg:py-20">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 px-3 py-1 rounded-full text-xs font-medium mb-5">
                    {{ $posts->total() }} bài viết
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight">
                    Blog <span class="bg-gradient-to-r from-amber-300 via-pink-300 to-indigo-300 bg-clip-text text-transparent">CVactive</span>
                </h1>
                <p class="mt-4 text-indigo-200 text-base max-w-xl mx-auto">
                    Cập nhật tin tức, mẹo viết CV và hướng dẫn nghề nghiệp hàng tuần.
                </p>
            </div>
        </div>
    </section>

    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white">
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <div class="absolute -top-20 -left-20 w-80 h-80 bg-white/30 rounded-full blur-3xl"></div>
            <div class="absolute top-20 right-0 w-96 h-96 bg-fuchsia-300/40 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 left-1/3 w-96 h-96 bg-indigo-300/40 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-6xl mx-auto px-4 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/20 px-3 py-1 rounded-full text-xs font-medium mb-5">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        Cập nhật mỗi tuần
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight">
                        Kiến thức nghề nghiệp<br>
                        <span class="bg-gradient-to-r from-amber-200 to-pink-200 bg-clip-text text-transparent">cho người đi làm</span>
                    </h1>
                    <p class="mt-4 text-indigo-100 text-base lg:text-lg max-w-lg">
                        Cẩm nang viết CV, phỏng vấn, đàm phán lương và phát triển sự nghiệp từ các chuyên gia HR hàng đầu.
                    </p>

                    <form method="GET" action="{{ route('blog.index') }}" class="mt-8 flex flex-col sm:flex-row gap-2 max-w-xl">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div class="relative flex-1">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                            <input type="search" name="q" value="{{ $term }}" placeholder="Tìm bài viết, chủ đề..."
                                class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white text-gray-900 placeholder-gray-400 border-0 focus:ring-2 focus:ring-amber-300 shadow-xl">
                        </div>
                        <button type="submit" class="px-6 py-3.5 rounded-xl bg-amber-400 text-indigo-950 font-bold hover:bg-amber-300 transition shadow-xl">
                            Tìm kiếm
                        </button>
                    </form>

                    <div class="mt-8 grid grid-cols-3 gap-4 max-w-md">
                        <div>
                            <div class="text-2xl font-extrabold">{{ number_format($stats['total_posts']) }}</div>
                            <div class="text-xs text-indigo-200 mt-1">Bài viết</div>
                        </div>
                        <div>
                            <div class="text-2xl font-extrabold">{{ number_format($stats['total_views']) }}+</div>
                            <div class="text-xs text-indigo-200 mt-1">Lượt đọc</div>
                        </div>
                        <div>
                            <div class="text-2xl font-extrabold">{{ number_format($stats['total_authors']) }}</div>
                            <div class="text-xs text-indigo-200 mt-1">Tác giả</div>
                        </div>
                    </div>
                </div>

                @if($featured)
                <a href="{{ route('blog.show', $featured->slug) }}" class="group relative block rounded-3xl overflow-hidden bg-white/10 backdrop-blur border border-white/20 shadow-2xl hover:shadow-amber-400/20 transition">
                    <div class="aspect-[4/3] overflow-hidden">
                        @if($featured->featured_image)
                            <img src="{{ asset('storage/'.$featured->featured_image) }}" alt="{{ $featured->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-amber-300/40 to-pink-300/40 flex items-center justify-center">
                                <svg class="w-20 h-20 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="inline-flex items-center gap-2 bg-amber-400 text-indigo-950 px-2.5 py-1 rounded-full text-xs font-bold mb-3">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.286 3.957c.3.921-.755 1.688-1.54 1.118L10 14.347l-3.367 2.446c-.784.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.65 8.384c-.783-.57-.38-1.81.588-1.81H7.4a1 1 0 00.95-.69L9.05 2.927z"/></svg>
                            BÀI VIẾT NỔI BẬT
                        </div>
                        <h3 class="text-xl font-bold leading-snug line-clamp-2 group-hover:text-amber-200 transition">{{ $featured->title }}</h3>
                        <p class="mt-2 text-sm text-indigo-100 line-clamp-2">{{ $featured->excerpt_truncated }}</p>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2">
                                <span class="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center font-bold">
                                    {{ mb_substr($featured->author->name ?? 'A', 0, 1) }}
                                </span>
                                {{ $featured->author->name }}
                            </span>
                            <span>{{ $featured->published_at?->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </section>

    {{-- CATEGORIES --}}
    @if($categories->count())
    <section class="max-w-6xl mx-auto px-4 mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900">Chủ đề phổ biến</h2>
            <a href="{{ route('blog.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Xem tất cả →</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            <a href="{{ route('blog.index') }}"
                class="group relative rounded-2xl border-2 p-4 transition {{ !request('category') ? 'border-indigo-600 bg-indigo-50' : 'border-gray-100 bg-white hover:border-indigo-200 hover:bg-indigo-50/50' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Tất cả</div>
                        <div class="text-xs text-gray-500">{{ $stats['total_posts'] }} bài</div>
                    </div>
                </div>
            </a>
            @foreach($categories->take(7) as $cat)
                @php $colors = $cat->color_classes; @endphp
                <a href="{{ route('blog.index', ['category' => $cat->slug]) }}"
                    class="group relative rounded-2xl border-2 p-4 transition {{ request('category') === $cat->slug ? "border-indigo-600 {$colors['bg']}" : 'border-gray-100 bg-white hover:border-gray-200 hover:shadow-sm' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $colors['bg'] }} {{ $colors['text'] }} flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900 text-sm truncate">{{ $cat->name }}</div>
                            <div class="text-xs text-gray-500">{{ $cat->posts_count }} bài</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- MAIN + SIDEBAR --}}
    <div class="max-w-6xl mx-auto px-4 mt-12 pb-12">
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- LEFT: post grid --}}
            <div class="lg:col-span-2">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            @if($term)
                                Kết quả cho "{{ $term }}"
                            @elseif(request('category'))
                                {{ optional($categories->firstWhere('slug', request('category')))->name ?? 'Bài viết' }}
                            @else
                                Bài viết mới nhất
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">{{ $posts->total() }} bài viết được tìm thấy</p>
                    </div>
                </div>

                @if($posts->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                    <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Không tìm thấy bài viết nào</h3>
                    <p class="text-sm text-gray-500 mt-1">Hãy thử từ khóa khác hoặc chọn chủ đề khác.</p>
                    <a href="{{ route('blog.index') }}" class="inline-block mt-4 px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Xem tất cả bài viết</a>
                </div>
                @else
                <div class="grid sm:grid-cols-2 gap-6">
                    @foreach($posts as $post)
                        @php $colors = $post->category?->color_classes ?? ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'solid' => 'bg-indigo-600']; @endphp
                        <article class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-[16/10] overflow-hidden relative">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-100 via-violet-100 to-fuchsia-100 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2"/></svg>
                                    </div>
                                @endif
                                @if($post->is_featured)
                                    <div class="absolute top-3 left-3 inline-flex items-center gap-1 bg-amber-400 text-indigo-950 px-2.5 py-1 rounded-full text-xs font-bold shadow-lg">
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
                                <h3 class="text-lg font-bold text-gray-900 mt-3 mb-2 line-clamp-2 leading-snug">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-indigo-600 transition">{{ $post->title }}</a>
                                </h3>
                                @if($post->excerpt)
                                    <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">{{ $post->excerpt }}</p>
                                @endif
                                <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-50 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center font-bold">
                                            {{ mb_substr($post->author->name ?? 'A', 0, 1) }}
                                        </div>
                                        <span class="text-gray-600 font-medium">{{ $post->author->name ?? 'Admin' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-gray-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $post->reading_time_label }}
                                        </span>
                                        <span>·</span>
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
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.24 17 7.317 17.91 9.393 18 11 18 12s-.1 2.5-1 3.5c.5-.5 1.5-1 2.5-1 .5 1.5 0 3-1 4z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-900">Đọc nhiều nhất</h3>
                    </div>
                    <ol class="space-y-4">
                        @foreach($popular as $i => $p)
                            <li class="flex gap-3 group">
                                <span class="text-2xl font-extrabold {{ $i < 3 ? 'text-rose-500' : 'text-gray-300' }} leading-none w-6">
                                    {{ $i + 1 }}
                                </span>
                                <a href="{{ route('blog.show', $p->slug) }}" class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 leading-snug transition">{{ $p->title }}</h4>
                                    <p class="text-xs text-gray-400 mt-1 flex items-center gap-2">
                                        <span>{{ number_format($p->views_count) }} lượt xem</span>
                                        <span>·</span>
                                        <span>{{ $p->reading_time_label }}</span>
                                    </p>
                                </a>
                            </li>
                            @if(!$loop->last)<hr class="border-gray-50">@endif
                        @endforeach
                    </ol>
                </div>
                @endif

                {{-- Recent --}}
                @if($recent->count())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-900">Mới cập nhật</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach($recent as $r)
                            <a href="{{ route('blog.show', $r->slug) }}" class="flex gap-3 group">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gradient-to-br from-sky-100 to-indigo-100 flex-shrink-0">
                                    @if($r->featured_image)
                                        <img src="{{ asset('storage/'.$r->featured_image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 leading-snug transition">{{ $r->title }}</h4>
                                    <p class="text-xs text-gray-400 mt-1">{{ $r->published_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- CTA --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white p-6">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold leading-tight">Áp dụng ngay vào CV của bạn</h3>
                        <p class="text-sm text-indigo-100 mt-2">Tạo CV chuyên nghiệp với template được HR hàng đầu khuyên dùng.</p>
                        <a href="{{ route('cv.create') }}" class="mt-4 inline-flex items-center gap-2 bg-white text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-amber-300 hover:text-indigo-950 transition">
                            Tạo CV miễn phí
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Newsletter --}}
                <div class="bg-amber-50 rounded-2xl border border-amber-100 p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <h3 class="font-bold text-amber-900">Nhận bài viết mới</h3>
                    </div>
                    <p class="text-sm text-amber-800 mb-4">Đăng ký để nhận bài viết mới nhất qua email mỗi tuần.</p>
                    <form class="space-y-2">
                        <input type="email" placeholder="email@example.com" class="w-full px-4 py-2.5 rounded-lg border border-amber-200 bg-white focus:ring-2 focus:ring-amber-300 focus:border-amber-300 text-sm">
                        <button type="button" class="w-full bg-amber-500 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-amber-600 transition text-sm">Đăng ký</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>

@endsection
