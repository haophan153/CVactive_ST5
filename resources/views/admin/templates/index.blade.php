@extends('layouts.admin')
@section('title', 'Quản lý Templates')
@section('page-title', 'Templates CV')

@section('breadcrumb')
<span class="text-slate-900 font-bold truncate">Templates</span>
@endsection

@section('content')

{{-- Toolbar --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.templates.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Thêm template
        </a>
        <div class="flex bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <a href="{{ route('admin.templates.index', array_merge(request()->except('view'), ['view' => 'grid'])) }}"
               class="px-3.5 py-2 text-xs font-semibold transition-colors {{ $view === 'grid' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Lưới</a>
            <a href="{{ route('admin.templates.index', array_merge(request()->except('view'), ['view' => 'list'])) }}"
               class="px-3.5 py-2 text-xs font-semibold border-l border-slate-200 transition-colors {{ $view === 'list' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Danh sách</a>
        </div>
    </div>
    <p class="text-sm font-semibold text-slate-500">{{ $templates->total() }} templates</p>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Tổng</p>
        <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-emerald-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Đang hoạt động</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['active']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-amber-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Premium</p>
        <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ number_format($stats['premium']) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-500">Vô hiệu</p>
        <p class="text-2xl font-extrabold text-slate-400 mt-1">{{ number_format($stats['inactive']) }}</p>
    </div>
</div>

{{-- Filter chips --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-5">
    <form method="GET" class="space-y-3">
        <input type="hidden" name="view" value="{{ $view }}">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-52">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên template..."
                    class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all placeholder-slate-400">
            </div>
            @php $chips = [['value'=>'all','label'=>'Tất cả'],['value'=>'premium','label'=>'Premium','color'=>'amber'],['value'=>'free','label'=>'Miễn phí'],['value'=>'active','label'=>'Active','color'=>'emerald'],['value'=>'inactive','label'=>'Inactive']]; @endphp
            @foreach($chips as $chip)
            <a href="{{ route('admin.templates.index', array_merge(request()->except(['chip']), ['chip' => $chip['value']])) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all
               {{ (request('chip', 'all') === $chip['value']) ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300' }}">
                {{ $chip['label'] }}
            </a>
            @endforeach
        </div>
    </form>
</div>

{{-- Grid view --}}
@if($view === 'grid')
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
    @forelse($templates as $template)
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden group hover:shadow-lg hover:border-indigo-200/60 transition-all duration-200">
        <div class="aspect-[3/4] bg-slate-100 relative overflow-hidden">
            @if($template->thumbnail)
                <img src="{{ $template->thumbnail_url }}" alt="{{ $template->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-300">
                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            @endif

            <div class="absolute top-2 left-2 flex flex-col gap-1">
                @if($template->is_premium)
                <span class="bg-amber-400 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow">PRO</span>
                @endif
                @if(!$template->is_active)
                <span class="bg-slate-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow">Ẩn</span>
                @endif
            </div>

            <div class="absolute top-2 right-2 flex flex-col gap-1.5">
                <button onclick="toggleTemplate({{ $template->id }}, 'is_premium')"
                        class="w-7 h-7 rounded-full text-[10px] font-extrabold flex items-center justify-center shadow transition-all duration-200 {{ $template->is_premium ? 'bg-amber-400 text-white' : 'bg-white/90 text-slate-500 hover:bg-amber-100' }}" title="Toggle premium">P</button>
                <button onclick="toggleTemplate({{ $template->id }}, 'is_active')"
                        class="w-7 h-7 rounded-full text-[10px] font-extrabold flex items-center justify-center shadow transition-all duration-200 {{ $template->is_active ? 'bg-emerald-500 text-white' : 'bg-white/90 text-slate-500 hover:bg-emerald-100' }}" title="Toggle active">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>

            <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-sm">
                <a href="{{ route('admin.templates.edit', $template) }}" class="px-4 py-2 bg-white text-slate-800 text-xs font-bold rounded-lg hover:bg-slate-100 transition-colors shadow-lg">Sửa</a>
                <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                      onsubmit="return confirm('Xóa template {{ addslashes($template->name) }}?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="px-4 py-2 bg-red-500 text-white text-xs font-bold rounded-lg hover:bg-red-600 transition-colors shadow-lg">Xóa</button>
                </form>
            </div>
        </div>
        <div class="p-3">
            <p class="font-bold text-sm text-slate-900 truncate">{{ $template->name }}</p>
            <p class="text-xs text-slate-400 truncate mt-0.5">{{ $template->category?->name ?? 'Chưa phân loại' }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ number_format($template->usage_count) }} lượt dùng</p>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center text-slate-400">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/></svg>
        </div>
        <p class="font-medium">Chưa có template nào</p>
    </div>
    @endforelse
</div>

{{-- List view --}}
@else
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Template</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Danh mục</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Premium</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Hoạt động</th>
                <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Lượt dùng</th>
                <th class="text-right px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($templates as $template)
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <img src="{{ $template->thumbnail_url ?? asset('storage/avatars/logo/logo.png') }}" class="w-12 h-12 rounded-xl object-cover bg-slate-100 shadow-sm" onerror="this.src='{{ asset('storage/avatars/logo/logo1.png') }}'">
                        <span class="font-bold text-slate-900">{{ $template->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3.5 text-slate-500 text-sm">{{ $template->category?->name ?? '—' }}</td>
                <td class="px-4 py-3.5">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" {{ $template->is_premium ? 'checked' : '' }} onclick="toggleTemplate({{ $template->id }}, 'is_premium')" class="sr-only peer">
                        <span class="w-10 h-5 bg-slate-200 peer-checked:bg-amber-500 rounded-full transition relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:w-4 after:h-4 after:transition-all peer-checked:after:translate-x-5"></span>
                    </label>
                </td>
                <td class="px-4 py-3.5">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" {{ $template->is_active ? 'checked' : '' }} onclick="toggleTemplate({{ $template->id }}, 'is_active')" class="sr-only peer">
                        <span class="w-10 h-5 bg-slate-200 peer-checked:bg-emerald-500 rounded-full transition relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:w-4 after:h-4 after:transition-all peer-checked:after:translate-x-5"></span>
                    </label>
                </td>
                <td class="px-4 py-3.5 text-slate-500 font-medium">{{ number_format($template->usage_count) }}</td>
                <td class="px-5 py-3.5 text-right">
                    <div class="flex justify-end gap-1">
                        <a href="{{ route('admin.templates.edit', $template) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Sửa">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('admin.templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Xóa?')" class="inline">
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
                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/></svg>
                </div>
                <p class="font-medium">Chưa có template nào</p>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@if($templates->hasPages())
<div class="mt-5">{{ $templates->links() }}</div>
@endif

<script>
async function toggleTemplate(id, field) {
    try {
        const res = await fetch(`{{ url('admin/templates') }}/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ field }),
        });
        if (res.ok) location.reload();
    } catch (e) { location.reload(); }
}
</script>
@endsection
