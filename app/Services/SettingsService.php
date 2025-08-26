<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    public static function get($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
    
    public static function set($key, $value, $type = 'text', $group = 'general')
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }
    
    public static function getGroup($group)
    {
        return Setting::where('group', $group)->pluck('value', 'key');
    }
    
    public static function getImage($key)
    {
        $path = self::get($key);
        return $path ? asset('storage/' . $path) : null;
    }
}