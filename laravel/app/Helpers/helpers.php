<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

if (! function_exists('money_format')) {
    /**
     * Format a numeric amount as currency.
     */
    function money_format(float|int $amount, ?string $currency = null): string
    {
        $currency = $currency ?? setting('currency', 'EGP');

        return number_format((float) $amount, 2).' '.$currency;
    }
}

if (! function_exists('setting')) {
    /**
     * Fetch an application setting, falling back to config/default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('settings.all', 3600, function () {
            return \App\Models\Setting::query()->pluck('value', 'key');
        });

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('current_lang')) {
    /**
     * Get the current application locale.
     */
    function current_lang(): string
    {
        return App::getLocale();
    }
}

if (! function_exists('asset_url')) {
    /**
     * Build a full URL for a stored asset path.
     */
    function asset_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}

if (! function_exists('generate_invoice_number')) {
    /**
     * Generate a unique invoice number, e.g. INV-20260808-00042.
     */
    function generate_invoice_number(): string
    {
        return 'INV-'.now()->format('Ymd').'-'.str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('generate_order_number')) {
    /**
     * Generate a unique order number, e.g. ORD-20260808-00042.
     */
    function generate_order_number(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('is_rtl')) {
    /**
     * Whether the current locale reads right-to-left.
     */
    function is_rtl(): bool
    {
        return App::getLocale() === 'ar';
    }
}

if (! function_exists('affiliate_code')) {
    /**
     * Return the "?ref=CODE" query string for the affiliate stored in
     * session, or an empty string when there is none.
     */
    function affiliate_code(): string
    {
        $code = session('affiliate_ref_code');

        return $code ? '?ref='.$code : '';
    }
}
