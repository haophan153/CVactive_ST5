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
                <div class="inline-flex items-center gap-2 bg-indigo-500/20 border border-indigo-400/30 px-3 py-1 rounded-full text-xs font-medium mb-6">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    {{ $stats['total'] }} mẫu CV chuyên nghiệp
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight">
                    Chọn mẫu CV
                    <span class="bg-gradient-to-r from-amber-300 via-pink-300 to-indigo-300 bg-clip-text text-transparent">hoàn hảo nhất</span>
                </h1>
                <p class="mt-4 text-indigo-200 text-base lg:text-lg max-w-xl mx-auto">
                    Hơn {{ $stats['total'] }} mẫu CV được thiết kế bởi chuyên gia HR, giúp bạn gây ấn tượng với nhà tuyển dụng từ cái nhìn đầu tiên.
                </p>

                {{-- Search --}}
                <form method="GET" action="{{ route('templates.index') }}" class="mt-8 flex flex-col sm:flex-row gap-2 max-w-xl mx-auto">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative flex-1">
                        <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16 10.5A5.5 5.5 0 1 1 5 10.5a5.5 5.5 0 0 1 11 0z"/></svg>
                        <input type="search" name="q" value="{{ $term }}" placeholder="Tìm mẫu CV..."
                            class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-white/10 backdrop-blur border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-400 focus:border-transparent focus:bg-white/20 transition">
                    </div>
                    <button type="submit" class="px-6 py-3.5 rounded-xl bg-amber-400 text-slate-900 font-bold hover:bg-amber-300 transition shadow-lg shadow-amber-400/25">
                        Tìm kiếm
                    </button>
                </form>

                {{-- Quick stats --}}
                <div class="mt-10 flex flex-wrap items-center justify-center gap-8 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-white">{{ number_format($stats['total']) }}</div>
                        <div class="text-indigo-300 mt-0.5">Mẫu CV</div>
                    </div>
                    <div class="w-px h-10 bg-indigo-500/30"></div>
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-emerald-400">{{ number_format($stats['free']) }}</div>
                        <div class="text-indigo-300 mt-0.5">Miễn phí</div>
                    </div>
                    <div class="w-px h-10 bg-indigo-500/30"></div>
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-amber-400">{{ number_format($stats['premium']) }}</div>
                        <div class="text-indigo-300 mt-0.5">Premium</div>
                    </div>
                    <div class="w-px h-10 bg-indigo-500/30"></div>
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-pink-400">{{ number_format($stats['total_use']) }}</div>
                        <div class="text-indigo-300 mt-0.5">Lượt sử dụng</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- MAIN LAYOUT --}}
    <div class="max-w-6xl mx-auto px-4 py-10 pb-16">
        <div class="grid lg:grid-cols-4 gap-8">
            {{-- LEFT: filters + grid --}}
            <div class="lg:col-span-3 space-y-6">
                {{-- Category filter --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4">
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
                        <a href="{{ route('templates.index', array_filter(['q' => $term, 'sort' => $sort, 'filter' => $filter])) }}"
                            class="flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition {{ !request('category') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
                            Tất cả
                        </a>
                        @foreach($categories as $cat)
                            @php $colors = $cat->color_classes; @endphp
                            <a href="{{ route('templates.index', array_filter(['q' => $term, 'category' => $cat->slug, 'sort' => $sort, 'filter' => $filter])) }}"
                                class="flex-shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-1.5 {{ request('category') === $cat->slug ? "{$colors['bg']} {$colors['text']} ring-2 ring-offset-1 {$colors['text']}" : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                                {{ $cat->name }}
                                <span class="text-[11px] opacity-60">{{ $cat->active_templates_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Sort + Filter bar --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm text-gray-500">
                        @if($term)
                            Kết quả cho "{{ $term }}"
                        @elseif(request('category'))
                            {{ $categories->firstWhere('slug', request('category'))?->name }}
                        @endif
                        — <strong class="text-gray-800">{{ $templates->total() }}</strong> mẫu
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Filter toggle --}}
                        <div class="flex bg-gray-100 rounded-xl p-0.5">
                            @foreach([['all','Tất cả'],['free','Miễn phí'],['premium','Premium']] as [$val,$label])
                                <a href="{{ route('templates.index', array_filter(['q' => $term, 'category' => request('category'), 'sort' => $sort, 'filter' => $val])) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === $val ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                        {{-- Sort dropdown --}}
                        <div class="relative">
                            <select onchange="window.location.href=this.value"
                                class="appearance-none pl-3 pr-8 py-1.5 rounded-lg text-xs font-medium bg-white border border-gray-200 text-gray-700 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-200 transition cursor-pointer">
                                @foreach([['popular','Phổ biến nhất'],['newest','Mới nhất'],['oldest','Cũ nhất'],['name_asc','A → Z'],['name_desc','Z → A']] as [$val,$label])
                                    <option value="{{ route('templates.index', array_filter(['q' => $term, 'category' => request('category'), 'sort' => $val, 'filter' => $filter])) }}"
                                        {{ $sort === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <svg class="w-3.5 h-3.5 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Grid --}}
                @if($templates->isEmpty())
                <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                    <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">Không tìm thấy mẫu CV nào</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-6">Hãy thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm.</p>
                    <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Xem tất cả mẫu CV
                    </a>
                </div>
                @else
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($templates as $template)
                        @php $colors = $template->color_style; @endphp
                        <article class="group relative bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                            @if($template->is_premium)
                                <div class="absolute top-3 left-3 z-20">
                                    <span class="inline-flex items-center gap-1 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-lg">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        PREMIUM
                                    </span>
                                </div>
                            @endif

                            {{-- Thumbnail --}}
                            <a href="{{ route('templates.preview', $template) }}" target="_blank" class="block relative aspect-[3/4] overflow-hidden bg-gray-50">
                                @if($template->thumbnail)
                                    <img src="{{ $template->thumbnail }}" alt="{{ $template->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 via-gray-50 to-indigo-50 flex items-center justify-center">
                                        <div class="w-16 h-20 bg-white rounded shadow-sm border border-gray-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                @endif

                                {{-- Hover overlay --}}
                                <div class="absolute inset-0 bg-gray-900/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-3 z-10 backdrop-blur-[2px]">
                                    <a href="{{ route('templates.preview', $template) }}" target="_blank"
                                        class="inline-flex items-center gap-2 bg-white text-gray-900 font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-gray-100 transition shadow-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Xem trước
                                    </a>
                                    @auth
                                    <form action="{{ route('cv.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-indigo-500 transition shadow-lg">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Sử dụng mẫu
                                        </button>
                                    </form>
                                    @else
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-indigo-500 transition shadow-lg">
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
                                        <h3 class="font-bold text-gray-900 text-sm truncate group-hover:text-indigo-600 transition">{{ $template->name }}</h3>
                                        @if($template->category)
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $template->category->name }}</p>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        @if($template->is_premium)
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-amber-500">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                Premium
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-600">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Miễn phí
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ $template->usage_label }}
                                    </span>
                                    <a href="{{ route('templates.preview', $template) }}" target="_blank"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                                        Xem chi tiết
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">{{ $templates->links() }}</div>
                @endif
            </div>

            {{-- RIGHT: sidebar --}}
            <aside class="space-y-6">
                {{-- Why CVactive --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        Tại sao chọn CVactive?
                    </h3>
                    <ul class="space-y-3">
                        @foreach([
                            ['100% chuyên nghiệp', 'Thiết kế bởi chuyên gia HR'],
                            ['ATS-friendly', 'Tương thích hệ thống lọc CV'],
                            ['Dễ chỉnh sửa', 'Giao diện kéo thả trực quan'],
                            ['Tải đa dạng', 'PDF, DOCX, PNG, liên kết'],
                            ['Hỗ trợ 24/7', 'Đội ngũ tư vấn luôn sẵn sàng'],
                        ] as [$title, $sub])
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                <div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $title }}</span>
                                    <span class="text-xs text-gray-400 block">{{ $sub }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- CTA --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 text-white p-6">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold leading-tight">Cần CV Premium?</h3>
                        <p class="text-sm text-indigo-100 mt-2">Mở khóa tất cả template premium và tính năng nâng cao.</p>
                        <a href="{{ route('pricing') }}" class="mt-4 inline-flex items-center gap-2 bg-white text-indigo-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-amber-300 hover:text-indigo-950 transition">
                            Xem gói Premium
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Quick tips --}}
                <div class="bg-amber-50 rounded-2xl border border-amber-100 p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <h3 class="font-bold text-amber-900 text-sm">Mẹo chọn CV</h3>
                    </div>
                    <ul class="space-y-2 text-xs text-amber-800">
                        <li class="flex gap-2"><span class="font-bold text-amber-500">01.</span>Chọn template phù hợp với ngành nghề ứng tuyển</li>
                        <li class="flex gap-2"><span class="font-bold text-amber-500">02.</span>Ưu tiên thiết kế sạch, dễ đọc</li>
                        <li class="flex gap-2"><span class="font-bold text-amber-500">03.</span>Đảm bảo tương thích với hệ thống ATS</li>
                        <li class="flex gap-2"><span class="font-bold text-amber-500">04.</span>Tùy chỉnh màu sắc theo thương hiệu cá nhân</li>
                    </ul>
                </div>

                {{-- Recent activity --}}
                @php $recentTemplates = $templates->take(3); @endphp
                @if($recentTemplates->count())
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-1 h-5 bg-indigo-600 rounded-full"></span>
                        Mẫu được quan tâm
                    </h3>
                    <div class="space-y-3">
                        @foreach($recentTemplates as $t)
                            <a href="{{ route('templates.preview', $t) }}" target="_blank" class="flex items-center gap-3 group">
                                <div class="w-12 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if($t->thumbnail)
                                        <img src="{{ $t->thumbnail }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-800 group-hover:text-indigo-600 line-clamp-1 transition">{{ $t->name }}</h4>
                                    <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-2">
                                        <span>{{ $t->usage_label }} lượt xem</span>
                                        @if($t->is_premium)
                                            <span class="text-amber-500">· Premium</span>
                                        @endif
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>

@endsection
