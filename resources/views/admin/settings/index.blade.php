@extends('layouts.admin')
@section('title', 'Cài đặt hệ thống')
@section('page-title', 'Cài đặt')

@section('breadcrumb')
<span class="text-slate-900 font-bold">Cài đặt</span>
@endsection

@php
$tabs = [
    'general'  => ['label' => 'Chung',       'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37'],
    'email'    => ['label' => 'Email',       'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8'],
    'payment'  => ['label' => 'Thanh toán',  'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
];
@endphp

@section('content')

{{-- Tabs --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm mb-5">
    <div class="flex border-b border-slate-100">
        @foreach($tabs as $key => $tab)
        <a href="{{ route('admin.settings.index', ['tab' => $key]) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold transition border-b-2 {{ (request('tab', 'general')) === $key ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
            {{ $tab['label'] }}
        </a>
        @endforeach
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <input type="hidden" name="tab" value="{{ request('tab', 'general') }}">

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">

        @if(request('tab', 'general') === 'general')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tên site</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'CVactive') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition placeholder-slate-400">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Email liên hệ</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition placeholder-slate-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Tagline</label>
                <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition placeholder-slate-400">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Logo</label>
                @if(!empty($settings['site_logo']))
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$settings['site_logo']) }}" class="h-12 rounded-xl object-contain bg-slate-100 p-1">
                </div>
                @endif
                <input type="file" name="site_logo" accept="image/*"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm cursor-pointer file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Mạng xã hội</label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Facebook URL</label>
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">LinkedIn URL</label>
                        <input type="url" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">YouTube URL</label>
                        <input type="url" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition placeholder-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">TikTok URL</label>
                        <input type="url" name="social_tiktok" value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition placeholder-slate-400">
                    </div>
                </div>
            </div>

        @elseif(request('tab') === 'email')
            <div class="flex items-start gap-3 p-3.5 bg-amber-50 border border-amber-200 rounded-xl">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-xs text-amber-800">Các thông tin SMTP này sẽ ghi đè cấu hình trong file <code class="font-mono bg-amber-100 px-1 rounded">.env</code>. Để trống password nếu không muốn thay đổi.</p>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Port</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Password</label>
                    <input type="password" name="mail_password" placeholder="••••••••"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Encryption</label>
                    <select name="mail_encryption" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                        <option value="">None</option>
                        <option value="tls" {{ ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">From Name</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">From Address</label>
                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
            </div>
        @elseif(request('tab') === 'payment')
            <div class="border border-slate-200 rounded-2xl p-5">
                <label class="flex items-center justify-between cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                            <span class="text-xs font-extrabold text-blue-600">VN</span>
                        </div>
                        <span class="font-bold text-slate-900">VNPay</span>
                    </div>
                    <input type="checkbox" name="vnpay_enabled" value="1" {{ old('vnpay_enabled', $settings['vnpay_enabled'] ?? false) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-5 w-5">
                </label>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <input type="text" name="vnpay_tmn_code" placeholder="TMN Code" value="{{ old('vnpay_tmn_code', $settings['vnpay_tmn_code'] ?? '') }}"
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                    <input type="password" name="vnpay_hash_secret" placeholder="Hash Secret" value="{{ old('vnpay_hash_secret', $settings['vnpay_hash_secret'] ?? '') }}"
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                </div>
            </div>
            <div class="border border-slate-200 rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center">
                            <span class="text-xs font-extrabold text-pink-600">Mo</span>
                        </div>
                        <span class="font-bold text-slate-900">MoMo</span>
                    </div>
                    <input type="checkbox" name="momo_enabled" value="1" {{ old('momo_enabled', $settings['momo_enabled'] ?? false) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-5 w-5">
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <input type="text" name="momo_partner_code" placeholder="Partner Code" value="{{ old('momo_partner_code', $settings['momo_partner_code'] ?? '') }}"
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                    <input type="text" name="momo_access_key" placeholder="Access Key" value="{{ old('momo_access_key', $settings['momo_access_key'] ?? '') }}"
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                    <input type="password" name="momo_secret_key" placeholder="Secret Key" value="{{ old('momo_secret_key', $settings['momo_secret_key'] ?? '') }}"
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition">
                </div>
            </div>
        @endif

        <div class="flex justify-end pt-4 border-t border-slate-100">
            <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20">Lưu cài đặt</button>
        </div>
    </div>
</form>

@endsection
