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

                    <form id="profile-form" method="POST" action="{{ route('web.account.profile.update', ['lang' => current_lang()]) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        @method('PUT')

                        <div class="web-checkout-field sm:col-span-2">
                            <label for="profile-name">{{ __('website.full_name') }}</label>
                            <input id="profile-name" name="name" type="text" value="{{ old('name', $customer->name) }}" autocomplete="name" required>
                            <p id="profile-name-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i></p>
                        </div>

                        <div class="web-checkout-field">
                            <label for="profile-email">{{ __('website.email_address') }}</label>
                            <input id="profile-email" type="email" value="{{ $customer->email }}" disabled class="bg-slate-50 text-slate-400">
                        </div>

                        <div class="web-checkout-field">
                            <label for="profile-phone">{{ __('website.phone_number') }}</label>
                            <input id="profile-phone" name="phone" type="tel" value="{{ old('phone', $customer->phone) }}" autocomplete="tel">
                        </div>

                        <div class="sm:col-span-2 flex justify-end">
                            <button id="profile-submit-btn" type="submit" class="web-btn-primary px-8">{{ __('website.save_changes') }}</button>
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
                            <label for="new-password">{{ __('website.new_password') }}</label>
                            <div class="web-input-wrap">
                                <input id="new-password" name="password" type="password" class="has-toggle" autocomplete="new-password">
                                <button type="button" class="web-toggle-password" data-target="new-password" aria-label="{{ __('website.new_password') }}"><i class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>

                        <div class="web-checkout-field sm:col-span-2">
                            <label for="confirm-password">{{ __('website.confirm_new_password') }}</label>
                            <div class="web-input-wrap">
                                <input id="confirm-password" name="password_confirmation" type="password" class="has-toggle" autocomplete="new-password">
                                <button type="button" class="web-toggle-password" data-target="confirm-password" aria-label="{{ __('website.confirm_new_password') }}"><i class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>

                        <div class="sm:col-span-2 flex justify-end">
                            <button id="password-submit-btn" type="submit" class="web-btn-outline px-8">{{ __('website.save_changes') }}</button>
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
@endpush
