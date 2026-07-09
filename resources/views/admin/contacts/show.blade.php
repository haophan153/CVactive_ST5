@extends('layouts.admin')
@section('title', 'Chi tiết liên hệ')
@section('page-title', 'Liên hệ')

@section('breadcrumb')
<a href="{{ route('admin.contacts.index') }}" class="text-slate-500 hover:text-indigo-600">Liên hệ</a>
<span class="text-slate-400 mx-2">/</span>
<span class="text-slate-900 font-bold">#{{ $contact->id }}</span>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between">
        <div>
            <p class="font-bold text-slate-900">{{ $contact->name }} <span class="text-slate-400 font-normal text-sm">&lt;{{ $contact->email }}&gt;</span></p>
            <h2 class="text-lg font-extrabold text-slate-900 mt-2">{{ $contact->subject }}</h2>
            <p class="text-xs text-slate-400 mt-1.5">{{ $contact->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="mailto:{{ $contact->email }}?subject=Re: {{ rawurlencode($contact->subject) }}" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-indigo-500/20">
                Trả lời
            </a>
            <form action="{{ route('admin.contacts.toggle-read', $contact) }}" method="POST">
                @csrf
                <button class="text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors px-4 py-2 rounded-xl border border-slate-200">{{ $contact->is_read ? 'Đánh dấu chưa đọc' : 'Đánh dấu đã đọc' }}</button>
            </form>
        </div>
    </div>
    <div class="px-6 py-6">
        <div class="prose prose-sm max-w-none whitespace-pre-line text-slate-700 leading-relaxed">{{ $contact->message }}</div>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/60 flex justify-between items-center">
        <a href="{{ route('admin.contacts.index') }}" class="text-sm text-slate-500 hover:text-indigo-600">← Quay lại danh sách</a>
        <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Xóa liên hệ này?')">
            @csrf @method('DELETE')
            <button class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 text-sm font-bold rounded-xl">Xóa</button>
        </form>
    </div>
</div>
@endsection