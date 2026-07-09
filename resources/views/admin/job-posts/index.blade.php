@extends('layouts.admin')
@section('title', 'Quản lý Tin tuyển dụng')
@section('page-title', 'Tin tuyển dụng')

@section('breadcrumb')
<span class="text-slate-900 font-bold">Tin tuyển dụng</span>
@endsection

@section('content')

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Tổng</p>
        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-emerald-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Đang tuyển</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['open']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Đã đóng</p>
        <p class="text-2xl font-extrabold text-slate-400 mt-1">{{ number_format($stats['closed']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-indigo-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Tổng đơn ứng tuyển</p>
        <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ number_format($stats['applications']) }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-52">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tìm kiếm</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề / công ty..."
                    class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all placeholder-slate-400">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Trạng thái</label>
            <select name="status" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đang tuyển</option>
                <option value="closed"    {{ request('status') === 'closed'    ? 'selected' : '' }}>Đã đóng</option>
                <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Nháp</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Danh mục</label>
            <select name="category" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                @foreach(\App\Models\JobPost::CATEGORIES as $key => $info)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Cấp</label>
            <select name="experience" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                @foreach(\App\Models\JobPost::EXPERIENCE_LEVELS as $key => $info)
                <option value="{{ $key }}" {{ request('experience') === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">HR sở hữu</label>
            <select name="owner" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                <option value="none" {{ request('owner') === 'none' ? 'selected' : '' }}>Không có</option>
                @foreach($owners as $o)
                <option value="{{ $o->id }}" {{ request('owner') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Từ ngày</label>
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Đến ngày</label>
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">Lọc</button>
        @if(request()->hasAny(['search','status','category','experience','owner','from','to']))
        <a href="{{ route('admin.job-posts.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-all">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Tin</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">HR sở hữu</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Danh mục</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Đơn</th>
                <th class="text-right px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($jobs as $job)
            @php
                $statusColor = match($job->status) {
                    'published' => 'bg-emerald-50 text-emerald-600',
                    'closed'    => 'bg-slate-100 text-slate-500',
                    'draft'     => 'bg-amber-50 text-amber-600',
                    default     => 'bg-slate-100 text-slate-500',
                };
                $statusLabel = match($job->status) {
                    'published' => 'Đang tuyển', 'closed' => 'Đã đóng', 'draft' => 'Nháp', default => $job->status,
                };
            @endphp
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xs font-extrabold shadow-md flex-shrink-0">
                            {{ $job->company_initials ?? strtoupper(substr($job->title, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 truncate max-w-xs">{{ $job->title }}</p>
                            <p class="text-xs text-slate-400">{{ $job->company_name ?? '—' }} · {{ $job->location ?? '' }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3.5 text-slate-600 font-medium">{{ $job->user?->name ?? '—' }}</td>
                <td class="px-4 py-3.5">
                    @if($job->category)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">{{ $job->category_info['label'] ?? $job->category }}</span>
                    @else
                    <span class="text-slate-400">—</span>
                    @endif
                </td>
                <td class="px-4 py-3.5">
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                </td>
                <td class="px-4 py-3.5 font-bold text-slate-700">{{ $job->applications_count ?? $job->applications->count() }}</td>
                <td class="px-5 py-3.5 text-right">
                    <div class="flex justify-end gap-1">
                        <a href="{{ route('admin.job-posts.show', $job) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Xem">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <form action="{{ route('admin.job-posts.destroy', $job) }}" method="POST" onsubmit="return confirm('Xóa tin này?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-16 text-center text-slate-400">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <p class="font-medium">Chưa có tin tuyển dụng nào</p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    @if($jobs->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">{{ $jobs->links() }}</div>
    @endif
</div>

@endsection
