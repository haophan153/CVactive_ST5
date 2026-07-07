<?php

if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        $all = \Illuminate\Support\Facades\Cache::rememberForever('settings_all', function () {
            return \App\Models\Setting::all()->keyBy('key')->map(fn ($s) => $s->value)->toArray();
        });
        return $all[$key] ?? $default;
    }
}
