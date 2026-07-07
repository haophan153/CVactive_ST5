@extends('layouts.app')

@section('title', 'Chỉnh sửa: ' . $jobPost->title)

@php
    $old = fn ($k, $d = null) => old($k, $d);
@endphp

@section('content')
<div class="py-6 sm:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back link + status pill --}}
        <div class="mb-6 flex items-center justify-between gap-3">
            <a href="{{ route('hr.job-posts.show', $jobPost) }}" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Quay lại chi tiết
            </a>
            @php
                $statusMap = [
                    'draft'     => 'bg-gray-100 text-gray-700',
                    'published' => 'bg-emerald-100 text-emerald-700',
                    'closed'    => 'bg-rose-100 text-rose-700',
                ];
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $statusMap[$jobPost->status] ?? 'bg-gray-100 text-gray-700' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $jobPost->status === 'published' ? 'bg-emerald-500' : ($jobPost->status === 'closed' ? 'bg-rose-500' : 'bg-gray-400') }}"></span>
                {{ $jobPost->status === 'published' ? 'Đang đăng' : ($jobPost->status === 'closed' ? 'Đã đóng' : 'Nháp') }}
            </span>
        </div>

        {{-- Hero header --}}
        <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden mb-6">
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative">
                <h1 class="text-2xl sm:text-3xl font-extrabold">✏️ Chỉnh sửa tin tuyển dụng</h1>
                <p class="text-amber-50 mt-2 text-sm line-clamp-1">{{ $jobPost->title }}</p>
            </div>
        </div>

        {{-- Stepper --}}
        <nav class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 p-4 sm:p-6" aria-label="Các bước">
            <ol id="stepper" class="flex items-center justify-between gap-2">
                @php $steps = [
                    ['n' => 1, 'icon' => '📋', 'label' => 'Thông tin', 'sub' => 'Tiêu đề · Loại hình'],
                    ['n' => 2, 'icon' => '✍️', 'label' => 'Mô tả',    'sub' => 'Mô tả công việc'],
                    ['n' => 3, 'icon' => '💰', 'label' => 'Lương',    'sub' => 'Mức lương'],
                    ['n' => 4, 'icon' => '🏢', 'label' => 'Công ty',  'sub' => 'Liên hệ · Logo'],
                ]; @endphp
                @foreach($steps as $s)
                    <li class="flex-1 flex items-center gap-3" data-step="{{ $s['n'] }}">
                        <div class="step-dot w-9 h-9 rounded-full flex items-center justify-center font-bold flex-shrink-0
                                    {{ $s['n'] === 1 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                            {{ $s['n'] }}
                        </div>
                        <div class="min-w-0 hidden sm:block">
                            <div class="text-sm font-semibold step-title {{ $s['n'] === 1 ? 'text-gray-900' : 'text-gray-500' }}">{{ $s['icon'] }} {{ $s['label'] }}</div>
                            <div class="text-[11px] text-gray-400 truncate">{{ $s['sub'] }}</div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </nav>

        {{-- Form --}}
        <form method="POST" action="{{ route('hr.job-posts.update', $jobPost) }}" enctype="multipart/form-data" id="jobForm" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-4">
            @csrf @method('PUT')

            {{-- Step 1 --}}
            <section data-step="1" class="step-panel space-y-5">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">
                        Tiêu đề tin tuyển dụng <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ $old('title', $jobPost->title) }}"
                        class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="job_type" class="block text-sm font-semibold text-gray-700 mb-1">Loại hình</label>
                        <select name="job_type" id="job_type" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Chọn loại hình —</option>
                            @foreach(\App\Models\JobPost::JOB_TYPES as $val => $info)
                                <option value="{{ $val }}" {{ $old('job_type', $jobPost->job_type) == $val ? 'selected' : '' }}>{{ $info['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-1">Ngành nghề</label>
                        <select name="category" id="category" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Chọn ngành —</option>
                            @foreach(\App\Models\JobPost::CATEGORIES as $val => $info)
                                <option value="{{ $val }}" {{ $old('category', $jobPost->category) == $val ? 'selected' : '' }}>{{ $info['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="experience_level" class="block text-sm font-semibold text-gray-700 mb-1">Cấp bậc</label>
                        <select name="experience_level" id="experience_level" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Chọn cấp bậc —</option>
                            @foreach(\App\Models\JobPost::EXPERIENCE_LEVELS as $val => $info)
                                <option value="{{ $val }}" {{ $old('experience_level', $jobPost->experience_level) == $val ? 'selected' : '' }}>{{ $info['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">Địa điểm</label>
                        <input type="text" name="location" id="location" value="{{ $old('location', $jobPost->location) }}"
                            class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="space-y-2 pt-6">
                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-emerald-50/50 transition">
                            <input type="checkbox" name="is_remote" value="1" {{ $old('is_remote', $jobPost->is_remote) ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="text-sm">
                                <span class="font-semibold text-gray-900">🌍 Cho phép Remote</span>
                                <span class="block text-xs text-gray-500">Tăng lượt ứng tuyển</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-amber-50/50 transition">
                            <input type="checkbox" name="is_hot" value="1" {{ $old('is_hot', $jobPost->is_hot) ? 'checked' : '' }}
                                class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                            <span class="text-sm">
                                <span class="font-semibold text-gray-900">🔥 Đánh dấu HOT</span>
                                <span class="block text-xs text-gray-500">Làm nổi bật trong tìm kiếm</span>
                            </span>
                        </label>
                    </div>
                </div>
            </section>

            {{-- Step 2 --}}
            <section data-step="2" class="step-panel hidden space-y-3">
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">
                        Mô tả công việc <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="12"
                        class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm" required>{{ $old('description', $jobPost->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    <div class="flex items-center justify-between text-xs text-gray-400 mt-2">
                        <span>Tối thiểu 200 ký tự cho chuẩn SEO.</span>
                        <span><span id="descCount">0</span> ký tự</span>
                    </div>
                </div>
            </section>

            {{-- Step 3 --}}
            <section data-step="3" class="step-panel hidden space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mức lương (VNĐ)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <input type="number" name="salary_min" value="{{ $old('salary_min', $jobPost->salary_min) }}" min="0"
                            class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tối thiểu">
                        <input type="number" name="salary_max" value="{{ $old('salary_max', $jobPost->salary_max) }}" min="0"
                            class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tối đa">
                        <select name="salary_currency" class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="VND" {{ $old('salary_currency', $jobPost->salary_currency ?? 'VND') === 'VND' ? 'selected' : '' }}>VND</option>
                            <option value="USD" {{ $old('salary_currency', $jobPost->salary_currency) === 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>
                    @error('salary_max') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-1">Ngày hết hạn</label>
                    <input type="date" name="expires_at" id="expires_at" value="{{ $old('expires_at', $jobPost->expires_at?->format('Y-m-d')) }}"
                        class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </section>

            {{-- Step 4 --}}
            <section data-step="4" class="step-panel hidden space-y-6">
                <fieldset class="border-t border-gray-100 pt-6">
                    <legend class="text-base font-bold text-gray-900 mb-4 px-2">🏢 Thông tin công ty</legend>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-1">Tên công ty</label>
                                <input type="text" name="company_name" id="company_name" value="{{ $old('company_name', $jobPost->company_name) }}"
                                    class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="company_logo" class="block text-sm font-semibold text-gray-700 mb-1">Logo công ty</label>
                                @if($jobPost->company_logo_url)
                                    <div class="flex items-center gap-3 mb-2">
                                        <img src="{{ $jobPost->company_logo_url }}" alt="Logo" class="w-12 h-12 object-contain rounded-lg border border-gray-100 p-1">
                                        <span class="text-xs text-gray-500">Logo hiện tại</span>
                                    </div>
                                @endif
                                <input type="file" name="company_logo" id="company_logo" accept="image/*"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-[11px] text-gray-400 mt-1">Để trống nếu muốn giữ nguyên logo cũ · Tối đa 2MB</p>
                            </div>
                        </div>
                        <div>
                            <label for="company_description" class="block text-sm font-semibold text-gray-700 mb-1">Giới thiệu công ty</label>
                            <textarea name="company_description" id="company_description" rows="3"
                                class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">{{ $old('company_description', $jobPost->company_description) }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border-t border-gray-100 pt-6">
                    <legend class="text-base font-bold text-gray-900 mb-4 px-2">📞 Thông tin liên hệ</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_email" class="block text-sm font-semibold text-gray-700 mb-1">Email liên hệ</label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ $old('contact_email', $jobPost->contact_email) }}"
                                class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-semibold text-gray-700 mb-1">Số điện thoại</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ $old('contact_phone', $jobPost->contact_phone) }}"
                                class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </fieldset>
            </section>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <button type="button" id="prevStep" class="hidden px-4 py-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold">
                    ← Quay lại
                </button>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('hr.job-posts.show', $jobPost) }}" class="px-4 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 text-sm font-medium">
                        Hủy
                    </a>
                    <button type="button" id="nextStep" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm shadow-sm hover:shadow transition">
                        Tiếp tục →
                    </button>
                    <button type="submit" id="submitStep" class="hidden px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm shadow-sm hover:shadow transition">
                        💾 Cập nhật
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const total = 4;
    let current = 1;

    const panels = document.querySelectorAll('.step-panel');
    const dots   = document.querySelectorAll('[data-step] .step-dot');
    const titles = document.querySelectorAll('[data-step] .step-title');
    const prev   = document.getElementById('prevStep');
    const next   = document.getElementById('nextStep');
    const submit = document.getElementById('submitStep');

    function render() {
        panels.forEach((p) => {
            p.classList.toggle('hidden', String(p.dataset.step) !== String(current));
        });
        dots.forEach((d) => {
            const n = Number(d.parentElement.dataset.step);
            d.classList.remove('bg-indigo-600', 'text-white', 'bg-emerald-500', 'bg-gray-100', 'text-gray-400');
            if (n < current)      { d.classList.add('bg-emerald-500', 'text-white'); }
            else if (n === current) { d.classList.add('bg-indigo-600', 'text-white'); }
            else                  { d.classList.add('bg-gray-100', 'text-gray-400'); }
        });
        titles.forEach((t) => {
            const n = Number(t.parentElement.parentElement.dataset.step);
            t.classList.toggle('text-gray-900', n <= current);
            t.classList.toggle('text-gray-500', n > current);
        });
        prev.classList.toggle('hidden', current === 1);
        next.classList.toggle('hidden', current === total);
        submit.classList.toggle('hidden', current !== total);
    }

    function validateStep() {
        const panel = document.querySelector(`.step-panel[data-step="${current}"]`);
        if (!panel) return true;
        const inputs = panel.querySelectorAll('input[required], textarea[required], select[required]');
        for (const el of inputs) {
            if (!el.value) { el.focus(); el.classList.add('ring-2', 'ring-rose-300'); return false; }
        }
        return true;
    }

    next.addEventListener('click', () => {
        if (!validateStep()) return;
        if (current < total) { current++; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    });
    prev.addEventListener('click', () => {
        if (current > 1) { current--; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    });

    const desc = document.getElementById('description');
    const cnt  = document.getElementById('descCount');
    if (desc && cnt) {
        const upd = () => cnt.textContent = (desc.value || '').length;
        desc.addEventListener('input', upd);
        upd();
    }

    render();
})();
</script>
@endpush
@endsection
