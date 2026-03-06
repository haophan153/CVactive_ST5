<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Câu hỏi thường gặp</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Câu hỏi thường gặp</h1>
                <p class="text-gray-500 mt-2">Không tìm thấy câu trả lời? <a href="{{ route('contact') }}" class="text-indigo-600 hover:underline">Liên hệ chúng tôi</a></p>
            </div>

            <div class="space-y-4" x-data="{open: null}">
                @foreach($faqs as $faq)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <button @click="open === {{ $faq->id }} ? open = null : open = {{ $faq->id }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left">
                        <span class="font-semibold text-gray-900">{{ $faq->question }}</span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open === {{ $faq->id }} ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === {{ $faq->id }}" x-collapse class="px-6 pb-4">
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $faq->answer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
