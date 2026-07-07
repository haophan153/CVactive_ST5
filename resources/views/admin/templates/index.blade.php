@extends('layouts.admin')
@section('title', 'Quản lý Templates')
@section('page-title', 'Templates CV')

@section('breadcrumb')
<span class="text-gray-900 font-semibold truncate">Templates</span>
@endsection

@section('content')

{{-- Toolbar --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.templates.create') }}" class="flex items-center space-x-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Thêm template</span>
        </a>
        <div class="flex border border-gray-300 rounded-lg overflow-hidden">
            <a href="{{ route('admin.templates.index', array_merge(request()->except('view'), ['view' => 'grid'])) }}"
               class="px-3 py-1.5 text-xs font-semibold {{ $view === 'grid' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">Grid</a>
            <a href="{{ route('admin.templates.index', array_merge(request()->except('view'), ['view' => 'list'])) }}"
               class="px-3 py-1.5 text-xs font-semibold border-l border-gray-300 {{ $view === 'list' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">List</a>
        </div>
    </div>
    <p class="text-sm text-gray-500">{{ $templates->total() }} templates</p>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Tổng</p>
        <p class="text-xl font-extrabold text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-green-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Đang hoạt động</p>
        <p class="text-xl font-extrabold text-green-600">{{ number_format($stats['active']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-amber-100 shadow-sm p-4">
        <p class="text-xs text-gray-500">Premium</p>
        <p class="text-xl font-extrabold text-amber-600">{{ number_format($stats['premium']) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs text-gray-500">Vô hiệu</p>
        <p class="text-xl font-extrabold text-gray-500">{{ number_format($stats['inactive']) }}</p>
    </div>
</div>

{{-- Filter chips + search --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" class="space-y-3">
        <input type="hidden" name="view" value="{{ $view }}">
        <div class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên template..."
                class="flex-1 min-w-48 text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            @php $chips = [['value'=>'all',     'label'=>'Tất cả'], ['value'=>'premium','label'=>'Premium', 'color'=>'amber'], ['value'=>'free','label'=>'Miễn phí'], ['value'=>'active','label'=>'Active', 'color'=>'green'], ['value'=>'inactive','label'=>'Inactive']]; @endphp
            @foreach($chips as $chip)
            <a href="{{ route('admin.templates.index', array_merge(request()->except(['chip']), ['chip' => $chip['value']])) }}"
               class="px-3 py-1.5 rounded-full text-xs font-medium border transition
               {{ (request('chip', 'all') === $chip['value']) ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                {{ $chip['label'] }}
            </a>
            @endforeach
        </div>
    </form>
</div>

{{-- Grid/List --}}
@if($view === 'grid')
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        @forelse($templates as $template)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden group">
            <div class="aspect-[3/4] bg-gray-100 relative overflow-hidden">
                @if($template->thumbnail)
                    <img src="{{ $template->thumbnail_url }}" alt="{{ $template->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif

                <div class="absolute top-2 left-2 flex flex-col gap-1">
                    @if($template->is_premium)
                    <span class="bg-amber-400 text-white text-xs font-bold px-2 py-0.5 rounded-full">PRO</span>
                    @endif
                    @if(!$template->is_active)
                    <span class="bg-gray-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Ẩn</span>
                    @endif
                </div>

                {{-- Inline toggles --}}
                <div class="absolute top-2 right-2 flex flex-col gap-1.5">
                    <button onclick="toggleTemplate({{ $template->id }}, 'is_premium')"
                            class="w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center shadow transition {{ $template->is_premium ? 'bg-amber-400 text-white' : 'bg-white/80 text-gray-500 hover:bg-amber-100' }}" title="Toggle premium">P</button>
                    <button onclick="toggleTemplate({{ $template->id }}, 'is_active')"
                            class="w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center shadow transition {{ $template->is_active ? 'bg-green-500 text-white' : 'bg-white/80 text-gray-500 hover:bg-green-100' }}" title="Toggle active">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </div>

                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-2">
                    <a href="{{ route('admin.templates.edit', $template) }}" class="px-3 py-1.5 bg-white text-gray-800 text-xs font-semibold rounded-lg hover:bg-gray-100">Sửa</a>
                    <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                          onsubmit="return confirm('Xóa template {{ addslashes($template->name) }}?')" class="inline">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600">Xóa</button>
                    </form>
                </div>
            </div>
            <div class="p-3">
                <p class="font-semibold text-sm text-gray-800 truncate">{{ $template->name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ $template->category?->name ?? 'Chưa phân loại' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ number_format($template->usage_count) }} lượt dùng</p>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-gray-400">Chưa có template nào.</div>
        @endforelse
    </div>
@else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Template</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Danh mục</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Premium</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Hoạt động</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Lượt dùng</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($templates as $template)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $template->thumbnail_url ?? asset('storage/avatars/logo/logo.png') }}" class="w-12 h-12 rounded object-cover bg-gray-100" onerror="this.src='{{ asset('storage/avatars/logo/logo1.png') }}'">
                            <span class="font-medium text-gray-900">{{ $template->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $template->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" {{ $template->is_premium ? 'checked' : '' }} onclick="toggleTemplate({{ $template->id }}, 'is_premium')" class="sr-only peer">
                            <span class="w-9 h-5 bg-gray-200 peer-checked:bg-amber-500 rounded-full transition relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:w-4 after:h-4 after:transition-all peer-checked:after:translate-x-4"></span>
                        </label>
                    </td>
                    <td class="px-4 py-3">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" {{ $template->is_active ? 'checked' : '' }} onclick="toggleTemplate({{ $template->id }}, 'is_active')" class="sr-only peer">
                            <span class="w-9 h-5 bg-gray-200 peer-checked:bg-green-500 rounded-full transition relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:w-4 after:h-4 after:transition-all peer-checked:after:translate-x-4"></span>
                        </label>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ number_format($template->usage_count) }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            <a href="{{ route('admin.templates.edit', $template) }}" class="p-1.5 text-indigo-500 hover:bg-indigo-50 rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                            <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                                  onsubmit="return confirm('Xóa?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">Chưa có template nào.</td></tr>
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
