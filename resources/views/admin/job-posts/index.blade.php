@extends('layouts.admin')
@section('title', 'Quản lý Tin tuyển dụng')
@section('page-title', 'Tin tuyển dụng')

@section('breadcrumb')
<span class="text-gray-900 font-semibold">Tin tuyển dụng</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-5">
    <p class="text-sm text-gray-500">{{ $jobs->total() }} tin tuyển dụng</p>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Tổng</p>
        <p class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-green-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Đang tuyển</p>
        <p class="text-2xl font-extrabold text-green-600">{{ number_format($stats['open']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <p class="text-xs text-gray-500">Đã đóng</p>
        <p class="text-2xl font-extrabold text-gray-500">{{ number_format($stats['closed']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-5">
        <p class="text-xs text-gray-500">Tổng đơn ứng tuyển</p>
        <p class="text-2xl font-extrabold text-blue-600">{{ number_format($stats['applications']) }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề / công ty..."
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái</label>
            <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đang tuyển</option>
                <option value="closed"    {{ request('status') === 'closed'    ? 'selected' : '' }}>Đã đóng</option>
                <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Nháp</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Danh mục</label>
            <select name="category" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                @foreach(\App\Models\JobPost::CATEGORIES as $key => $info)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Cấp</label>
            <select name="experience" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                @foreach(\App\Models\JobPost::EXPERIENCE_LEVELS as $key => $info)
                <option value="{{ $key }}" {{ request('experience') === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">HR sở hữu</label>
            <select name="owner" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                <option value="none" {{ request('owner') === 'none' ? 'selected' : '' }}>Không có</option>
                @foreach($owners as $o)
                <option value="{{ $o->id }}" {{ request('owner') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Từ ngày</label>
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Đến ngày</label>
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Lọc</button>
        @if(request()->hasAny(['search','status','category','experience','owner','from','to']))
        <a href="{{ route('admin.job-posts.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tin</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">HR sở hữu</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Danh mục</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Đơn</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($jobs as $job)
            @php
                $statusColor = match($job->status) {
                    'published' => 'bg-green-100 text-green-700',
                    'closed'    => 'bg-gray-100 text-gray-600',
                    'draft'     => 'bg-yellow-100 text-yellow-700',
                    default     => 'bg-gray-100 text-gray-600',
                };
                $statusLabel = match($job->status) {
                    'published' => 'Đang tuyển', 'closed' => 'Đã đóng', 'draft' => 'Nháp', default => $job->status,
                };
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                            {{ $job->company_initials ?? strtoupper(substr($job->title, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate max-w-xs">{{ $job->title }}</p>
                            <p class="text-xs text-gray-400">{{ $job->company_name ?? '—' }} · {{ $job->location ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-700 text-sm">{{ $job->user?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-xs">
                    @if($job->category)
                    <span class="px-2 py-0.5 rounded bg-{{ $job->category_info['color'] ?? 'gray' }}-100 text-{{ $job->category_info['color'] ?? 'gray' }}-700">{{ $job->category_info['label'] }}</span>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                </td>
                <td class="px-4 py-3 text-gray-600 font-semibold text-sm">{{ $job->applications_count ?? $job->applications->count() }}</td>
                <td class="px-5 py-3 text-right">
                    <div class="flex justify-end gap-1">
                        <a href="{{ route('admin.job-posts.show', $job) }}" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <form action="{{ route('admin.job-posts.destroy', $job) }}" method="POST" onsubmit="return confirm('Xóa tin này?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Chưa có tin tuyển dụng nào.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($jobs->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $jobs->links() }}</div>
    @endif
</div>

@endsection
