<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Liên hệ</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Liên hệ với chúng tôi</h1>
                <p class="text-gray-500 mt-2">Chúng tôi sẽ phản hồi trong vòng 24 giờ làm việc.</p>
            </div>

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 mb-6 flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chủ đề</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('subject')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                        <textarea name="message" rows="5" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                            placeholder="Mô tả vấn đề hoặc câu hỏi của bạn...">{{ old('message') }}</textarea>
                        @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                        Gửi tin nhắn
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
