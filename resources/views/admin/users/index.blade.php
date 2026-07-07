@extends('layouts.admin')
@section('title', 'Quản lý người dùng')
@section('page-title', 'Người dùng')

@section('breadcrumb')
<span class="text-gray-900 font-semibold truncate">Người dùng</span>
@endsection

@php
    $colsVisible = ['avatar','plan','role','registered','actions'];
    $allColumns = [
        'avatar'     => ['label' => 'Người dùng', 'key' => 'avatar'],
        'plan'       => ['label' => 'Gói',        'key' => 'plan'],
        'role'       => ['label' => 'Vai trò',    'key' => 'role'],
        'registered' => ['label' => 'Đăng ký',    'key' => 'registered'],
        'verified'   => ['label' => 'Xác minh',   'key' => 'verified'],
        'cvs'        => ['label' => 'Số CV',      'key' => 'cvs'],
        'actions'    => ['label' => 'Thao tác',   'key' => 'actions'],
    ];
@endphp

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

{{-- Filters + Toolbar --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email..."
                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Vai trò</label>
            <select name="role" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tất cả</option>
                @foreach(['user', 'hr', 'admin'] as $r)
                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Gói</label>
            <select name="plan" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tất cả</option>
                <option value="none" {{ request('plan') === 'none' ? 'selected' : '' }}>Chưa có gói</option>
                @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Xác minh</label>
            <select name="verified" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Tất cả</option>
                <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Đã xác minh</option>
                <option value="no"  {{ request('verified') === 'no'  ? 'selected' : '' }}>Chưa xác minh</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Từ ngày</label>
            <input type="date" name="from" value="{{ request('from') }}" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Đến ngày</label>
            <input type="date" name="to" value="{{ request('to') }}" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Sắp xếp</label>
            <select name="sort" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="latest"     {{ request('sort','latest')==='latest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="oldest"     {{ request('sort')==='oldest' ? 'selected' : '' }}>Cũ nhất</option>
                <option value="name_asc"   {{ request('sort')==='name_asc' ? 'selected' : '' }}>Tên A→Z</option>
                <option value="name_desc"  {{ request('sort')==='name_desc' ? 'selected' : '' }}>Tên Z→A</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Lọc</button>
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Reset</a>
        <a href="{{ route('admin.users.export', request()->all()) }}" class="px-4 py-2 bg-emerald-50 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-100 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            CSV
        </a>
    </form>
</div>

{{-- Bulk action bar --}}
<form x-ref="bulkForm" method="POST" action="{{ route('admin.users.bulk') }}" class="mb-4 flex items-center gap-2" x-show="selected.length > 0" x-cloak>
    @csrf
    <input type="hidden" name="action" value="">
    <input type="hidden" name="value" value="">
    <span class="text-sm text-gray-600" x-text="'Đã chọn ' + selected.length + ' người dùng:'"></span>
    <select @change="applyBulk('set_role', $event.target.value); $event.target.value = ''" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
        <option value="">Đổi vai trò...</option>
        @foreach(['user', 'hr', 'admin'] as $r)
        <option value="{{ $r }}">{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    <select @change="applyBulk('set_plan', $event.target.value); $event.target.value = ''" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
        <option value="">Gán gói...</option>
        <option value="">Bỏ gói</option>
        @foreach($plans as $plan)
        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
        @endforeach
    </select>
    <button type="button" @click="applyBulk('delete')" class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition">Xóa</button>
    <button type="button" @click="selected = []; selectAll = false; document.querySelectorAll('.user-checkbox').forEach(c => c.checked = false)" class="ml-auto px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Bỏ chọn</button>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">{{ number_format($users->total()) }} người dùng</h3>
        <span class="text-xs text-gray-500" x-show="selected.length === 0">Chọn nhiều bằng cách tick checkbox để bulk action.</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 w-10">
                        <input type="checkbox" x-model="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Người dùng</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Gói</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Vai trò</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Đăng ký</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 overflow-hidden
                                {{ $user->role === 'admin' ? 'bg-red-500' : 'bg-indigo-500' }}">
                                @if($user->avatar && !str_starts_with($user->avatar, 'http'))
                                    <img src="{{ asset('storage/'.$user->avatar) }}" class="w-full h-full object-cover">
                                @elseif($user->avatar)
                                    <img src="{{ $user->avatar }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <select onchange="quickUserUpdate({{ $user->id }}, 'plan_id', this.value)"
                            class="text-xs border border-gray-300 rounded-md px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                            <option value="" {{ !$user->plan_id ? 'selected' : '' }}>Không</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ $user->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-3">
                        <select onchange="quickUserUpdate({{ $user->id }}, 'role', this.value)"
                            class="text-xs border border-gray-300 rounded-md px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white
                            {{ $user->role === 'admin' ? 'text-red-700' : ($user->role === 'hr' ? 'text-purple-700' : 'text-blue-700') }}">
                            <option value="user"  {{ $user->role==='user'  ? 'selected' : '' }}>User</option>
                            <option value="hr"    {{ $user->role==='hr'    ? 'selected' : '' }}>HR</option>
                            <option value="admin" {{ $user->role==='admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        <div>{{ $user->created_at->format('d/m/Y') }}</div>
                        <div class="text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end space-x-2">
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
                                class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition" title="Xem nhanh">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="p-1.5 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition" title="Sửa">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                onsubmit="return confirm('Xóa người dùng {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1z"/></svg>
                    Không tìm thấy người dùng nào.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- Quick view slide-over --}}
<div x-data="{ user: null, open: false, openQuickView(data) { this.user = data; this.open = true; }, close() { this.open = false; this.user = null; } }"
    @keydown.escape.window="if (open) close()">

    <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 bg-black/40 z-50" @click="close()"></div>

    <aside x-show="open" x-cloak x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 w-full sm:w-96 bg-white shadow-2xl z-50 overflow-y-auto">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Chi tiết nhanh</h2>
            <button @click="close()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-5" x-show="user">
            <div class="text-center pb-5">
                <div class="w-20 h-20 rounded-full mx-auto mb-3 overflow-hidden bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-bold">
                    <template x-if="user && user.avatar">
                        <img :src="user.avatar" class="w-full h-full object-cover">
                    </template>
                    <template x-if="user && !user.avatar">
                        <span x-text="user.name.charAt(0).toUpperCase()"></span>
                    </template>
                </div>
                <h3 class="font-bold text-gray-900 text-lg" x-text="user?.name"></h3>
                <p class="text-sm text-gray-500" x-text="user?.email"></p>
                <div class="flex justify-center gap-2 mt-3">
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full"
                        :class="{
                            'bg-red-100 text-red-700': user?.role === 'admin',
                            'bg-purple-100 text-purple-700': user?.role === 'hr',
                            'bg-blue-100 text-blue-700': user?.role === 'user'
                        }" x-text="user?.role"></span>
                    <template x-if="user?.plan">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700" x-text="user.plan"></span>
                    </template>
                </div>
            </div>

            <dl class="space-y-2 text-sm border-t border-gray-100 pt-4">
                <div class="flex justify-between"><dt class="text-gray-500">Đăng ký</dt><dd class="font-medium" x-text="user?.joined"></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Xác minh email</dt><dd class="font-medium" x-text="user?.verified ? 'Đã xác minh' : 'Chưa'"></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Google OAuth</dt><dd class="font-medium" x-text="user?.google ? 'Đã liên kết' : 'Không'"></dd></div>
            </dl>

            <div class="mt-5 pt-4 border-t border-gray-100 space-y-2">
                <a :href="user?.showUrl" class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">Mở trang chi tiết</a>
                <a :href="user?.editUrl" class="block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg">Chỉnh sửa</a>
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
