<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class SettingController extends Controller
{
    private const TRANSLATABLE = [
        'site_name',
        'site_tagline',
        'address',
        'announcement_text',
        'contact_address',
        'contact_hours',
        'footer_about',
        'hero_banner_title',
        'hero_banner_subtitle',
    ];

    private const GENERAL_KEYS = [
        'default_language', 'default_currency', 'contact_email', 'contact_phone', 'contact_whatsapp',
        'social_facebook', 'social_instagram', 'social_tiktok', 'social_x', 'social_youtube',
        'google_analytics_id', 'google_tag_manager_id', 'meta_pixel_id',
        'google_search_console_verification', 'notification_email', 'tax_rate',
        'contact_whatsapp_link', 'google_maps_embed_url',
    ];

    private const MAIL_KEYS = [
        'mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
        'mail_from_address', 'mail_from_name',
    ];

    public function general(Request $request): View
    {
        $channels = json_decode(
            Setting::query()->where('key', 'notification_channels')->value('value') ?? '{}',
            true
        );

        return view('admin.settings.general', [
            'activeTab' => $request->query('tab', 'general'),
            'channels' => $channels,
        ]);
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $input = $request->input('channels', []);

        $config = [];
        foreach ($input as $audience => $events) {
            foreach ($events as $event => $chans) {
                foreach (['database', 'mail', 'whatsapp'] as $ch) {
                    $config[$audience][$event][$ch] = isset($chans[$ch]);
                }
            }
        }

        $this->save('notification_channels', json_encode($config), 'notifications', 'json');

        Cache::forget('settings.all');
        \App\Services\NotificationChannelService::flush();

        return redirect()->route('admin.settings.general', ['tab' => 'notifications'])->with('success', __('Notification settings saved.'));
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name.ar' => ['required', 'string', 'max:150'],
            'site_name.en' => ['required', 'string', 'max:150'],
            'site_tagline.ar' => ['nullable', 'string', 'max:255'],
            'site_tagline.en' => ['nullable', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'max:1024'],
            'favicon' => ['nullable', 'image', 'max:512'],
            'default_language' => ['required', Rule::in(['ar', 'en'])],
            'default_currency' => ['required', 'string', 'max:10'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'address.ar' => ['nullable', 'string', 'max:500'],
            'address.en' => ['nullable', 'string', 'max:500'],
            'announcement_enabled' => ['boolean'],
            'hero_banner_enabled' => ['boolean'],
            'announcement_text.ar' => ['nullable', 'string', 'max:2000'],
            'announcement_text.en' => ['nullable', 'string', 'max:2000'],
            'contact_address.ar' => ['nullable', 'string', 'max:500'],
            'contact_address.en' => ['nullable', 'string', 'max:500'],
            'contact_hours.ar' => ['nullable', 'string', 'max:500'],
            'contact_hours.en' => ['nullable', 'string', 'max:500'],
            'footer_about.ar' => ['nullable', 'string', 'max:500'],
            'footer_about.en' => ['nullable', 'string', 'max:500'],
            'hero_banner_title.ar' => ['nullable', 'string', 'max:255'],
            'hero_banner_title.en' => ['nullable', 'string', 'max:255'],
            'hero_banner_subtitle.ar' => ['nullable', 'string', 'max:255'],
            'hero_banner_subtitle.en' => ['nullable', 'string', 'max:255'],
            'contact_whatsapp_link' => ['nullable', 'url', 'max:500'],
            'google_maps_embed_url' => ['nullable', 'string', 'max:1000'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_x' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:50'],
            'meta_pixel_id' => ['nullable', 'string', 'max:50'],
            'google_search_console_verification' => ['nullable', 'string', 'max:150'],
            'notification_email' => ['nullable', 'email', 'max:150'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach (self::TRANSLATABLE as $field) {
            foreach (['ar', 'en'] as $locale) {
                $this->save("{$field}_{$locale}", $data[$field][$locale] ?? null, 'general');
            }
        }

        foreach (self::GENERAL_KEYS as $key) {
            $this->save($key, $data[$key] ?? null, 'general');
        }

        $this->save('announcement_enabled', $request->boolean('announcement_enabled') ? '1' : '0', 'general', 'boolean');
        $this->save('hero_banner_enabled', $request->boolean('hero_banner_enabled') ? '1' : '0', 'general', 'boolean');

        if ($request->hasFile('site_logo')) {
            $this->save('site_logo', $request->file('site_logo')->store('settings', 'public'), 'general', 'file');
        }

        if ($request->hasFile('favicon')) {
            $this->save('favicon', $request->file('favicon')->store('settings', 'public'), 'general', 'file');
        }

        Cache::forget('settings.all');

        return redirect()->route('admin.settings.general', ['tab' => 'general'])->with('success', __('Settings updated successfully.'));
    }

    public function updateMail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mail_driver' => ['required', 'string', 'max:20'],
            'mail_host' => ['nullable', 'string', 'max:150'],
            'mail_port' => ['nullable', 'integer'],
            'mail_username' => ['nullable', 'string', 'max:150'],
            'mail_password' => ['nullable', 'string', 'max:150'],
            'mail_from_address' => ['required', 'email', 'max:150'],
            'mail_from_name' => ['required', 'string', 'max:150'],
        ]);

        foreach (self::MAIL_KEYS as $key) {
            $this->save($key, $data[$key] ?? null, 'mail');
        }

        Cache::forget('settings.all');

        return redirect()->route('admin.settings.general', ['tab' => 'mail'])->with('success', __('Settings updated successfully.'));
    }

    public function testMail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mail_driver' => ['required', 'string', 'max:20'],
            'mail_host' => ['nullable', 'string', 'max:150'],
            'mail_port' => ['nullable', 'integer'],
            'mail_username' => ['nullable', 'string', 'max:150'],
            'mail_password' => ['nullable', 'string', 'max:150'],
            'mail_from_address' => ['required', 'email', 'max:150'],
            'mail_from_name' => ['required', 'string', 'max:150'],
            'test_email' => ['required', 'email'],
        ]);

        config([
            'mail.default' => $data['mail_driver'],
            'mail.mailers.smtp.host' => $data['mail_host'] ?? config('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port' => $data['mail_port'] ?? config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username' => $data['mail_username'] ?? config('mail.mailers.smtp.username'),
            'mail.mailers.smtp.password' => $data['mail_password'] ?? config('mail.mailers.smtp.password'),
            'mail.from.address' => $data['mail_from_address'],
            'mail.from.name' => $data['mail_from_name'],
        ]);

        try {
            Mail::raw(__('This is a test email from :name.', ['name' => $data['mail_from_name']]), function ($message) use ($data): void {
                $message->to($data['test_email'])->subject(__('Test Email'));
            });

            return response()->json(['success' => true, 'message' => __('Test email sent successfully.')]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function updateAdvanced(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'maintenance_mode' => ['boolean'],
            'commission_hold_days' => ['required', 'integer', 'min:0'],
            'max_product_images' => ['required', 'integer', 'min:1'],
            'low_stock_threshold_default' => ['required', 'integer', 'min:0'],
        ]);

        $this->save('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0', 'advanced', 'boolean');
        $this->save('commission_hold_days', $data['commission_hold_days'], 'advanced');
        $this->save('max_product_images', $data['max_product_images'], 'advanced');
        $this->save('low_stock_threshold_default', $data['low_stock_threshold_default'], 'advanced');

        Cache::forget('settings.all');

        return redirect()->route('admin.settings.general', ['tab' => 'advanced'])->with('success', __('Settings updated successfully.'));
    }

    private function save(string $key, mixed $value, string $group, string $type = 'text'): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type],
        );
    }
}
