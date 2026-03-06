@extends('layouts.admin')
@section('title', 'Chi tiết – ' . $user->name)
@section('page-title', 'Chi tiết người dùng')

@section('content')

<div class="grid lg:grid-cols-3 gap-5">

    {{-- Profile card --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-center">
            <div class="w-20 h-20 rounded-full mx-auto mb-4 overflow-hidden bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-bold">
                @if($user->avatar)
                    <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/'.$user->avatar) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
            <div class="flex justify-center gap-2 mt-3">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">{{ $user->role }}</span>
                @if($user->plan)
                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">{{ $user->plan->name }}</span>
                @endif
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 text-left space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Đăng ký</span><span class="font-medium">{{ $user->created_at->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Số CV</span><span class="font-medium">{{ $user->cvs->count() }}</span></div>
                @if($user->plan_expires_at)
                <div class="flex justify-between"><span class="text-gray-500">Gói hết hạn</span><span class="font-medium {{ $user->plan_expires_at < now() ? 'text-red-500' : 'text-green-600' }}">{{ $user->plan_expires_at->format('d/m/Y') }}</span></div>
                @endif
                @if($user->google_id)
                <div class="flex justify-between"><span class="text-gray-500">Google OAuth</span><span class="text-green-600 font-medium">Đã liên kết</span></div>
                @endif
            </div>
            <a href="{{ route('admin.users.edit', $user) }}" class="mt-4 block text-center py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Chỉnh sửa</a>
        </div>
    </div>

    {{-- CV list + Payments --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- CVs --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">CV đã tạo ({{ $user->cvs->count() }})</h3>
            </div>
            @if($user->cvs->isEmpty())
            <p class="px-5 py-4 text-sm text-gray-400">Chưa tạo CV nào.</p>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($user->cvs as $cv)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <p class="font-medium text-sm text-gray-800">{{ $cv->title }}</p>
                        <p class="text-xs text-gray-400">{{ $cv->template?->name ?? 'Không rõ template' }} · {{ $cv->updated_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($cv->is_draft)
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Draft</span>
                        @endif
                        <span class="text-xs bg-{{ $cv->visibility === 'public' ? 'green' : 'gray' }}-100 text-{{ $cv->visibility === 'public' ? 'green' : 'gray' }}-600 px-2 py-0.5 rounded-full">{{ $cv->visibility }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Payments --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Lịch sử thanh toán ({{ $user->payments->count() }})</h3>
            </div>
            @if($user->payments->isEmpty())
            <p class="px-5 py-4 text-sm text-gray-400">Chưa có giao dịch.</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50"><tr>
                        <th class="text-left px-5 py-2 text-xs text-gray-500">Gói</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500">Phương thức</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500">Số tiền</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500">Trạng thái</th>
                        <th class="text-left px-4 py-2 text-xs text-gray-500">Ngày</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($user->payments as $p)
                        <tr>
                            <td class="px-5 py-2 font-medium">{{ $p->plan->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $p->payment_method }}</td>
                            <td class="px-4 py-2 font-semibold">{{ number_format($p->amount, 0, ',', '.') }}₫</td>
                            <td class="px-4 py-2">
                                @php $c = match($p->status) { 'completed'=>'green','pending'=>'yellow','failed'=>'red',default=>'gray' }; @endphp
                                <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $c }}-100 text-{{ $c }}-700">{{ $p->status }}</span>
                            </td>
                            <td class="px-4 py-2 text-gray-400 text-xs">{{ $p->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
