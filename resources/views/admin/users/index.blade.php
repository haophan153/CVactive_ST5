@extends('layouts.admin')
@section('title', 'Quản lý người dùng')
@section('page-title', 'Người dùng')

@section('breadcrumb')
<span class="text-slate-900 font-bold truncate">Người dùng</span>
@endsection

@section('content')
<div x-data="{
    selected: [],
    selectAll: false,
    toggleAll() {
        const ids = [...document.querySelectorAll('.user-checkbox')].map(c => c.value);
        this.selected = this.selectAll ? ids : [];
        document.querySelectorAll('.user-checkbox').forEach(c => c.checked = this.selectAll);
    },
    applyBulk(action, value = '') {
        if (this.selected.length === 0) { alert('Vui lòng chọn ít nhất 1 người dùng.'); return; }
        let msg = '';
        if (action === 'delete') msg = 'Xóa ' + this.selected.length + ' người dùng?';
        else if (action === 'set_role') msg = 'Đổi vai trò thành \"' + (value || '') + '\"?';
        else if (action === 'set_plan') msg = 'Gán gói \"' + (value || '(trống)') + '\"?';
        if (!confirm(msg)) return;
        this.$refs.bulkForm.action.value = action;
        this.$refs.bulkForm.value.value = value;
        this.$refs.bulkForm.submit();
    }
}" x-init="$watch('selectAll', () => toggleAll())">

