@extends('website.layouts.app')

@section('title', __('website.contact') . ' - ' . __('website.site_name'))

@push('styles')
    @vite(['resources/js/website/contact.js'])
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.contact'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="web-contact-hero">
            <span class="web-blog-tag">{{ __('website.contact_hero_tag') }}</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">{{ __('website.contact_hero_title') }}</h1>
            <p class="text-sm text-slate-500 mt-3">{{ __('website.contact_hero_subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mt-10">
            <div class="web-quick-contact-card">
                <div class="web-quick-contact-icon"><i class="fa-solid fa-phone"></i></div>
                <h3 class="font-bold text-sm">{{ __('website.contact_call_us') }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $contactPhone ?? '+1 (321) 645-4321' }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ __('website.contact_call_hours') }}</p>
            </div>
            <div class="web-quick-contact-card">
                <div class="web-quick-contact-icon"><i class="fa-solid fa-envelope"></i></div>
                <h3 class="font-bold text-sm">{{ __('website.contact_email_us') }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $contactEmail }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ __('website.contact_email_note') }}</p>
            </div>
            <div class="web-quick-contact-card">
                <div class="web-quick-contact-icon"><i class="fa-solid fa-comments"></i></div>
                <h3 class="font-bold text-sm">{{ __('website.contact_live_chat') }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ __('website.contact_live_chat_availability') }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ __('website.contact_live_chat_note') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-10">
            <div class="lg:col-span-7">
                <div class="web-contact-card">
                    <h2 class="font-extrabold text-lg mb-1">{{ __('website.contact_form_title') }}</h2>
                    <p class="text-sm text-slate-500 mb-6">{{ __('website.contact_form_subtitle') }}</p>

                    <form id="contact-form" action="{{ route('web.contact.submit', ['lang' => current_lang()]) }}" method="POST" novalidate>
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5">
                            <div class="web-contact-field">
                                <label for="contact-name">{{ __('website.contact_full_name') }}</label>
                                <input id="contact-name" name="name" type="text" placeholder="{{ __('website.contact_name_placeholder') }}">
                                <p id="contact-name-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.contact_name_required') }}</p>
                            </div>
                            <div class="web-contact-field">
                                <label for="contact-email">{{ __('website.contact_email_address') }}</label>
                                <input id="contact-email" name="email" type="email" placeholder="you@example.com">
                                <p id="contact-email-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.contact_email_required') }}</p>
                            </div>
                        </div>

                        <div class="web-contact-field">
                            <label for="contact-subject">{{ __('website.contact_subject') }}</label>
                            <select id="contact-subject" name="subject">
                                <option value="">{{ __('website.contact_subject_placeholder') }}</option>
                                <option value="order">{{ __('website.contact_subject_order') }}</option>
                                <option value="returns">{{ __('website.contact_subject_returns') }}</option>
                                <option value="product">{{ __('website.contact_subject_product') }}</option>
                                <option value="partnership">{{ __('website.contact_subject_partnership') }}</option>
                                <option value="other">{{ __('website.contact_subject_other') }}</option>
                            </select>
                            <p id="contact-subject-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.contact_subject_required') }}</p>
                        </div>

                        <div class="web-contact-field">
                            <label for="contact-message">{{ __('website.contact_message') }}</label>
                            <textarea id="contact-message" name="message" rows="5" placeholder="{{ __('website.contact_message_placeholder') }}"></textarea>
                            <p id="contact-message-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.contact_message_required') }}</p>
                        </div>

                        <button id="contact-submit-btn" type="submit" class="web-btn-primary mt-2">
                            <i class="fa-solid fa-paper-plane"></i> <span>{{ __('website.contact_send') }}</span>
                        </button>

                        <div id="contact-success" class="web-field-success hidden">
                            <i class="fa-solid fa-circle-check"></i> {{ __('website.contact_success') }}
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="web-contact-card">
                    <h2 class="font-extrabold text-lg mb-5">{{ __('website.contact_info_title') }}</h2>

                    <div class="web-info-row">
                        <div class="web-info-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <h4>{{ __('website.contact_address_title') }}</h4>
                            <p>{{ __('website.contact_address_value') }}</p>
                        </div>
                    </div>
                    <div class="web-info-row">
                        <div class="web-info-icon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <h4>{{ __('website.contact_phone_title') }}</h4>
                            <p>{{ $contactPhone ?? '+1 (321) 645-4321' }}</p>
                        </div>
                    </div>
                    <div class="web-info-row">
                        <div class="web-info-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <h4>{{ __('website.contact_email_title') }}</h4>
                            <p>{{ $contactEmail }}</p>
                        </div>
                    </div>
                    <div class="web-info-row">
                        <div class="web-info-icon"><i class="fa-solid fa-clock"></i></div>
                        <div>
                            <h4>{{ __('website.contact_hours_title') }}</h4>
                            <p>{{ __('website.contact_hours_value') }}</p>
                        </div>
                    </div>

                    <div class="web-map-preview mt-6">
                        <img src="https://placehold.co/700x320/eef4ff/2b6ee0?text=Map+Preview" alt="{{ __('website.contact_map_alt') }}">
                        <span class="web-map-pin"><i class="fa-solid fa-store"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-14">
            <p class="text-sm text-slate-500">
                {{ __('website.contact_faq_teaser') }}
                <a href="{{ route('web.faqs.index', ['lang' => current_lang()]) }}" class="text-brand font-semibold hover:underline">{{ __('website.contact_faq_link') }}</a>
            </p>
        </div>
    </section>
@endsection
