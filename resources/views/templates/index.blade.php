{{-- HERO --}}
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
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    }
</style>
@endpush

@section('content')

    <section class="relative bg-[#0F172A] text-white overflow-hidden pt-16 pb-20">
        <div class="hero-texture absolute inset-0"></div>
        <div class="relative z-10 max-w-6xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 px-3 py-1 rounded-full text-xs font-medium mb-6">
                {{ $stats['total'] }} mẫu CV chuyên nghiệp
            </div>
            <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight mb-4">
                Chọn mẫu CV <span class="text-indigo-400">hoàn hảo nhất</span>
            </h1>
            <p class="text-indigo-200 text-base lg:text-lg max-w-xl mx-auto mb-8">
                Hơn {{ $stats['total'] }} mẫu được thiết kế bởi chuyên gia HR, giúp bạn gây ấn tượng với nhà tuyển dụng.
            </p>

            {{-- Search (solid white inputs) --}}
            <form method="GET" action="{{ route('templates.index') }}" class="mt-2 flex flex-col sm:flex-row gap-2 max-w-xl mx-auto">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative flex-1">
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                    <input type="search" name="q" value="{{ $term }}" placeholder="Tìm mẫu CV..."
                        class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white text-slate-900 placeholder-slate-400 border-0 focus:ring-2 focus:ring-indigo-300 shadow-xl text-sm">
                </div>
                <button type="submit" class="px-6 py-3.5 rounded-xl bg-indigo-500 text-white font-semibold hover:bg-indigo-400 active:scale-[0.98] transition shadow-xl shadow-indigo-900/40 flex items-center justify-center gap-2 text-sm shrink-0">
                    Tìm kiếm
                </button>
            </form>

            {{-- Quick stats (navy strip) --}}
            <div class="mt-10 flex flex-wrap items-center justify-center gap-8 text-sm">
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-white">{{ number_format($stats['total']) }}</div>
                    <div class="text-indigo-300 mt-1">Mẫu CV</div>
                </div>
                <div class="w-px h-10 bg-indigo-800/50"></div>
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-emerald-400">{{ number_format($stats['free']) }}</div>
                    <div class="text-indigo-300 mt-1">Miễn phí</div>
                </div>
                <div class="w-px h-10 bg-indigo-800/50"></div>
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-indigo-300">{{ number_format($stats['premium']) }}</div>
                    <div class="text-indigo-300 mt-1">Premium</div>
                </div>
                <div class="w-px h-10 bg-indigo-800/50"></div>
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-white">{{ number_format($stats['total_use']) }}</div>
                    <div class="text-indigo-300 mt-1">Lượt sử dụng</div>
                </div>
            </div>
        </div>
    </section>

    {{-- MAIN LAYOUT --}}
    <div class="max-w-6xl mx-auto px-6 py-10 pb-16">
        <div class="grid lg:grid-cols-[280px_1fr] gap-8">

            {{-- LEFT: sidebar --}}
            <aside class="space-y-4">
                {{-- Category filter pills --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('templates.index', array_filter(['q' => $term, 'sort' => $sort, 'filter' => $filter])) }}"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition {{ !request('category') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                            Tất cả
                        </a>
                        @foreach($categories as $cat)
                            @php $colors = $cat->color_classes; @endphp
                            <a href="{{ route('templates.index', array_filter(['q' => $term, 'category' => $cat->slug, 'sort' => $sort, 'filter' => $filter])) }}"
                                class="px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-1.5 {{ request('category') === $cat->slug ? "{$colors['bg']} {$colors['text']} ring-2 ring-offset-1 {$colors['text']}" : 'bg-slate-50 text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                                {{ $cat->name }}
                                <span class="text-[11px] opacity-60">{{ $cat->active_templates_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- CTA (navy) --}}
                <div class="bg-[#0F172A] rounded-2xl p-6 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-50" style="background-image:linear-gradient(rgba(255,255,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.04) 1px,transparent 1px);background-size:60px 60px"></div>
                    <div class="relative">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/30 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold leading-tight mb-2">Cần CV Premium?</h3>
                        <p class="text-sm text-indigo-200 leading-relaxed">Mở khóa tất cả template premium và tính năng nâng cao.</p>
                        <a href="{{ route('pricing') }}" class="mt-5 inline-flex items-center gap-2 bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-400 active:scale-[0.98] transition shadow-lg">
                            Xem gói Premium
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>
            </aside>

            {{-- RIGHT: grid --}}
            <div class="space-y-5">

                {{-- Sort + Filter bar --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm text-slate-500">
                        @if($term)
                            Kết quả cho "{{ $term }}"
                        @elseif(request('category'))
                            {{ $categories->firstWhere('slug', request('category'))?->name }}
                        @endif
                        <span class="font-semibold text-slate-800 ml-1">{{ optional($templates)->total() }}</span> mẫu
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex bg-slate-100 rounded-xl p-0.5">
                            @foreach([['all','Tất cả'],['free','Miễn phí'],['premium','Premium']] as [$val,$label])
                                <a href="{{ route('templates.index', array_filter(['q' => $term, 'category' => request('category'), 'sort' => $sort, 'filter' => $val])) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === $val ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                        <div class="relative">
                            <select onchange="window.location.href=this.value"
                                class="appearance-none pl-3 pr-8 py-1.5 rounded-lg text-xs font-medium bg-white border border-slate-200 text-slate-700 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-200 transition cursor-pointer">
                                @foreach([['popular','Phổ biến nhất'],['newest','Mới nhất'],['oldest','Cũ nhất'],['name_asc','A → Z'],['name_desc','Z → A']] as [$val,$label])
                                    <option value="{{ route('templates.index', array_filter(['q' => $term, 'category' => request('category'), 'sort' => $val, 'filter' => $filter])) }}"
                                        {{ $sort === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" style="margin-left:-20px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Grid --}}
                @if(optional($templates)->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-slate-200">
                    <div class="w-16 h-16 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002 2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg">Không tìm thấy mẫu CV nào</h3>
                    <p class="text-sm text-slate-500 mt-2 mb-6">Hãy thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm.</p>
                    <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 active:scale-[0.98] transition">Xem tất cả mẫu CV</a>
                </div>
                @else
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($templates as $template)
                        @php $colors = $template->color_style; @endphp
                        <article class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" style="transition-timing-function:cubic-bezier(0.16,1,0.3,1)">
                            @if($template->is_premium)
                                <div class="absolute top-3 left-3 z-20">
                                    <span class="inline-flex items-center gap-1 bg-amber-400 text-indigo-950 text-[10px] font-bold px-2.5 py-1 rounded-full shadow-lg">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        PREMIUM
                                    </span>
                                </div>
                            @endif

                            <a href="{{ route('templates.preview', $template) }}" target="_blank" class="block relative aspect-[3/4] overflow-hidden bg-slate-50">
                                @if($template->thumbnail)
                                    <img src="{{ $template->thumbnail_url }}" alt="{{ $template->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                        <div class="w-16 h-20 bg-white rounded shadow-sm border border-slate-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                @endif

                                {{-- Hover overlay --}}
                                <div class="absolute inset-0 bg-slate-900/80 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center gap-3 z-10">
                                    <a href="{{ route('templates.preview', $template) }}" target="_blank"
                                        class="inline-flex items-center gap-2 bg-white text-slate-900 font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-slate-100 active:scale-[0.98] transition shadow-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Xem trước
                                    </a>
                                    @auth
                                    <form action="{{ route('cv.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-indigo-500 active:scale-[0.98] transition shadow-lg">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Sử dụng mẫu
                                        </button>
                                    </form>
                                    @else
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-indigo-500 active:scale-[0.98] transition shadow-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Sử dụng mẫu
                                    </a>
                                    @endauth
                                </div>
                            </a>

                            {{-- Card body --}}
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-slate-900 text-sm truncate group-hover:text-indigo-600 transition">{{ $template->name }}</h3>
                                        @if($template->category)
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $template->category->name }}</p>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if($template->is_premium)
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-amber-500">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                Premium
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-600">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Miễn phí
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50">
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ $template->usage_label }}
                                    </span>
                                    <a href="{{ route('templates.preview', $template) }}" target="_blank"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1 active:scale-[0.95]">
                                        Chi tiết
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                    @php /** @var \Illuminate\Pagination\LengthAwarePaginator $templates */ @endphp
                    @if($templates->hasPages())
                        <div class="mt-8 col-span-full">{{ $templates->links() }}</div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection
