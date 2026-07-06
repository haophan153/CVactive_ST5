@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('blog.index') }}" class="text-gray-500 hover:text-indigo-600 transition">Blog</a>
        <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
        @if($post->category)
            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="text-gray-500 hover:text-indigo-600 transition">{{ $post->category->name }}</a>
            <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
        @endif
        <span class="text-gray-700 font-medium truncate">{{ Str::limit($post->title, 50) }}</span>
    </div>
@endsection

@section('content')

    @php
        $colors = $post->category?->color_classes ?? ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'solid' => 'bg-indigo-600'];
        $shareUrl = urlencode(request()->fullUrl());
        $shareTitle = urlencode($post->title);
    @endphp

    {{-- HERO HEADER --}}
    <article class="bg-white">
        <div class="max-w-4xl mx-auto px-4 pt-12 pb-8 text-center">
            @if($post->category)
                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                    class="inline-flex items-center gap-1 {{ $colors['bg'] }} {{ $colors['text'] }} px-3 py-1 rounded-full text-xs font-bold mb-5 hover:opacity-80 transition">
                    {{ $post->category->name }}
                </a>
            @endif
            <h1 class="text-3xl lg:text-5xl font-extrabold text-gray-900 leading-tight tracking-tight">
                {{ $post->title }}
            </h1>
            @if($post->excerpt)
                <p class="mt-4 text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">{{ $post->excerpt }}</p>
            @endif

            <div class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center font-bold">
                        {{ mb_substr($post->author->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="text-left">
                        <div class="font-semibold text-gray-900">{{ $post->author->name ?? 'Admin' }}</div>
                        <div class="text-xs text-gray-500">Tác giả</div>
                    </div>
                </div>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <div class="flex items-center gap-1.5 text-gray-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $post->published_at?->format('d/m/Y') }}
                </div>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <div class="flex items-center gap-1.5 text-gray-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $post->reading_time_label }}
                </div>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <div class="flex items-center gap-1.5 text-gray-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ number_format($post->views_count) }} lượt xem
                </div>
            </div>
        </div>

        {{-- Featured Image --}}
        @if($post->featured_image)
        <div class="max-w-5xl mx-auto px-4">
            <div class="aspect-[21/9] overflow-hidden rounded-3xl shadow-2xl">
                <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        </div>
        @endif
    </article>

    {{-- BODY + SIDEBAR --}}
    <div class="max-w-6xl mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-12 gap-10">
            {{-- LEFT: share rail (desktop) --}}
            <div class="hidden lg:block lg:col-span-1">
                <div class="sticky top-24 space-y-3">
                    <span class="block text-[10px] uppercase tracking-widest text-gray-400 font-bold text-center">Share</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-sky-50 hover:text-sky-500 hover:border-sky-200 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                    </a>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ request()->fullUrl() }}'); this.classList.add('text-emerald-600');"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    </button>
                </div>
            </div>

            {{-- CENTER: content --}}
            <div class="lg:col-span-7">
                <div class="prose prose-lg prose-gray max-w-none text-gray-800 leading-loose">
                    {!! nl2br(e($post->content)) !!}
                </div>

                {{-- Tags + Author --}}
                <div class="mt-12 pt-8 border-t border-gray-100">
                    <div class="flex flex-wrap gap-2 mb-8">
                        <span class="text-sm font-semibold text-gray-500 mr-2">Chủ đề:</span>
                        @if($post->category)
                            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $colors['bg'] }} {{ $colors['text'] }} hover:opacity-80 transition">
                                #{{ $post->category->name }}
                            </a>
                        @endif
                        @if($post->is_featured)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">#nổi_bật</span>
                        @endif
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">#{{ $post->reading_time_label }}</span>
                    </div>

                    {{-- Author card --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-gradient-to-br from-indigo-50 to-violet-50 rounded-2xl p-6">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center text-xl font-bold flex-shrink-0">
                            {{ mb_substr($post->author->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-1">Tác giả bài viết</div>
                            <h4 class="font-bold text-gray-900 text-lg">{{ $post->author->name ?? 'Admin' }}</h4>
                            <p class="text-sm text-gray-600 mt-1">Chuyên gia HR với hơn 10 năm kinh nghiệm tuyển dụng tại các tập đoàn đa quốc gia.</p>
                        </div>
                        <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-lg bg-white text-indigo-700 text-sm font-semibold border border-indigo-200 hover:bg-indigo-600 hover:text-white transition">
                            Xem thêm bài viết
                        </a>
                    </div>

                    {{-- Mobile share --}}
                    <div class="lg:hidden mt-8 bg-white border border-gray-100 rounded-2xl p-4">
                        <div class="text-sm font-semibold text-gray-700 mb-3">Chia sẻ bài viết:</div>
                        <div class="flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-blue-50 text-blue-600 text-xs font-semibold hover:bg-blue-100">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-sky-50 text-sky-600 text-xs font-semibold hover:bg-sky-100">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231z"/></svg>
                                Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                                LinkedIn
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: sidebar --}}
            <aside class="lg:col-span-4 space-y-6">
                {{-- CTA --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white p-6">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold leading-tight">Áp dụng ngay</h3>
                        <p class="text-sm text-indigo-100 mt-2">Đã đọc xong? Tạo CV chuyên nghiệp chỉ trong 5 phút.</p>
                        <a href="{{ route('cv.create') }}" class="mt-4 inline-flex items-center gap-2 bg-white text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-amber-300 hover:text-indigo-950 transition">
                            Tạo CV miễn phí
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Related --}}
                @if($related->count())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-5 bg-indigo-600 rounded-full"></span>
                        Bài viết liên quan
                    </h3>
                    <div class="space-y-4">
                        @foreach($related as $rel)
                            @php $rc = $rel->category?->color_classes ?? ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700']; @endphp
                            <a href="{{ route('blog.show', $rel->slug) }}" class="flex gap-3 group">
                                <div class="w-20 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if($rel->featured_image)
                                        <img src="{{ asset('storage/'.$rel->featured_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-violet-100"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($rel->category)
                                        <span class="text-[10px] font-bold {{ $rc['text'] }} uppercase tracking-wider">{{ $rel->category->name }}</span>
                                    @endif
                                    <h4 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 line-clamp-2 leading-snug mt-1 transition">{{ $rel->title }}</h4>
                                    <p class="text-xs text-gray-400 mt-1">{{ $rel->published_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                            @if(!$loop->last)<hr class="border-gray-50">@endif
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Popular --}}
                @if($popular->count())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-5 bg-rose-500 rounded-full"></span>
                        Đọc nhiều nhất
                    </h3>
                    <ol class="space-y-3">
                        @foreach($popular as $i => $p)
                            <li class="flex gap-3 group">
                                <span class="text-xl font-extrabold {{ $i < 3 ? 'text-rose-500' : 'text-gray-300' }} leading-none w-6">{{ $i + 1 }}</span>
                                <a href="{{ route('blog.show', $p->slug) }}" class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-800 group-hover:text-indigo-600 line-clamp-2 leading-snug transition">{{ $p->title }}</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ number_format($p->views_count) }} lượt xem</p>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </div>
                @endif
            </aside>
        </div>

        {{-- Next post --}}
        @if($next)
        <a href="{{ route('blog.show', $next->slug) }}" class="mt-16 group block rounded-2xl overflow-hidden bg-gradient-to-r from-gray-900 to-indigo-900 text-white p-8 lg:p-10 hover:shadow-2xl transition">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div>
                    <div class="text-xs uppercase tracking-widest text-indigo-300 mb-3">Bài viết tiếp theo →</div>
                    <h3 class="text-2xl lg:text-3xl font-bold leading-tight group-hover:text-amber-300 transition">{{ $next->title }}</h3>
                    <p class="mt-2 text-indigo-200 text-sm">{{ $next->excerpt_truncated }}</p>
                </div>
                <div class="flex items-center gap-3 px-6 py-3 rounded-full bg-white/10 backdrop-blur group-hover:bg-white/20 transition">
                    <span class="font-semibold">Đọc tiếp</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
            </div>
        </a>
        @endif
    </div>

@endsection
