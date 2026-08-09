@extends('website.layouts.app')

@section('title', __('website.create_account'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.create_account'), 'url' => null],
        ]" />
    </div>

    <section class="web-auth-wrap max-w-6xl mx-auto px-4 py-8 sm:py-12 flex items-center">
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 rounded-3xl overflow-hidden shadow-xl border border-slate-100">
            {{-- Brand panel --}}
            <div class="web-auth-panel">
                <div>
                    <span class="text-2xl font-extrabold">{{ __('website.site_name') }}</span>
                    <h2 class="text-3xl font-extrabold leading-tight mt-10">{{ __('website.welcome_back_title') }}</h2>
                    <p class="text-sm text-white/75 mt-3 max-w-sm">{{ __('website.welcome_back_subtitle') }}</p>
                </div>

                <div class="space-y-5 mt-10">
                    <div class="web-auth-feature">
                        <span class="web-auth-feature-icon"><i class="fa-solid fa-truck-fast"></i></span>
                        <div>
                            <p class="font-semibold text-sm">{{ __('website.auth_feature_tracking_title') }}</p>
                            <p class="text-xs text-white/70 mt-0.5">{{ __('website.auth_feature_tracking_desc') }}</p>
                        </div>
                    </div>
                    <div class="web-auth-feature">
                        <span class="web-auth-feature-icon"><i class="fa-solid fa-tags"></i></span>
                        <div>
                            <p class="font-semibold text-sm">{{ __('website.auth_feature_deals_title') }}</p>
                            <p class="text-xs text-white/70 mt-0.5">{{ __('website.auth_feature_deals_desc') }}</p>
                        </div>
                    </div>
                    <div class="web-auth-feature">
                        <span class="web-auth-feature-icon"><i class="fa-solid fa-shield-halved"></i></span>
                        <div>
                            <p class="font-semibold text-sm">{{ __('website.auth_feature_secure_title') }}</p>
                            <p class="text-xs text-white/70 mt-0.5">{{ __('website.auth_feature_secure_desc') }}</p>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-white/60 mt-10">&copy; {{ now()->year }} {{ __('website.site_name') }}. {{ __('website.all_rights_reserved') }}</p>
            </div>

            {{-- Form panel --}}
            <div class="flex items-center justify-center bg-white p-6 sm:p-10">
                <div class="w-full max-w-sm">
                    <h1 class="text-2xl font-extrabold text-slate-900">{{ __('website.create_account') }}</h1>
                    <p class="text-sm text-slate-500 mt-1.5">
                        {{ __('website.already_have_account') }}
                        <a href="{{ route('web.account.login', ['lang' => current_lang()]) }}" class="text-amber-600 font-semibold hover:underline">{{ __('website.sign_in_link') }}</a>
                    </p>

                    <form id="register-form" method="POST" action="{{ route('web.account.register.store', ['lang' => current_lang()]) }}" class="mt-7">
                        @csrf

                        <div class="web-auth-field">
                            <label for="register-name">{{ __('website.full_name') }}</label>
                            <div class="web-input-wrap">
                                <i class="fa-regular fa-user web-input-icon"></i>
                                <input id="register-name" name="name" type="text" class="has-icon" value="{{ old('name') }}" autocomplete="name" required>
                            </div>
                            <p id="register-name-error" class="web-field-error {{ $errors->has('name') ? '' : 'hidden' }}"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('name') ?: __('website.name_required') }}</p>
                        </div>

                        <div class="web-auth-field">
                            <label for="register-email">{{ __('website.email_address') }}</label>
                            <div class="web-input-wrap">
                                <i class="fa-regular fa-envelope web-input-icon"></i>
                                <input id="register-email" name="email" type="email" class="has-icon" placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email" required>
                            </div>
                            <p id="register-email-error" class="web-field-error {{ $errors->has('email') ? '' : 'hidden' }}"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('email') ?: __('website.valid_email_required') }}</p>
                        </div>

                        <div class="web-auth-field">
                            <label for="register-phone">{{ __('website.phone_number') }}</label>
                            <div class="web-input-wrap">
                                <i class="fa-solid fa-phone web-input-icon"></i>
                                <input id="register-phone" name="phone" type="tel" class="has-icon" value="{{ old('phone') }}" autocomplete="tel">
                            </div>
                        </div>

                        <div class="web-auth-field">
                            <label for="register-password">{{ __('website.password') }}</label>
                            <div class="web-input-wrap">
                                <i class="fa-solid fa-lock web-input-icon"></i>
                                <input id="register-password" name="password" type="password" class="has-icon" autocomplete="new-password" required>
                                <button type="button" class="web-toggle-password" data-target="register-password" aria-label="{{ __('website.password') }}"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            <div class="web-pw-strength-track"><div id="pw-strength-bar" class="web-pw-strength-bar"></div></div>
                            <p id="pw-strength-label" class="web-pw-strength-label"></p>
                            <p id="register-password-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.password_min_chars') }}</p>
                        </div>

                        <div class="web-auth-field">
                            <label for="register-confirm">{{ __('website.confirm_password') }}</label>
                            <div class="web-input-wrap">
                                <i class="fa-solid fa-lock web-input-icon"></i>
                                <input id="register-confirm" name="password_confirmation" type="password" class="has-icon" autocomplete="new-password" required>
                                <button type="button" class="web-toggle-password" data-target="register-confirm" aria-label="{{ __('website.confirm_password') }}"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            <p id="register-confirm-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.passwords_not_match') }}</p>
                        </div>

                        <label class="web-auth-checkbox mb-2">
                            <input type="checkbox" id="register-terms" required>
                            <span>
                                {{ __('website.agree_terms_prefix') }}
                                <a href="{{ route('web.pages.show', ['lang' => current_lang(), 'slug' => 'terms-conditions']) }}" class="text-amber-600 hover:underline">{{ __('website.terms_conditions') }}</a>
                                {{ __('website.and') }}
                                <a href="{{ route('web.pages.show', ['lang' => current_lang(), 'slug' => 'privacy-policy']) }}" class="text-amber-600 hover:underline">{{ __('website.privacy_policy') }}</a>
                            </span>
                        </label>
                        <p id="register-terms-error" class="web-field-error hidden mb-3"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.terms_required') }}</p>

                        <button id="register-submit-btn" type="submit" class="web-btn-primary w-full mt-2">
                            <i class="fa-solid fa-user-plus"></i> <span>{{ __('website.create_account') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div id="web-auth-toast" class="web-toast">
        <i class="fa-solid fa-circle-check"></i>
        <span></span>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/auth.js'])
@endpush
