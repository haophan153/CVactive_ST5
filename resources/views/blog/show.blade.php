<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $post->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="grid lg:grid-cols-3 gap-8">
                <article class="lg:col-span-2">
                    @if($post->featured_image)
                    <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                        class="w-full rounded-xl mb-6 aspect-video object-cover">
                    @endif

                    @if($post->category)
                    <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">{{ $post->category->name }}</span>
                    @endif

                    <h1 class="text-3xl font-extrabold text-gray-900 mt-4 mb-4 leading-tight">{{ $post->title }}</h1>

                    <div class="flex items-center space-x-3 text-sm text-gray-400 mb-8 pb-6 border-b border-gray-100">
                        <span>{{ $post->author->name }}</span>
                        <span>·</span>
                        <span>{{ $post->published_at?->format('d/m/Y') }}</span>
                        <span>·</span>
                        <span>{{ number_format($post->views_count) }} lượt xem</span>
                    </div>

                    <div class="prose prose-gray max-w-none text-sm leading-7">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                </article>

                <aside class="space-y-6">
                    @if($related->count())
                    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-4">Bài viết liên quan</h3>
                        <div class="space-y-4">
                            @foreach($related as $rel)
                            <div class="flex space-x-3">
                                <div class="w-16 h-12 bg-indigo-50 rounded flex-shrink-0 overflow-hidden">
                                    @if($rel->featured_image)
                                    <img src="{{ asset('storage/'.$rel->featured_image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('blog.show', $rel->slug) }}" class="text-sm font-medium text-gray-800 hover:text-indigo-600 line-clamp-2">{{ $rel->title }}</a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $rel->published_at?->diffForHumans() }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="bg-indigo-50 rounded-xl p-5">
                        <h3 class="font-bold text-indigo-900 mb-2">Tạo CV ngay hôm nay</h3>
                        <p class="text-sm text-indigo-700 mb-4">Áp dụng ngay những gì bạn đọc được vào CV chuyên nghiệp của mình.</p>
                        <a href="{{ route('cv.create') }}" class="block text-center py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">Tạo CV miễn phí</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
