<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];
    public $timestamps = true;

    public static function booted()
    {
        static::saved(function () { Cache::forget('settings_all'); });
        static::deleted(function () { Cache::forget('settings_all'); });
    }
}
