<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class NotificationChannelService
{
    // Channels the app actually supports sending right now.
    // 'whatsapp' is planned — listed in UI but never returned until implemented.
    private const SUPPORTED = ['database', 'mail'];

    private array $config;

    public function __construct()
    {
        $this->config = Cache::remember('setting.notification_channels', 3600, function () {
            $raw = Setting::query()->where('key', 'notification_channels')->value('value');

            return $raw ? json_decode($raw, true) : [];
        });
    }

    /**
     * Return active Laravel notification channels for a given event key.
     *
     * @param  string  $audience  'customer' or 'admin'
     * @param  string  $event     e.g. 'order_placed', 'new_order'
     * @param  array  $fallback  Channels to use if setting is missing
     */
    public function channels(string $audience, string $event, array $fallback = ['database', 'mail']): array
    {
        $channels = $this->config[$audience][$event] ?? null;

        if ($channels === null) {
            return $fallback;
        }

        return array_values(
            array_filter(
                array_keys(array_filter($channels)),
                fn (string $ch) => in_array($ch, self::SUPPORTED, true)
            )
        );
    }

    /**
     * Flush the cached config (call after saving settings).
     */
    public static function flush(): void
    {
        Cache::forget('setting.notification_channels');
    }
}
