@extends('layouts.app')

@push('styles')
<style>
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
    .prose.prose-lg p { margin-bottom: 1.25em; }
    .prose.prose-lg h2 { font-size: 1.75rem; font-weight: 800; margin-top: 2em; margin-bottom: 0.75em; color: #0F172A; }
    .prose.prose-lg h3 { font-size: 1.4rem; font-weight: 700; margin-top: 1.5em; margin-bottom: 0.5em; color: #0F172A; }
    .prose.prose-lg a { color: #6366F1; text-decoration: none; }
    .prose.prose-lg a:hover { text-decoration: underline; }
    .prose.prose-lg blockquote { border-left: 3px solid #6366F1; padding-left: 1.25em; color: #475569; font-style: italic; }
</style>
@endpush

@section('header')
    <div class="flex items-center gap-2 text-sm">
        <a href="{{ route('blog.index') }}" class="text-slate-500 hover:text-indigo-600 transition">Blog</a>
        <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
        @if($post->category)
            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="text-slate-500 hover:text-indigo-600 transition">{{ $post->category->name }}</a>
            <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
        @endif
        <span class="text-slate-700 font-medium truncate">{{ Str::limit($post->title, 50) }}</span>
    </div>
@endsection

@section('content')

    @php
        $colors = $post->category?->color_classes ?? ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'solid' => 'bg-indigo-600'];
        $shareUrl = urlencode(request()->fullUrl());
        $shareTitle = urlencode($post->title);
    @endphp

    {{-- ═════════════════════════════════════ ARTICLE HEADER (centered, no gradient) ══ --}}
    <article class="bg-white">
        <div class="max-w-3xl mx-auto px-6 pt-12 pb-8 text-center">
            @if($post->category)
                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                    class="inline-flex items-center gap-1 {{ $colors['bg'] }} {{ $colors['text'] }} px-3 py-1 rounded-full text-xs font-bold mb-5 hover:opacity-80 transition">
                    {{ $post->category->name }}
                </a>
            @endif
            <h1 class="text-3xl lg:text-5xl font-extrabold text-slate-900 leading-tight tracking-tight">
                {{ $post->title }}
            </h1>
            @if($post->excerpt)
                <p class="mt-4 text-lg text-slate-500 max-w-2xl mx-auto leading-relaxed">{{ $post->excerpt }}</p>
            @endif

            <div class="mt-8 flex flex-wrap items-center justify-center gap-x-5 gap-y-3 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                        {{ mb_substr($post->author->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="text-left">
                        <div class="font-semibold text-slate-900">{{ $post->author->name ?? 'Admin' }}</div>
                        <div class="text-[11px] text-slate-500">Tác giả</div>
                    </div>
                </div>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <div class="text-slate-500">{{ $post->published_at?->format('d/m/Y') }}</div>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <div class="text-slate-500">{{ $post->reading_time_label }}</div>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <div class="text-slate-500">{{ number_format($post->views_count) }} lượt xem</div>
            </div>
        </div>

        {{-- Featured Image --}}
        @if($post->featured_image)
        <div class="max-w-5xl mx-auto px-6">
            <div class="aspect-[21/9] overflow-hidden rounded-2xl shadow-xl">
                <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        </div>
        @endif
    </article>

    {{-- ═════════════════════════════════════ BODY + SIDEBAR ══ --}}
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="grid lg:grid-cols-[1fr_320px] gap-10">

            {{-- LEFT: share rail (desktop) --}}
            <div class="hidden lg:block lg:w-12 lg:flex-shrink-0">
                <div class="sticky top-24 space-y-3">
                    <span class="block text-[10px] uppercase tracking-widest text-slate-400 font-bold text-center">Chia sẻ</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 active:scale-[0.92] transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-500 hover:border-sky-200 active:scale-[0.92] transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 active:scale-[0.92] transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                    </a>
                    <button type="button"
                        x-data="{ copied: false }"
                        @click="navigator.clipboard.writeText(@js(request()->fullUrl())); copied = true; setTimeout(() => copied = false, 2000);"
                        :class="copied ? 'text-emerald-600 border-emerald-200 bg-emerald-50' : 'text-slate-500'"
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 hover:bg-emerald-50 active:scale-[0.92] transition">
                        <svg x-show="!copied" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        <svg x-show="copied" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- CENTER: content --}}
            <div class="lg:flex-1 lg:min-w-0">
                <div class="prose prose-lg prose-slate max-w-none text-slate-800 leading-loose">
                    {!! nl2br(e($post->content)) !!}
                </div>

                {{-- Tags + Author --}}
                <div class="mt-12 pt-8 border-t border-slate-100">
                    <div class="flex flex-wrap gap-2 mb-8">
                        <span class="text-sm font-semibold text-slate-500 mr-2 self-center">Chủ đề:</span>
                        @if($post->category)
                            <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $colors['bg'] }} {{ $colors['text'] }} hover:opacity-80 transition">
                                #{{ $post->category->name }}
                            </a>
                        @endif
                        @if($post->is_featured)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">#nổi_bật</span>
                        @endif
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">#{{ $post->reading_time_label }}</span>
                    </div>

                    {{-- Author card (no fabricated bio) --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <div class="w-14 h-14 rounded-full bg-indigo-600 text-white flex items-center justify-center text-base font-bold flex-shrink-0">
                            {{ mb_substr($post->author->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-indigo-600 mb-1">Tác giả bài viết</div>
                            <h4 class="font-bold text-slate-900 text-base">{{ $post->author->name ?? 'Admin' }}</h4>
                            @if($post->author?->bio)
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $post->author->bio }}</p>
                            @endif
                        </div>
                        <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-xl bg-white text-indigo-700 text-sm font-semibold border border-slate-200 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 active:scale-[0.98] transition shrink-0">
                            Xem thêm bài viết
                        </a>
                    </div>

                    {{-- Mobile share --}}
                    <div class="lg:hidden mt-8 bg-white border border-slate-100 rounded-2xl p-4">
                        <div class="text-sm font-semibold text-slate-700 mb-3">Chia sẻ bài viết:</div>
                        <div class="flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-blue-50 text-blue-600 text-xs font-semibold hover:bg-blue-100 active:scale-[0.98] transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-sky-50 text-sky-600 text-xs font-semibold hover:bg-sky-100 active:scale-[0.98] transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231z"/></svg>
                                Twitter
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100 active:scale-[0.98] transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg>
                                LinkedIn
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: sidebar --}}
            <aside class="space-y-6 lg:w-80">

                {{-- CTA card (navy bg, matches welcome hero) --}}
                <div class="bg-[#0F172A] rounded-2xl p-6 text-white">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/30 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold leading-tight mb-2">Áp dụng ngay</h3>
                    <p class="text-sm text-indigo-200 leading-relaxed">Đã đọc xong? Tạo CV chuyên nghiệp chỉ trong 5 phút.</p>
                    <a href="{{ route('cv.create') }}"
                        class="mt-5 inline-flex items-center gap-2 bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-400 active:scale-[0.98] transition shadow-lg">
                        Tạo CV miễn phí
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>

                {{-- Related --}}
                @if($related->count())
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h3 class="font-bold text-slate-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-5 bg-indigo-600 rounded-full"></span>
                        Bài viết liên quan
                    </h3>
                    <div class="space-y-4">
                        @foreach($related as $rel)
                            @php $rc = $rel->category?->color_classes ?? ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700']; @endphp
                            <a href="{{ route('blog.show', $rel->slug) }}" class="flex gap-3 group">
                                <div class="w-20 h-16 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0">
                                    @if($rel->featured_image)
                                        <img src="{{ asset('storage/'.$rel->featured_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-slate-100 to-indigo-50"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($rel->category)
                                        <span class="text-[10px] font-bold {{ $rc['text'] }} uppercase tracking-wider">{{ $rel->category->name }}</span>
                                    @endif
                                    <h4 class="text-sm font-semibold text-slate-900 group-hover:text-indigo-600 line-clamp-2 leading-snug mt-1 transition">{{ $rel->title }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">{{ $rel->published_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                            @if(!$loop->last)<hr class="border-slate-50">@endif
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Popular --}}
                @if($popular->count())
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h3 class="font-bold text-slate-900 mb-5 flex items-center gap-2">
                        <span class="w-1 h-5 bg-rose-500 rounded-full"></span>
                        Đọc nhiều nhất
                    </h3>
                    <ol class="space-y-3">
                        @foreach($popular as $i => $p)
                            <li class="flex gap-3 group">
                                <span class="text-xl font-extrabold {{ $i < 3 ? 'text-rose-500' : 'text-slate-200' }} leading-none w-6">{{ $i + 1 }}</span>
                                <a href="{{ route('blog.show', $p->slug) }}" class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-slate-800 group-hover:text-indigo-600 line-clamp-2 leading-snug transition">{{ $p->title }}</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ number_format($p->views_count) }} lượt xem</p>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </div>
                @endif
            </aside>
        </div>

        {{-- Next post (navy, matches welcome page CTA) --}}
        @if($next)
        <a href="{{ route('blog.show', $next->slug) }}" class="mt-16 group block rounded-2xl overflow-hidden bg-[#0F172A] text-white p-8 lg:p-10 hover:shadow-2xl transition card-hover">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div>
                    <div class="text-xs uppercase tracking-widest text-indigo-300 mb-3">Bài viết tiếp theo</div>
                    <h3 class="text-2xl lg:text-3xl font-bold leading-tight group-hover:text-indigo-300 transition">{{ $next->title }}</h3>
                    <p class="mt-2 text-indigo-200 text-sm">{{ $next->excerpt_truncated }}</p>
                </div>
                <div class="inline-flex items-center gap-3 px-6 py-3 rounded-xl bg-indigo-500 group-hover:bg-indigo-400 transition shrink-0">
                    <span class="font-semibold">Đọc tiếp</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </div>
            </div>
        </a>
        @endif
    </div>

@endsection
