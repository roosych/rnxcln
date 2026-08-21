<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a DB-backed setting by "group.key" (e.g. `setting('site.phone')`).
     */
    function setting(string $groupDotKey, mixed $default = null): mixed
    {
        [$group, $key] = explode('.', $groupDotKey, 2);

        return Setting::get($group, $key, $default);
    }
}

if (! function_exists('logo_url')) {
    /**
     * Resolve a branding image: the file uploaded through Settings → Company
     * (stored on the public disk) if there is one, otherwise the theme's
     * built-in default from config/site.php.
     */
    function logo_url(string $variant): string
    {
        $uploaded = Setting::get('site', "logo_{$variant}");

        return $uploaded ? asset('storage/'.$uploaded) : asset(config("site.logo.{$variant}"));
    }
}
