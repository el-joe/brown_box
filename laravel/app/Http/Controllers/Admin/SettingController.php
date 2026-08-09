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
    private const TRANSLATABLE = ['site_name', 'site_tagline', 'address'];

    private const GENERAL_KEYS = [
        'default_language', 'default_currency', 'contact_email', 'contact_phone', 'contact_whatsapp',
        'social_facebook', 'social_instagram', 'social_tiktok', 'social_x', 'social_youtube',
        'google_analytics_id', 'google_tag_manager_id', 'meta_pixel_id',
    ];

    private const MAIL_KEYS = [
        'mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
        'mail_from_address', 'mail_from_name',
    ];

    public function general(Request $request): View
    {
        return view('admin.settings.general', [
            'activeTab' => $request->query('tab', 'general'),
        ]);
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
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_x' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:50'],
            'meta_pixel_id' => ['nullable', 'string', 'max:50'],
        ]);

        foreach (self::TRANSLATABLE as $field) {
            foreach (['ar', 'en'] as $locale) {
                $this->save("{$field}_{$locale}", $data[$field][$locale] ?? null, 'general');
            }
        }

        foreach (self::GENERAL_KEYS as $key) {
            $this->save($key, $data[$key] ?? null, 'general');
        }

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
