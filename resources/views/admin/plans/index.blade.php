@extends('layouts.admin')
@section('title', 'Quản lý Gói dịch vụ')
@section('page-title', 'Gói dịch vụ')

@section('breadcrumb')
<span class="text-gray-900 font-semibold">Gói dịch vụ</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-5">
    <p class="text-sm text-gray-500">Cấu hình giá + tính năng cho từng gói.</p>
    <a href="{{ route('admin.plans.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Thêm gói
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($plans as $plan)
    <div class="bg-white rounded-xl border {{ $plan->is_active ? 'border-gray-200 shadow-sm' : 'border-gray-200 opacity-60' }} overflow-hidden hover:shadow-md transition">
        <div class="px-6 py-5 bg-gradient-to-br {{ $plan->slug === 'pro' ? 'from-indigo-500 to-purple-600 text-white' : ($plan->slug === 'enterprise' ? 'from-amber-400 to-orange-500 text-white' : 'from-gray-100 to-gray-200 text-gray-900') }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium opacity-80">{{ ucfirst($plan->slug) }}</p>
                    <h3 class="text-2xl font-bold mt-1">{{ $plan->name }}</h3>
                </div>
                <form action="{{ route('admin.plans.toggle', $plan) }}" method="POST">
                    @csrf
                    <button class="px-2.5 py-1 text-xs rounded-full {{ $plan->is_active ? 'bg-white text-green-700' : 'bg-gray-700 text-gray-200' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </form>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold">{{ number_format($plan->price, 0, ',', '.') }}₫</span>
                <span class="text-sm opacity-70">/tháng</span>
            </div>
        </div>

        <div class="px-6 py-5 space-y-3">
            <ul class="space-y-2 text-sm">
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tối đa <strong>{{ $plan->cv_limit ?? '∞' }}</strong> CV</li>
                @if(is_array($plan->features))
                    @foreach(array_slice($plan->features, 0, 5) as $f)
                    <li class="flex items-start gap-2 text-gray-700 text-sm">
                        <svg class="w-4 h-4 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ $f }}</span>
                    </li>
                    @endforeach
                @endif
            </ul>

            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-500">User</p>
                    <p class="font-bold text-gray-900">{{ number_format($plan->users_count) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Doanh thu</p>
                    <p class="font-bold text-emerald-600">{{ number_format($plan->revenue ?? 0, 0, ',', '.') }}₫</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-2">
            <a href="{{ route('admin.plans.edit', $plan) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Chỉnh sửa</a>
            @if($plan->users_count === 0)
            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Xóa gói {{ $plan->name }}?')" class="inline">
                @csrf @method('DELETE')
                <button class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@if($plans->isEmpty())
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
    Chưa có gói dịch vụ nào. Tạo gói đầu tiên!
</div>
@endif

@endsection
