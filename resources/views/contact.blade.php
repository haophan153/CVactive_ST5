<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Liên hệ</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-6">
            <div class="mb-10">
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Liên hệ với chúng tôi</h1>
                <p class="text-slate-500 mt-3">Phản hồi trong vòng 24 giờ làm việc.</p>
            </div>

            @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl mb-6">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-100 p-8">
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Họ tên <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 transition bg-white">
                            @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 transition bg-white">
                            @error('email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Chủ đề <span class="text-rose-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 transition bg-white">
                        @error('subject')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nội dung <span class="text-rose-500">*</span></label>
                        <textarea name="message" rows="5" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 transition resize-none bg-white"
                            placeholder="Mô tả vấn đề hoặc câu hỏi của bạn...">{{ old('message') }}</textarea>
                        @error('message')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 active:scale-[0.98] transition">
                        Gửi tin nhắn
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
