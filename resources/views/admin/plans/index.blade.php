@extends('layouts.admin')
@section('title', 'Quản lý Gói dịch vụ')
@section('page-title', 'Gói dịch vụ')

@section('breadcrumb')
<span class="text-slate-900 font-bold">Gói dịch vụ</span>
@endsection

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <p class="text-sm font-semibold text-slate-500">Cấu hình giá + tính năng cho từng gói</p>
    <a href="{{ route('admin.plans.create') }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Thêm gói
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($plans as $plan)
    <div class="bg-white rounded-2xl border {{ $plan->is_active ? 'border-slate-200/80 shadow-sm hover:shadow-lg hover:border-indigo-200/60' : 'border-slate-200/40 opacity-60' }} overflow-hidden transition-all duration-200">
        <div class="px-6 py-6 bg-gradient-to-br {{ $plan->slug === 'pro' ? 'from-indigo-600 to-violet-700 text-white' : ($plan->slug === 'business' ? 'from-amber-400 to-orange-600 text-white' : 'from-slate-100 to-slate-200 text-slate-800') }}">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-60">{{ $plan->slug }}</p>
                    <h3 class="text-2xl font-extrabold mt-0.5">{{ $plan->name }}</h3>
                </div>
                <form action="{{ route('admin.plans.toggle', $plan) }}" method="POST">
                    @csrf
                    <button class="px-3 py-1.5 text-xs font-bold rounded-full {{ $plan->is_active ? 'bg-white/20 backdrop-blur text-white' : 'bg-slate-300/50 text-slate-600' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </form>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold">{{ number_format($plan->price, 0, ',', '.') }}₫</span>
                <span class="text-sm opacity-70 font-medium">/tháng</span>
            </div>
        </div>

        <div class="px-6 py-5 space-y-3">
            <ul class="space-y-2.5 text-sm">
                <li class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    Tối đa <strong class="font-extrabold">{{ $plan->cv_limit ?? '∞' }}</strong> CV
                </li>
                @if(is_array($plan->features))
                    @foreach(array_slice($plan->features, 0, 5) as $f)
                    <li class="flex items-start gap-2.5 text-slate-600">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>{{ $f }}</span>
                    </li>
                    @endforeach
                @endif
            </ul>

            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                <div class="bg-slate-50 rounded-xl p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Users</p>
                    <p class="font-extrabold text-slate-900 text-lg mt-0.5">{{ number_format($plan->users_count) }}</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Doanh thu</p>
                    <p class="font-extrabold text-emerald-600 text-lg mt-0.5">{{ number_format($plan->revenue ?? 0, 0, ',', '.') }}₫</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3">
            <a href="{{ route('admin.plans.edit', $plan) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Chỉnh sửa</a>
            @if($plan->users_count === 0)
            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Xóa gói {{ $plan->name }}?')" class="inline">
                @csrf @method('DELETE')
                <button class="text-sm font-bold text-red-500 hover:text-red-700 transition-colors">Xóa</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@if($plans->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-16 text-center text-slate-400">
    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
    </div>
    <p class="font-semibold">Chưa có gói dịch vụ nào. Tạo gói đầu tiên!</p>
</div>
@endif

@endsection
