@extends('layouts.admin')
@section('title', 'Cài đặt hệ thống')
@section('page-title', 'Cài đặt')

@section('breadcrumb')
<span class="text-gray-900 font-semibold">Cài đặt</span>
@endsection

@php
$tabs = [
    'general' => ['label' => 'Chung', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37'],
    'email'   => ['label' => 'Email', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8'],
    'payment' => ['label' => 'Thanh toán', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
];
@endphp

@section('content')

{{-- Tabs --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-5">
    <div class="flex border-b border-gray-100">
        @foreach($tabs as $key => $tab)
        <a href="{{ route('admin.settings.index', ['tab' => $key]) }}"
           class="flex items-center gap-2 px-5 py-3 text-sm font-medium transition border-b-2 {{ (request('tab', 'general')) === $key ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
            {{ $tab['label'] }}
        </a>
        @endforeach
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <input type="hidden" name="tab" value="{{ request('tab', 'general') }}">

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">

        @if(request('tab', 'general') === 'general')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên site</label>
                <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'CVactive') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email liên hệ</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                @if(!empty($settings['site_logo']))
                <img src="{{ asset('storage/'.$settings['site_logo']) }}" class="h-12 mb-2 rounded object-contain">
                @endif
                <input type="file" name="site_logo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                    <input type="url" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                    <input type="url" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">YouTube URL</label>
                    <input type="url" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">TikTok URL</label>
                    <input type="url" name="social_tiktok" value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
            </div>

        @elseif(request('tab') === 'email')
            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">
                <strong>Lưu ý:</strong> Các thông tin SMTP này sẽ ghi đè cấu hình trong file <code>.env</code>. Để trống password nếu không muốn thay đổi.
            </p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="mail_password" placeholder="••••••••" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                    <select name="mail_encryption" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                        <option value="">None</option>
                        <option value="tls" {{ ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm">
            </div>

        @elseif(request('tab') === 'payment')
            <div class="border border-gray-200 rounded-xl p-4">
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="font-semibold text-gray-900">VNPay</span>
                    <input type="checkbox" name="vnpay_enabled" value="1" {{ old('vnpay_enabled', $settings['vnpay_enabled'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </label>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <input type="text" name="vnpay_tmn_code" placeholder="TMN Code" value="{{ old('vnpay_tmn_code', $settings['vnpay_tmn_code'] ?? '') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <input type="password" name="vnpay_hash_secret" placeholder="Hash Secret" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="border border-gray-200 rounded-xl p-4">
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="font-semibold text-gray-900">MoMo</span>
                    <input type="checkbox" name="momo_enabled" value="1" {{ old('momo_enabled', $settings['momo_enabled'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </label>
                <div class="grid grid-cols-3 gap-3 mt-3">
                    <input type="text" name="momo_partner_code" placeholder="Partner Code" value="{{ old('momo_partner_code', $settings['momo_partner_code'] ?? '') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <input type="text" name="momo_access_key" placeholder="Access Key" value="{{ old('momo_access_key', $settings['momo_access_key'] ?? '') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <input type="password" name="momo_secret_key" placeholder="Secret Key" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        @endif

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">Lưu cài đặt</button>
        </div>
    </div>
</form>

@endsection
