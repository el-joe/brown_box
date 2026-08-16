@extends('website.layouts.app')

@section('title', __('website.my_profile'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.my_profile'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="web-account-layout">
            @include('website.account._sidebar', ['customer' => $customer, 'active' => 'profile'])

            <div class="min-w-0">
                {{-- Personal info --}}
                <div class="web-account-card">
                    <div class="web-account-card-head">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ __('website.personal_information') }}</h2>
                            <p class="web-account-card-sub">{{ __('website.personal_information_desc') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-5 mb-7">
                        <div class="web-account-avatar !w-16 !h-16 !text-2xl">
                            {{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-slate-900">{{ __('website.full_name') }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $customer->email }}</p>
                        </div>
                    </div>

                    <form id="profile-form" method="POST" action="{{ route('web.account.profile.update', ['lang' => current_lang()]) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        @method('PUT')

                        <div class="web-checkout-field sm:col-span-2">
                            <label for="profile-name">{{ __('website.full_name') }}</label>
                            <input id="profile-name" name="name" type="text" value="{{ old('name', $customer->name) }}" autocomplete="name" required>
                            @error('name')
                                <p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="web-checkout-field">
                            <label for="profile-email">{{ __('website.email_address') }}</label>
                            <input id="profile-email" type="email" value="{{ $customer->email }}" disabled class="bg-slate-50 text-slate-400">
                        </div>

                        <div class="web-checkout-field">
                            <label for="profile-phone">{{ __('website.phone_number') }}</label>
                            <input id="profile-phone" name="phone" type="tel" value="{{ old('phone', $customer->phone) }}" autocomplete="tel">
                            @error('phone')
                                <p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 flex justify-end">
                            <button id="profile-submit-btn" type="submit" class="web-btn-primary px-8">
                                <i class="fa-solid fa-check"></i> {{ __('website.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Change password --}}
                <div class="web-account-card">
                    <div class="web-account-card-head">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ __('website.change_password') }}</h2>
                            <p class="web-account-card-sub">{{ __('website.change_password_desc') }}</p>
                        </div>
                    </div>

                    <form id="password-form" method="POST" action="{{ route('web.account.profile.update', ['lang' => current_lang()]) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $customer->name }}">
                        <input type="hidden" name="phone" value="{{ $customer->phone }}">

                        <div class="web-checkout-field sm:col-span-2">
                            <label for="current-password">{{ __('website.current_password') }}</label>
                            <div class="web-input-wrap">
                                <input id="current-password" name="current_password" type="password" class="has-toggle" autocomplete="current-password">
                                <button type="button" class="web-toggle-password" data-target="current-password" aria-label="{{ __('website.current_password') }}"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            @error('current_password')
                                <p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="web-checkout-field">
                            <label for="new-password">{{ __('website.new_password') }}</label>
                            <div class="web-input-wrap">
                                <input id="new-password" name="password" type="password" class="has-toggle" autocomplete="new-password">
                                <button type="button" class="web-toggle-password" data-target="new-password" aria-label="{{ __('website.new_password') }}"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            <div class="web-pw-strength-track mt-2">
                                <div id="pw-strength-bar" class="web-pw-strength-bar"></div>
                            </div>
                            <p id="pw-strength-label" class="web-pw-strength-label"></p>
                            @error('password')
                                <p class="web-field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="web-checkout-field">
                            <label for="confirm-password">{{ __('website.confirm_new_password') }}</label>
                            <div class="web-input-wrap">
                                <input id="confirm-password" name="password_confirmation" type="password" class="has-toggle" autocomplete="new-password">
                                <button type="button" class="web-toggle-password" data-target="confirm-password" aria-label="{{ __('website.confirm_new_password') }}"><i class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>

                        <div class="sm:col-span-2 flex justify-end">
                            <button id="password-submit-btn" type="submit" class="web-btn-outline px-8">
                                <i class="fa-solid fa-lock"></i> {{ __('website.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div id="web-account-toast" class="web-toast">
        <i class="fa-solid fa-circle-check"></i>
        <span></span>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/account.js'])
    <script>
        (function () {
            document.querySelectorAll('.web-toggle-password').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var target = document.getElementById(btn.dataset.target);
                    if (!target) return;
                    var icon = btn.querySelector('i');
                    if (target.type === 'password') {
                        target.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        target.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            var newPassword = document.getElementById('new-password');
            var strengthBar = document.getElementById('pw-strength-bar');
            var strengthLabel = document.getElementById('pw-strength-label');

            if (newPassword && strengthBar && strengthLabel) {
                newPassword.addEventListener('input', function () {
                    var value = newPassword.value;
                    var score = 0;

                    if (value.length >= 8) score++;
                    if (/[A-Z]/.test(value)) score++;
                    if (/[0-9]/.test(value)) score++;
                    if (/[^A-Za-z0-9]/.test(value)) score++;

                    var levels = [
                        { width: '0%', color: '', label: '' },
                        { width: '25%', color: 'bg-red-500', label: '{{ __('website.password_min_chars') }}' },
                        { width: '50%', color: 'bg-orange-500', label: 'Weak' },
                        { width: '75%', color: 'bg-yellow-500', label: 'Medium' },
                        { width: '100%', color: 'bg-green-500', label: 'Strong' },
                    ];

                    var level = value.length === 0 ? levels[0] : levels[score] || levels[1];

                    strengthBar.style.width = level.width;
                    strengthBar.className = 'web-pw-strength-bar ' + level.color;
                    strengthLabel.textContent = level.label;
                });
            }
        })();
    </script>
@endpush
