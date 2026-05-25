<?php

use App\Models\SiteSetting;

if (! function_exists('setting')) {
    /**
     * Site-wide setting accessor.
     * Usage in Blade: {{ setting('site.name', 'GEMDTEK') }}
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }
}
