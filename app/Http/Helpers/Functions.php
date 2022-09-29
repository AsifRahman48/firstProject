<?php


if (! function_exists('setting')) {
    function setting(string $key)
    {
        $setting = \App\Models\V2\Setting::getValue($key);
        if ($setting) {
            return  $setting;
        }
    }
}
if (! function_exists('storeSetting')) {
    function storeSetting(string $key, $value)
    {
        $setting = \App\Models\V2\Setting::storeValue($key, $value);
        if ($setting) {
            return  $setting;
        }
    }
}
