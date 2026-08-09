@extends('admin.layouts.app')

@section('title', __('Settings'))

@section('breadcrumb')
    <span>{{ __('Settings') }}</span>
@endsection

@section('content')
<div x-data="{ activeTab: '{{ $activeTab }}' }">
    <div class="flex items-center gap-1 border-b border-slate-200 mb-6 bg-white rounded-t-xl px-2 pt-2">
        <button type="button" @click="activeTab = 'general'" class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors" :class="activeTab === 'general' ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-700'">{{ __('General') }}</button>
        <button type="button" @click="activeTab = 'mail'" class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors" :class="activeTab === 'mail' ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-700'">{{ __('Mail') }}</button>
        <button type="button" @click="activeTab = 'advanced'" class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors" :class="activeTab === 'advanced' ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-700'">{{ __('Advanced') }}</button>
    </div>

    {{-- General --}}
    <div x-show="activeTab === 'general'" x-cloak>
        <form method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <x-admin.card :title="__('Site Identity')">
                        <x-admin.lang-tabs>
                            <x-slot:en>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Site Name (EN)') }}</label>
                                    <input type="text" name="site_name[en]" value="{{ old('site_name.en', setting('site_name_en')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                    @error('site_name.en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Site Tagline (EN)') }}</label>
                                    <input type="text" name="site_tagline[en]" value="{{ old('site_tagline.en', setting('site_tagline_en')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                            </x-slot:en>
                            <x-slot:ar>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Site Name (AR)') }}</label>
                                    <input type="text" name="site_name[ar]" value="{{ old('site_name.ar', setting('site_name_ar')) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                                    @error('site_name.ar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="admin-field mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Site Tagline (AR)') }}</label>
                                    <input type="text" name="site_tagline[ar]" value="{{ old('site_tagline.ar', setting('site_tagline_ar')) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                                </div>
                            </x-slot:ar>
                        </x-admin.lang-tabs>
                    </x-admin.card>

                    <x-admin.card :title="__('Contact Information')">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Contact Email') }}</label>
                                <input type="email" name="contact_email" value="{{ old('contact_email', setting('contact_email')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                @error('contact_email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Phone') }}</label>
                                <input type="text" name="contact_phone" value="{{ old('contact_phone', setting('contact_phone')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('WhatsApp') }}</label>
                                <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', setting('contact_whatsapp')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                        </div>

                        <x-admin.lang-tabs class="mt-4">
                            <x-slot:en>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Address (EN)') }}</label>
                                    <textarea name="address[en]" rows="2" class="w-full rounded-lg border-slate-300 text-sm">{{ old('address.en', setting('address_en')) }}</textarea>
                                </div>
                            </x-slot:en>
                            <x-slot:ar>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Address (AR)') }}</label>
                                    <textarea name="address[ar]" rows="2" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('address.ar', setting('address_ar')) }}</textarea>
                                </div>
                            </x-slot:ar>
                        </x-admin.lang-tabs>
                    </x-admin.card>

                    <x-admin.card :title="__('Social Links')">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Facebook') }}</label>
                                <input type="url" name="social_facebook" value="{{ old('social_facebook', setting('social_facebook')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Instagram') }}</label>
                                <input type="url" name="social_instagram" value="{{ old('social_instagram', setting('social_instagram')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('TikTok') }}</label>
                                <input type="url" name="social_tiktok" value="{{ old('social_tiktok', setting('social_tiktok')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('X (Twitter)') }}</label>
                                <input type="url" name="social_x" value="{{ old('social_x', setting('social_x')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('YouTube') }}</label>
                                <input type="url" name="social_youtube" value="{{ old('social_youtube', setting('social_youtube')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                        </div>
                    </x-admin.card>

                    <x-admin.card :title="__('Tracking & Analytics')">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Google Analytics ID') }}</label>
                                <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', setting('google_analytics_id')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Google Tag Manager ID') }}</label>
                                <input type="text" name="google_tag_manager_id" value="{{ old('google_tag_manager_id', setting('google_tag_manager_id')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Pixel ID') }}</label>
                                <input type="text" name="meta_pixel_id" value="{{ old('meta_pixel_id', setting('meta_pixel_id')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                        </div>
                    </x-admin.card>
                </div>

                <div class="space-y-6">
                    <x-admin.card :title="__('Logo & Favicon')">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Site Logo') }}</label>
                                <x-admin.image-upload name="site_logo" :current="setting('site_logo')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Favicon') }}</label>
                                <x-admin.image-upload name="favicon" :current="setting('favicon')" />
                            </div>
                        </div>
                    </x-admin.card>

                    <x-admin.card :title="__('Localization')">
                        <div class="space-y-4">
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Default Language') }}</label>
                                <select name="default_language" class="w-full rounded-lg border-slate-300 text-sm">
                                    <option value="ar" @selected(old('default_language', setting('default_language', 'ar')) === 'ar')>{{ __('Arabic') }}</option>
                                    <option value="en" @selected(old('default_language', setting('default_language', 'ar')) === 'en')>{{ __('English') }}</option>
                                </select>
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Default Currency') }}</label>
                                <input type="text" name="default_currency" value="{{ old('default_currency', setting('default_currency', 'EGP')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                        </div>
                    </x-admin.card>

                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                        {{ __('Save General Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Mail --}}
    <div x-show="activeTab === 'mail'" x-cloak>
        <form method="POST" action="{{ route('admin.settings.mail.update') }}" x-data="mailSettings()">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <x-admin.card :title="__('Mail Configuration')">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">MAIL_DRIVER</label>
                                <select name="mail_driver" x-model="fields.mail_driver" class="w-full rounded-lg border-slate-300 text-sm">
                                    @foreach (['smtp' => 'SMTP', 'log' => 'Log', 'sendmail' => 'Sendmail'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('mail_driver', setting('mail_driver', 'smtp')) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">MAIL_HOST</label>
                                <input type="text" name="mail_host" x-model="fields.mail_host" value="{{ old('mail_host', setting('mail_host')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">MAIL_PORT</label>
                                <input type="number" name="mail_port" x-model="fields.mail_port" value="{{ old('mail_port', setting('mail_port')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">MAIL_USERNAME</label>
                                <input type="text" name="mail_username" x-model="fields.mail_username" value="{{ old('mail_username', setting('mail_username')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">MAIL_PASSWORD</label>
                                <input type="password" name="mail_password" x-model="fields.mail_password" value="{{ old('mail_password', setting('mail_password')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">MAIL_FROM_ADDRESS</label>
                                <input type="email" name="mail_from_address" x-model="fields.mail_from_address" value="{{ old('mail_from_address', setting('mail_from_address')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                @error('mail_from_address')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">MAIL_FROM_NAME</label>
                                <input type="text" name="mail_from_name" x-model="fields.mail_from_name" value="{{ old('mail_from_name', setting('mail_from_name')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                @error('mail_from_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </x-admin.card>

                    <x-admin.card :title="__('Send Test Email')">
                        <div class="flex flex-wrap items-end gap-3">
                            <div class="admin-field flex-1 min-w-[220px]">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Send To') }}</label>
                                <input type="email" x-model="testEmail" class="w-full rounded-lg border-slate-300 text-sm" placeholder="{{ __('you@example.com') }}">
                            </div>
                            <button type="button" @click="sendTest()" :disabled="sending" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-50">
                                <span x-show="!sending">{{ __('Send Test Email') }}</span>
                                <span x-show="sending" x-cloak>{{ __('Sending…') }}</span>
                            </button>
                        </div>
                        <p class="mt-3 text-sm" :class="resultOk ? 'text-emerald-600' : 'text-red-600'" x-show="resultMessage" x-text="resultMessage"></p>
                    </x-admin.card>
                </div>

                <div class="space-y-6">
                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                        {{ __('Save Mail Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Advanced --}}
    <div x-show="activeTab === 'advanced'" x-cloak>
        <form method="POST" action="{{ route('admin.settings.advanced.update') }}">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <x-admin.card :title="__('Advanced Settings')">
                        <div class="space-y-4">
                            <x-admin.checkbox name="maintenance_mode" :checked="old('maintenance_mode', setting('maintenance_mode', '0') === '1')" :label="__('Maintenance Mode')" />

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Commission Hold Days') }}</label>
                                    <input type="number" min="0" name="commission_hold_days" value="{{ old('commission_hold_days', setting('commission_hold_days', 14)) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Max Product Images') }}</label>
                                    <input type="number" min="1" name="max_product_images" value="{{ old('max_product_images', setting('max_product_images', 8)) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Low Stock Threshold Default') }}</label>
                                    <input type="number" min="0" name="low_stock_threshold_default" value="{{ old('low_stock_threshold_default', setting('low_stock_threshold_default', 5)) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                            </div>
                        </div>
                    </x-admin.card>
                </div>

                <div class="space-y-6">
                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                        {{ __('Save Advanced Settings') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function mailSettings() {
            return {
                fields: {
                    mail_driver: @js(old('mail_driver', setting('mail_driver', 'smtp'))),
                    mail_host: @js(old('mail_host', setting('mail_host'))),
                    mail_port: @js(old('mail_port', setting('mail_port'))),
                    mail_username: @js(old('mail_username', setting('mail_username'))),
                    mail_password: @js(old('mail_password', setting('mail_password'))),
                    mail_from_address: @js(old('mail_from_address', setting('mail_from_address'))),
                    mail_from_name: @js(old('mail_from_name', setting('mail_from_name'))),
                },
                testEmail: @js(auth('admin')->user()?->email),
                sending: false,
                resultOk: false,
                resultMessage: '',

                async sendTest() {
                    this.sending = true;
                    this.resultMessage = '';

                    const response = await fetch(@js(route('admin.settings.test-mail')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({ ...this.fields, test_email: this.testEmail }),
                    });

                    const data = await response.json();
                    this.sending = false;
                    this.resultOk = !!data.success;
                    this.resultMessage = data.message || (data.errors ? Object.values(data.errors)[0][0] : @js(__('Something went wrong.')));
                },
            };
        }
    </script>
@endpush
