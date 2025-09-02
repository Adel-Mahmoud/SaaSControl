<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    public static function get($key)
    {
        $settings = Setting::first();
        return $settings ? $settings->$key : null;
    }
}
