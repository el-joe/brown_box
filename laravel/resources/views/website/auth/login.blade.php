@extends('website.layouts.app')

@section('title', __('website.sign_in'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.sign_in'), 'url' => null],
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
                    <h1 class="text-2xl font-extrabold text-slate-900">{{ __('website.sign_in') }}</h1>
                    <p class="text-sm text-slate-500 mt-1.5">
                        {{ __('website.dont_have_account') }}
                        <a href="{{ route('web.account.register', ['lang' => current_lang()]) }}" class="text-amber-600 font-semibold hover:underline">{{ __('website.create_one') }}</a>
                    </p>

                    <form id="login-form" method="POST" action="{{ route('web.account.login.store', ['lang' => current_lang(), 'redirect' => request('redirect')]) }}" class="mt-7">
                        @csrf

                        <div class="web-auth-field">
                            <label for="login-email">{{ __('website.email_address') }}</label>
                            <div class="web-input-wrap">
                                <i class="fa-regular fa-envelope web-input-icon"></i>
                                <input id="login-email" name="email" type="email" class="has-icon @error('email') is-invalid @enderror" placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email" required>
                            </div>
                            <p id="login-email-error" class="web-field-error {{ $errors->has('email') ? '' : 'hidden' }}"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('email') ?: __('website.valid_email_required') }}</p>
                        </div>

                        <div class="web-auth-field">
                            <label for="login-password">{{ __('website.password') }}</label>
                            <div class="web-input-wrap">
                                <i class="fa-solid fa-lock web-input-icon"></i>
                                <input id="login-password" name="password" type="password" class="has-icon" placeholder="{{ __('website.password') }}" autocomplete="current-password" required>
                                <button type="button" class="web-toggle-password" data-target="login-password" aria-label="{{ __('website.password') }}"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            <p id="login-password-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.password_required') }}</p>
                        </div>

                        <div class="flex items-center justify-between mb-6">
                            <label class="web-auth-checkbox">
                                <input type="checkbox" name="remember" value="1">
                                <span>{{ __('website.remember_me') }}</span>
                            </label>
                        </div>

                        <button id="login-submit-btn" type="submit" class="web-btn-primary w-full">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> <span>{{ __('website.sign_in') }}</span>
                        </button>
                    </form>

                    <p class="text-xs text-slate-400 text-center mt-8">
                        {!! __('website.auth_footer_note', [
                            'terms' => '<a href="'.route('web.pages.show', ['lang' => current_lang(), 'slug' => 'terms-conditions']).'" class="text-slate-600 hover:text-amber-600 underline">'.__('website.terms_conditions').'</a>',
                            'privacy' => '<a href="'.route('web.pages.show', ['lang' => current_lang(), 'slug' => 'privacy-policy']).'" class="text-slate-600 hover:text-amber-600 underline">'.__('website.privacy_policy').'</a>',
                        ]) !!}
                    </p>
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
