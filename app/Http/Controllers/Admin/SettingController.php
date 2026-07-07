<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = $this->loadGrouped();
        return view('admin.settings.index', ['settings' => $settings, 'tab' => request('tab', 'general')]);
    }

    public function update(Request $request)
    {
        $allowed = [
            'general' => ['site_name', 'site_tagline', 'contact_email', 'site_logo', 'social_facebook', 'social_linkedin', 'social_youtube', 'social_tiktok'],
            'email'   => ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'],
            'payment' => ['vnpay_enabled', 'vnpay_tmn_code', 'vnpay_hash_secret', 'momo_enabled', 'momo_partner_code', 'momo_access_key', 'momo_secret_key'],
        ];

        $currentTab = $request->input('tab', 'general');
        $fields = $allowed[$currentTab] ?? [];

        foreach ($fields as $key) {
            $value = $request->input($key);

            if ($key === 'site_logo' && $request->hasFile('site_logo')) {
                $value = $request->file('site_logo')->store('settings', 'public');
                if ($old = setting('site_logo')) {
                    Storage::disk('public')->delete($old);
                }
            } elseif (in_array($key, ['vnpay_enabled', 'momo_enabled'])) {
                $value = $request->boolean($key);
            } elseif (in_array($key, ['mail_password', 'vnpay_hash_secret', 'momo_secret_key']) && $value === '__unchanged__') {
                continue;
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $currentTab]);
        }

        Cache::forget('settings_all');
        return redirect()->route('admin.settings.index', ['tab' => $currentTab])->with('success', 'Đã lưu cài đặt.');
    }

    private function loadGrouped(): array
    {
        return Cache::rememberForever('settings_all', function () {
            return Setting::all()->keyBy('key')->map(fn ($s) => $s->value)->toArray();
        });
    }
}
