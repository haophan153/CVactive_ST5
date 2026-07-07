@extends('layouts.app')

@section('title', 'Đăng tin tuyển dụng')

@php
    $old = fn ($k, $d = null) => old($k, $d);
@endphp

@section('content')
<div class="py-6 sm:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('hr.job-posts.index') }}" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Quay lại danh sách
            </a>
        </div>

        {{-- Hero header --}}
        <div class="bg-[#0F172A] rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden mb-6">
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative">
                <h1 class="text-2xl sm:text-3xl font-extrabold">📝 Đăng tin tuyển dụng mới</h1>
                <p class="text-indigo-100 mt-2 text-sm max-w-md">Hoàn thành 4 bước để có một tin tuyển dụng thu hút nhà ứng tuyển.</p>
            </div>
        </div>

        {{-- Stepper --}}
        <nav class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 p-4 sm:p-6" aria-label="Các bước đăng tin">
            <ol id="stepper" class="flex items-center justify-between gap-2">
                @php
                    $steps = [
                        ['n' => 1, 'icon' => '📋', 'label' => 'Thông tin', 'sub' => 'Tiêu đề · Loại hình'],
                        ['n' => 2, 'icon' => '✍️', 'label' => 'Mô tả',    'sub' => 'Mô tả công việc'],
                        ['n' => 3, 'icon' => '💰', 'label' => 'Lương',    'sub' => 'Mức lương'],
                        ['n' => 4, 'icon' => '🏢', 'label' => 'Công ty',  'sub' => 'Liên hệ · Logo'],
                    ];
                @endphp
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
        <form method="POST" action="{{ route('hr.job-posts.store') }}" enctype="multipart/form-data" id="jobForm" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-4">
            @csrf

            {{-- Step 1: Basics --}}
            <section data-step="1" class="step-panel space-y-5">
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">
                        Tiêu đề tin tuyển dụng <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ $old('title') }}"
                        class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Ví dụ: Senior Laravel Developer" required>
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Mẹo: tiêu đề cụ thể có từ khóa sẽ được tìm kiếm nhiều hơn <span class="font-semibold">3 lần</span>.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="job_type" class="block text-sm font-semibold text-gray-700 mb-1">Loại hình</label>
                        <select name="job_type" id="job_type" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Chọn loại hình —</option>
                            @foreach(\App\Models\JobPost::JOB_TYPES as $val => $info)
                                <option value="{{ $val }}" {{ $old('job_type') == $val ? 'selected' : '' }}>{{ $info['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-1">Ngành nghề</label>
                        <select name="category" id="category" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Chọn ngành —</option>
                            @foreach(\App\Models\JobPost::CATEGORIES as $val => $info)
                                <option value="{{ $val }}" {{ $old('category') == $val ? 'selected' : '' }}>{{ $info['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="experience_level" class="block text-sm font-semibold text-gray-700 mb-1">Cấp bậc</label>
                        <select name="experience_level" id="experience_level" class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Chọn cấp bậc —</option>
                            @foreach(\App\Models\JobPost::EXPERIENCE_LEVELS as $val => $info)
                                <option value="{{ $val }}" {{ $old('experience_level') == $val ? 'selected' : '' }}>{{ $info['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">Địa điểm</label>
                        <input type="text" name="location" id="location" value="{{ $old('location') }}"
                            class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ví dụ: Hà Nội, Hồ Chí Minh...">
                    </div>
                    <div class="space-y-2 pt-6">
                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-emerald-50/50 transition">
                            <input type="checkbox" name="is_remote" value="1" {{ $old('is_remote') ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="text-sm">
                                <span class="font-semibold text-gray-900">🌍 Cho phép Remote / Từ xa</span>
                                <span class="block text-xs text-gray-500">Tăng gấp đôi lượt ứng tuyển tiềm năng</span>
                            </span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:bg-amber-50/50 transition">
                            <input type="checkbox" name="is_hot" value="1" {{ $old('is_hot') ? 'checked' : '' }}
                                class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                            <span class="text-sm">
                                <span class="font-semibold text-gray-900">🔥 Đánh dấu là tin <span class="text-amber-600">HOT</span></span>
                                <span class="block text-xs text-gray-500">Làm nổi bật trong kết quả tìm kiếm (7 ngày)</span>
                            </span>
                        </label>
                    </div>
                </div>
            </section>

            {{-- Step 2: Description --}}
            <section data-step="2" class="step-panel hidden space-y-3">
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">
                        Mô tả công việc <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="12"
                        class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
                        placeholder="Mô tả chi tiết về công việc, trách nhiệm, yêu cầu, quyền lợi..." required>{{ $old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

                    <div class="flex items-center justify-between text-xs text-gray-400 mt-2">
                        <span>Gợi ý: dùng bullet points cho rõ ràng. Tối thiểu 200 ký tự để đạt chuẩn SEO.</span>
                        <span><span id="descCount">0</span> ký tự</span>
                    </div>
                </div>
            </section>

            {{-- Step 3: Salary --}}
            <section data-step="3" class="step-panel hidden space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mức lương (VNĐ)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <input type="number" name="salary_min" value="{{ $old('salary_min') }}" min="0"
                            class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tối thiểu">
                        <input type="number" name="salary_max" value="{{ $old('salary_max') }}" min="0"
                            class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tối đa">
                        <select name="salary_currency" class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="VND" {{ $old('salary_currency', 'VND') === 'VND' ? 'selected' : '' }}>VND</option>
                            <option value="USD" {{ $old('salary_currency') === 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Để trống nếu thương lượng. Tin có mức lương rõ ràng tăng <span class="font-semibold text-emerald-600">+35%</span> lượt ứng tuyển.</p>
                    @error('salary_max') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-1">Ngày hết hạn</label>
                    <input type="date" name="expires_at" id="expires_at" value="{{ $old('expires_at') }}"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        class="rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Mặc định: <strong>30 ngày</strong> sau khi đăng. Để trống nếu không giới hạn.</p>
                </div>
            </section>

            {{-- Step 4: Company + Contact --}}
            <section data-step="4" class="step-panel hidden space-y-6">
                <fieldset class="border-t border-gray-100 pt-6">
                    <legend class="text-base font-bold text-gray-900 mb-4 px-2">🏢 Thông tin công ty</legend>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-1">Tên công ty</label>
                                <input type="text" name="company_name" id="company_name" value="{{ $old('company_name', auth()->user()->company_name ?? '') }}"
                                    class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Công ty TNHH ABC">
                            </div>
                            <div>
                                <label for="company_logo" class="block text-sm font-semibold text-gray-700 mb-1">Logo công ty</label>
                                <input type="file" name="company_logo" id="company_logo" accept="image/*"
                                    class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-[11px] text-gray-400 mt-1">PNG, JPG, WEBP · Tối đa 2MB</p>
                            </div>
                        </div>
                        <div>
                            <label for="company_description" class="block text-sm font-semibold text-gray-700 mb-1">Giới thiệu công ty</label>
                            <textarea name="company_description" id="company_description" rows="3"
                                class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Vài dòng về văn hoá, sứ mệnh...">{{ $old('company_description') }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border-t border-gray-100 pt-6">
                    <legend class="text-base font-bold text-gray-900 mb-4 px-2">📞 Thông tin liên hệ</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_email" class="block text-sm font-semibold text-gray-700 mb-1">Email liên hệ</label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ $old('contact_email', auth()->user()->email) }}"
                                class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-semibold text-gray-700 mb-1">Số điện thoại</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ $old('contact_phone') }}"
                                class="w-full rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="0901 234 567">
                        </div>
                    </div>
                </fieldset>

                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm text-emerald-800">
                        <strong>Sẵn sàng đăng!</strong> Sau khi lưu, tin sẽ ở trạng thái <em>Nháp</em>. Bạn có thể xem và đăng từ trang chi tiết.
                    </div>
                </div>
            </section>

            {{-- Nav buttons --}}
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <button type="button" id="prevStep" class="hidden px-4 py-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-semibold">
                    ← Quay lại
                </button>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('hr.job-posts.index') }}" class="px-4 py-2.5 rounded-lg text-gray-500 hover:bg-gray-100 text-sm font-medium">
                        Hủy
                    </a>
                    <button type="button" id="nextStep" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm shadow-sm hover:shadow transition">
                        Tiếp tục →
                    </button>
                    <button type="submit" id="submitStep" class="hidden px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm shadow-sm hover:shadow transition">
                        💾 Lưu nháp
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

    // Description counter
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
