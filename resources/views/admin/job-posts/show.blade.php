@extends('layouts.admin')
@section('title', $jobPost->title)
@section('page-title', 'Chi tiết tin tuyển dụng')

@section('breadcrumb')
<a href="{{ route('admin.job-posts.index') }}" class="text-gray-500 hover:text-gray-700">Tin tuyển dụng</a>
<svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 font-semibold truncate max-w-xs">{{ $jobPost->title }}</span>
@endsection

@section('content')

<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $jobPost->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $jobPost->company_name }} · {{ $jobPost->location }}</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full
                    {{ $jobPost->status === 'published' ? 'bg-green-100 text-green-700' : ($jobPost->status === 'closed' ? 'bg-gray-100 text-gray-600' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ ['published' => 'Đang tuyển', 'closed' => 'Đã đóng', 'draft' => 'Nháp'][$jobPost->status] ?? $jobPost->status }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5 pt-5 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-500">Đơn ứng tuyển</p>
                    <p class="font-bold text-gray-900">{{ $jobPost->applications->count() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Loại</p>
                    <p class="font-bold text-gray-900">{{ $jobPost->type_info['label'] ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Cấp</p>
                    <p class="font-bold text-gray-900">{{ $jobPost->experience_info['label'] ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Lương</p>
                    <p class="font-bold text-gray-900">{{ $jobPost->salary_label }}</p>
                </div>
            </div>

            @if($jobPost->description)
            <div class="mt-6 pt-5 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 mb-2">Mô tả</h3>
                <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line">{{ $jobPost->description }}</div>
            </div>
            @endif
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <p class="text-sm font-medium text-amber-800">
                <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Thông tin nhạy cảm
            </p>
            <p class="text-xs text-amber-700 mt-1">
                Vì lý do bảo mật, danh sách ứng viên chỉ HR sở hữu tin mới xem được. Admin không truy cập trực tiếp CV ứng viên tại đây.
            </p>
        </div>
    </div>

    <div class="space-y-4">

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-3">HR sở hữu</h3>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold">
                    {{ strtoupper(substr($jobPost->user?->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $jobPost->user?->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $jobPost->user?->email }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Đổi trạng thái</h3>
            <form action="{{ route('admin.job-posts.toggle', $jobPost) }}" method="POST" class="space-y-2">
                @csrf
                <select name="status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2">
                    <option value="published" {{ $jobPost->status === 'published' ? 'selected' : '' }}>Đang tuyển</option>
                    <option value="closed"    {{ $jobPost->status === 'closed'    ? 'selected' : '' }}>Đã đóng</option>
                    <option value="draft"     {{ $jobPost->status === 'draft'     ? 'selected' : '' }}>Nháp</option>
                </select>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">Cập nhật</button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Vòng đời</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Tạo lúc</dt><dd>{{ $jobPost->created_at->format('d/m/Y H:i') }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Đăng lúc</dt><dd>{{ optional($jobPost->published_at)->format('d/m/Y H:i') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Hết hạn</dt><dd>{{ optional($jobPost->expires_at)->format('d/m/Y') ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Lượt xem</dt><dd>{{ number_format($jobPost->views_count ?? 0) }}</dd></div>
            </dl>
        </div>
    </div>
</div>

{{-- Top 10 chart --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mt-5">
    <h3 class="font-semibold text-gray-900 mb-4">Top 10 tin có nhiều đơn ứng tuyển nhất</h3>
    @php $max = $topJobsByApps->max('applications_count') ?: 1; @endphp
    <div class="space-y-2">
        @foreach($topJobsByApps as $j)
        @php $pct = $j->applications_count > 0 ? max(3, round($j->applications_count / $max * 100)) : 3; @endphp
        <div>
            <div class="flex justify-between text-xs mb-1">
                <span class="text-gray-700 truncate flex-1 mr-3">{{ $j->title }}</span>
                <span class="font-semibold text-gray-900">{{ $j->applications_count }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500" style="width: {{ $pct }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
