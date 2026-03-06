<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Blog</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Blog CVactive</h1>
                <p class="text-gray-500 mt-2">Kiến thức & kinh nghiệm tìm việc, viết CV từ các chuyên gia</p>
            </div>

            {{-- Categories --}}
            @if($categories->count())
            <div class="flex flex-wrap gap-2 mb-8 justify-center">
                <a href="{{ route('blog.index') }}" class="px-4 py-1.5 rounded-full text-sm font-medium {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">Tất cả</a>
                @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat->slug]) }}"
                    class="px-4 py-1.5 rounded-full text-sm font-medium {{ request('category') === $cat->slug ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition">
                    {{ $cat->name }} ({{ $cat->posts_count }})
                </a>
                @endforeach
            </div>
            @endif

            @if($posts->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <p>Chưa có bài viết nào.</p>
            </div>
            @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                <article class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition group">
                    @if($post->featured_image)
                    <div class="aspect-video overflow-hidden">
                        <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    @else
                    <div class="aspect-video bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <svg class="w-10 h-10 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    @endif
                    <div class="p-5">
                        @if($post->category)
                        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">{{ $post->category->name }}</span>
                        @endif
                        <h2 class="text-base font-bold text-gray-900 mt-3 mb-2 line-clamp-2">
                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-indigo-600 transition">{{ $post->title }}</a>
                        </h2>
                        @if($post->excerpt)
                        <p class="text-sm text-gray-500 line-clamp-2">{{ $post->excerpt }}</p>
                        @endif
                        <div class="flex items-center justify-between mt-4 text-xs text-gray-400">
                            <span>{{ $post->author->name }}</span>
                            <span>{{ $post->published_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