{{-- Toolbar --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-2">
        <span class="text-sm font-semibold text-slate-500">{{ number_format($users->total()) }} người dùng</span>
    </div>
    <div class="flex items-center gap-2">
        <div class="flex bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <a href="{{ route('admin.users.index', array_merge(request()->except('view'), ['view' => 'table'])) }}"
               class="px-3.5 py-2 text-xs font-semibold transition-colors {{ request('view', 'table') === 'table' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Bảng</a>
            <a href="{{ route('admin.users.index', array_merge(request()->except('view'), ['view' => 'grid'])) }}"
               class="px-3.5 py-2 text-xs font-semibold border-l border-slate-200 transition-colors {{ request('view') === 'grid' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Lưới</a>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-52">
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Tìm kiếm</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email..."
                    class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white placeholder-slate-400 transition-all">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Vai trò</label>
            <select name="role" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                @foreach(['user', 'hr', 'admin'] as $r)
                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Gói</label>
            <select name="plan" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                <option value="none" {{ request('plan') === 'none' ? 'selected' : '' }}>Chưa có gói</option>
                @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Xác minh</label>
            <select name="verified" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="">Tất cả</option>
                <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Đã xác minh</option>
                <option value="no"  {{ request('verified') === 'no'  ? 'selected' : '' }}>Chưa xác minh</option>
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
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sắp xếp</label>
            <select name="sort" class="text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all">
                <option value="latest"    {{ request('sort','latest')==='latest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="oldest"    {{ request('sort')==='oldest' ? 'selected' : '' }}>Cũ nhất</option>
                <option value="name_asc"  {{ request('sort')==='name_asc' ? 'selected' : '' }}>Tên A→Z</option>
                <option value="name_desc" {{ request('sort')==='name_desc' ? 'selected' : '' }}>Tên Z→A</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-500/20">Lọc</button>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-colors">Reset</a>
        <a href="{{ route('admin.users.export', request()->all()) }}" class="flex items-center gap-1.5 px-4 py-2 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            CSV
        </a>
    </form>
</div>

{{-- Bulk action bar --}}
<form x-ref="bulkForm" method="POST" action="{{ route('admin.users.bulk') }}" class="mb-4 flex items-center gap-3 p-3 bg-indigo-600/5 border border-indigo-200/60 rounded-2xl" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="">
    <input type="hidden" name="value" value="">
    <span class="text-sm font-semibold text-indigo-700" x-text="'Đã chọn ' + selected.length + ' người dùng'"></span>
    <div class="w-px h-5 bg-indigo-200"></div>
    <select @change="applyBulk('set_role', $event.target.value); $event.target.value = ''" class="text-sm bg-white border border-slate-200 rounded-xl px-3 py-1.5 font-medium">
        <option value="">Đổi vai trò...</option>
        @foreach(['user', 'hr', 'admin'] as $r)
        <option value="{{ $r }}">{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <select @change="applyBulk('set_plan', $event.target.value); $event.target.value = ''" class="text-sm bg-white border border-slate-200 rounded-xl px-3 py-1.5 font-medium">
        <option value="">Gán gói...</option>
        <option value="">Bỏ gói</option>
        @foreach($plans as $plan)
        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
        @endforeach
    </select>
    <button type="button" @click="applyBulk('delete')" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 font-semibold rounded-xl hover:bg-red-100 transition-colors">Xóa</button>
    <button type="button" @click="selected = []; selectAll = false; document.querySelectorAll('.user-checkbox').forEach(c => c.checked = false)" class="ml-auto px-3 py-1.5 text-sm bg-white text-slate-500 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Bỏ chọn</button>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <h3 class="font-bold text-slate-900">{{ number_format($users->total()) }} người dùng</h3>
            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-full" x-show="selected.length === 0">Chọn nhiều để bulk action</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3.5 w-10">
                        <input type="checkbox" x-model="selectAll" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Người dùng</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Gói</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Vai trò</th>
                    <th class="text-left px-4 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Đăng ký</th>
                    <th class="text-right px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-5 py-3.5">
                        <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="user-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold flex-shrink-0 overflow-hidden shadow-md
                                {{ $user->role === 'admin' ? 'bg-gradient-to-br from-red-500 to-rose-600' : 'bg-gradient-to-br from-indigo-500 to-violet-600' }}">
                                @if($user->avatar && !str_starts_with($user->avatar, 'http'))
                                    <img src="{{ asset('storage/'.$user->avatar) }}" class="w-full h-full object-cover">
                                @elseif($user->avatar)
                                    <img src="{{ $user->avatar }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <select onchange="quickUserUpdate({{ $user->id }}, 'plan_id', this.value)"
                            class="text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 bg-slate-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all font-medium">
                            <option value="" {{ !$user->plan_id ? 'selected' : '' }}>—</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ $user->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3.5">
                        <select onchange="quickUserUpdate({{ $user->id }}, 'role', this.value)"
                            class="text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 bg-slate-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all font-bold
                            {{ $user->role === 'admin' ? 'text-red-600' : ($user->role === 'hr' ? 'text-violet-600' : 'text-blue-600') }}">
                            <option value="user"  {{ $user->role==='user'  ? 'selected' : '' }}>User</option>
                            <option value="hr"    {{ $user->role==='hr'    ? 'selected' : '' }}>HR</option>
                            <option value="admin" {{ $user->role==='admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="text-sm font-medium text-slate-700">{{ $user->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-slate-400">{{ $user->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button type="button" @click="openQuickView(@js([
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'role' => $user->role,
                                'plan' => $user->plan->name ?? null,
                                'avatar' => $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/'.$user->avatar)) : null,
                                'cvs' => $user->cvs_count ?? 0,
                                'joined' => $user->created_at->format('d/m/Y H:i'),
                                'verified' => (bool) $user->email_verified_at,
                                'google' => (bool) $user->google_id,
                                'showUrl' => route('admin.users.show', $user),
                                'editUrl' => route('admin.users.edit', $user),
                            ]))"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Xem nhanh">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Sửa">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                onsubmit="return confirm('Xóa người dùng {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-16 text-center text-slate-400">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/></svg>
                        </div>
                        <p class="font-medium text-slate-500">Không tìm thấy người dùng nào</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- Quick view slide-over --}}
<div x-data="{ user: null, open: false, openQuickView(data) { this.user = data; this.open = true; }, close() { this.open = false; this.user = null; } }"
    @keydown.escape.window="if (open) close()">

    <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50" @click="close()"></div>

    <aside x-show="open" x-cloak x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-full sm:w-[28rem] bg-white shadow-2xl z-50 overflow-y-auto">

        <div class="sticky top-0 bg-white z-10 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-bold text-slate-900">Chi tiết người dùng</h2>
            <button @click="close()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6" x-show="user">
            <div class="text-center pb-6 border-b border-slate-100">
                <div class="w-20 h-20 rounded-2xl mx-auto mb-4 overflow-hidden bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-2xl font-extrabold shadow-lg shadow-indigo-500/30">
                    <template x-if="user && user.avatar">
                        <img :src="user.avatar" class="w-full h-full object-cover">
                    </template>
                    <template x-if="user && !user.avatar">
                        <span x-text="user.name.charAt(0).toUpperCase()"></span>
                    </template>
                </div>
                <h3 class="font-extrabold text-slate-900 text-lg" x-text="user?.name"></h3>
                <p class="text-sm text-slate-500" x-text="user?.email"></p>
                <div class="flex justify-center gap-2 mt-3">
                    <span class="px-3 py-1 text-xs font-bold rounded-full"
                        :class="{
                            'bg-red-50 text-red-600': user?.role === 'admin',
                            'bg-violet-50 text-violet-600': user?.role === 'hr',
                            'bg-blue-50 text-blue-600': user?.role === 'user'
                        }" x-text="user?.role?.toUpperCase()"></span>
                    <template x-if="user?.plan">
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-indigo-50 text-indigo-600" x-text="user.plan"></span>
                    </template>
                    <template x-if="user?.google">
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-600">Google</span>
                    </template>
                </div>
            </div>

            <dl class="space-y-3 py-5 border-b border-slate-100">
                <div class="flex justify-between items-center"><dt class="text-sm text-slate-500">Ngày đăng ký</dt><dd class="text-sm font-bold text-slate-900" x-text="user?.joined"></dd></div>
                <div class="flex justify-between items-center"><dt class="text-sm text-slate-500">Email xác minh</dt><dd class="text-sm font-bold" :class="user?.verified ? 'text-emerald-600' : 'text-amber-600'" x-text="user?.verified ? 'Đã xác minh' : 'Chưa xác minh'"></dd></div>
                <div class="flex justify-between items-center"><dt class="text-sm text-slate-500">CV đã tạo</dt><dd class="text-sm font-bold text-slate-900" x-text="user?.cvs + ' CV'"></dd></div>
            </dl>

            <div class="mt-5 space-y-2">
                <a :href="user?.showUrl" class="block w-full text-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors shadow-md shadow-indigo-500/20">Mở trang chi tiết</a>
                <a :href="user?.editUrl" class="block w-full text-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-xl transition-colors">Chỉnh sửa</a>
            </div>
        </div>
    </aside>
</div>

</div>

<script>
async function quickUserUpdate(id, field, value) {
    try {
        const res = await fetch(`{{ url('admin/users') }}/${id}/quick`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ field, value }),
        });
        const data = await res.json();
        if (!data.ok) {
            alert(data.msg || 'Cập nhật thất bại');
            location.reload();
        }
    } catch (e) {
        alert('Lỗi mạng');
    }
}
</script>
@endsection
