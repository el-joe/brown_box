@extends('website.layouts.app')

@section('title', __('website.checkout'))

@section('content')
    @php
        $gatewayMeta = [
            'bank_transfer' => ['icon' => 'fa-building-columns', 'name' => __('website.bank_transfer'), 'desc' => __('website.bank_transfer_desc')],
            'vodafone_cash' => ['icon' => 'fa-mobile-screen-button', 'name' => __('website.vodafone_cash'), 'desc' => __('website.vodafone_cash_desc')],
            'instapay' => ['icon' => 'fa-money-bill-transfer', 'name' => __('website.instapay'), 'desc' => __('website.instapay_desc')],
            'paymob' => ['icon' => 'fa-credit-card', 'name' => __('website.paymob'), 'desc' => __('website.paymob_desc')],
        ];
        $manualGatewayCodes = ['bank_transfer', 'vodafone_cash', 'instapay'];

        $citiesByGovernorate = $governorates->mapWithKeys(function ($gov) {
            return [
                $gov->id => $gov->cities->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name' => current_lang() === 'ar' ? $city->name_ar : $city->name_en,
                    ];
                }),
            ];
        });
    @endphp

    <div class="web-checkout max-w-7xl mx-auto px-4 py-8" data-subtotal="{{ $subtotal }}">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.cart'), 'url' => route('web.cart.index', ['lang' => current_lang()])],
            ['label' => __('website.checkout'), 'url' => null],
        ]" />

        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">{{ __('website.checkout') }}</h1>

        <div class="web-checkout-steps">
            <div class="web-checkout-step is-done">
                <span class="web-checkout-step-circle"><i class="fa-solid fa-check"></i></span>
                <span class="web-checkout-step-label">{{ __('website.checkout_step_cart') }}</span>
            </div>
            <div class="web-checkout-step-line"></div>
            <div class="web-checkout-step is-active" data-step-indicator="1">
                <span class="web-checkout-step-circle">1</span>
                <span class="web-checkout-step-label">{{ __('website.delivery_address') }}</span>
            </div>
            <div class="web-checkout-step-line"></div>
            <div class="web-checkout-step" data-step-indicator="2">
                <span class="web-checkout-step-circle">2</span>
                <span class="web-checkout-step-label">{{ __('website.shipping') }}</span>
            </div>
            <div class="web-checkout-step-line"></div>
            <div class="web-checkout-step" data-step-indicator="3">
                <span class="web-checkout-step-circle">3</span>
                <span class="web-checkout-step-label">{{ __('website.payment_method') }}</span>
            </div>
        </div>

        @if (count($items) === 0)
            <div class="web-empty-state mt-6">
                <i class="fa-solid fa-bag-shopping"></i>
                <p class="font-semibold text-lg mt-4 text-slate-700">{{ __('website.cart_empty_notice') }}</p>
                <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-btn-primary mt-5 inline-flex px-8">{{ __('website.continue_shopping') }}</a>
            </div>
        @else
            <form id="checkout-form" method="POST" action="{{ route('web.checkout.store', ['lang' => current_lang()]) }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-2">
                @csrf

                {{-- ============ LEFT ============ --}}
                <div class="lg:col-span-8">

                    {{-- ---------- Step 1: Address ---------- --}}
                    <div class="web-checkout-block" data-step="1">
                        <h2 class="font-bold text-lg mb-4"><i class="fa-solid fa-location-dot text-brand me-2"></i>{{ __('website.delivery_address') }}</h2>

                        <div id="address-list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($addresses as $address)
                                <label class="web-select-card {{ $loop->first ? 'is-checked' : '' }}" data-address-card
                                    data-id="{{ $address->id }}"
                                    data-city-id="{{ $address->city_id }}"
                                    data-governorate-id="{{ $address->governorate_id }}">
                                    <input type="radio" name="address_id" value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }}>
                                    <span class="web-select-check"><i class="fa-solid fa-check"></i></span>
                                    @if ($address->is_default)
                                        <span class="web-address-badge">{{ __('website.default') }}</span>
                                    @endif
                                    <p class="font-bold text-sm text-slate-900">{{ $address->name }}</p>
                                    <p class="text-sm text-slate-600 mt-0.5">{{ $address->phone }}</p>
                                    <p class="text-sm text-slate-400 mt-1 leading-relaxed">{{ $address->address_line }}, {{ current_lang() === 'ar' ? $address->city?->name_ar : $address->city?->name_en }}, {{ current_lang() === 'ar' ? $address->governorate?->name_ar : $address->governorate?->name_en }}</p>
                                </label>
                            @endforeach

                            <button type="button" id="add-address-btn" class="web-add-address-card">
                                <i class="fa-solid fa-plus"></i>
                                <span>{{ __('website.add_new_address') }}</span>
                            </button>
                        </div>

                        {{-- Inline new-address form (hidden unless "Add new address" chosen) --}}
                        <div id="new-address-form" class="mt-5 hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="web-checkout-field sm:col-span-2">
                                <label for="name">{{ __('website.full_name') }}</label>
                                <input id="name" name="name" type="text" placeholder="e.g. Ahmed Hassan">
                            </div>
                            <div class="web-checkout-field">
                                <label for="phone">{{ __('website.phone_number') }}</label>
                                <input id="phone" name="phone" type="tel" placeholder="e.g. +20 100 123 4567">
                            </div>
                            <div class="web-checkout-field">
                                <label for="governorate_id">{{ __('website.governorate') }}</label>
                                <select id="governorate_id" name="governorate_id">
                                    <option value="">—</option>
                                    @foreach ($governorates as $gov)
                                        <option value="{{ $gov->id }}">{{ current_lang() === 'ar' ? $gov->name_ar : $gov->name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="web-checkout-field">
                                <label for="city_id">{{ __('website.city') }}</label>
                                <select id="city_id" name="city_id">
                                    <option value="">—</option>
                                </select>
                            </div>
                            <div class="web-checkout-field sm:col-span-2">
                                <label for="address_line">{{ __('website.street_address') }}</label>
                                <input id="address_line" name="address_line" type="text" placeholder="Street name, building, apartment">
                            </div>
                        </div>
                        <p id="address-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.select_address') }}</p>

                        <div class="flex justify-end mt-5">
                            <button type="button" class="web-btn-primary px-8" data-next-step>{{ __('website.next') }}</button>
                        </div>
                    </div>

                    {{-- ---------- Step 2: Shipping ---------- --}}
                    <div class="web-checkout-block hidden" data-step="2">
                        <h2 class="font-bold text-lg mb-4"><i class="fa-solid fa-truck text-brand me-2"></i>{{ __('website.shipping') }}</h2>

                        <div id="shipping-companies" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <p class="text-sm text-slate-400">{{ __('website.select_address') }}</p>
                        </div>

                        <div class="flex justify-between mt-5">
                            <button type="button" class="web-btn-outline px-8" data-prev-step>{{ __('website.back') }}</button>
                            <button type="button" class="web-btn-primary px-8" data-next-step>{{ __('website.next') }}</button>
                        </div>
                    </div>

                    {{-- ---------- Step 3: Payment ---------- --}}
                    <div class="web-checkout-block hidden" data-step="3">
                        <h2 class="font-bold text-lg mb-4"><i class="fa-solid fa-credit-card text-brand me-2"></i>{{ __('website.payment_method') }}</h2>

                        <div id="payment-methods" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach ($gateways as $gateway)
                                @php $meta = $gatewayMeta[$gateway->code] ?? ['icon' => 'fa-wallet', 'name' => $gateway->code, 'desc' => '']; @endphp
                                <label class="web-select-card web-payment-card" data-payment-card data-method="{{ $gateway->code }}">
                                    <input type="radio" name="payment_gateway" value="{{ $gateway->code }}">
                                    <span class="web-select-check"><i class="fa-solid fa-check"></i></span>
                                    <i class="fa-solid {{ $meta['icon'] }} web-payment-icon"></i>
                                    <p class="web-payment-name">{{ $meta['name'] }}</p>
                                    <p class="web-payment-desc">{{ $meta['desc'] }}</p>
                                </label>
                            @endforeach
                        </div>
                        <p id="payment-error" class="web-field-error hidden"><i class="fa-solid fa-circle-exclamation"></i> {{ __('website.select_payment_method') }}</p>

                        {{-- Dynamic instructions per manual gateway --}}
                        <div id="payment-details" class="web-payment-details hidden">
                            @foreach ($gateways as $gateway)
                                @continue(! in_array($gateway->code, $manualGatewayCodes, true))
                                @php $meta = $gatewayMeta[$gateway->code] ?? []; $config = $gateway->config ?? []; @endphp
                                <div id="pm-details-{{ $gateway->code }}" class="pm-instructions hidden">
                                    <p class="font-bold text-sm mb-3">{{ $meta['name'] }} {{ __('website.upload_payment_invoice') }}</p>
                                    @foreach ($config as $label => $value)
                                        <div class="web-pm-row">
                                            <span>{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                                            <b>{{ $value }}</b>
                                            <button type="button" class="web-copy-btn" data-copy="{{ $value }}"><i class="fa-regular fa-copy"></i></button>
                                        </div>
                                    @endforeach
                                    @if (empty($config))
                                        <p class="text-sm text-slate-400">{{ __('website.upload_invoice_hint') }}</p>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Invoice upload (shared across manual methods) --}}
                            <div class="mt-5" id="invoice-upload-block">
                                <label class="text-sm font-semibold">{{ __('website.upload_payment_invoice') }} <span class="text-red-500">*</span></label>
                                <p class="text-xs text-slate-400 mt-0.5 mb-2.5">{{ __('website.upload_invoice_hint') }}</p>

                                <div id="file-dropzone" class="web-file-dropzone">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <p class="text-sm font-medium mt-2">{{ __('website.click_to_upload') }}</p>
                                    <input type="file" id="invoice-input" name="payment_proof" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/*" class="hidden">
                                </div>

                                <div id="file-preview" class="web-file-preview hidden">
                                    <i class="fa-solid fa-file-lines text-brand text-xl"></i>
                                    <div class="min-w-0">
                                        <p id="file-preview-name" class="text-sm font-medium truncate"></p>
                                        <p id="file-preview-size" class="text-xs text-slate-400"></p>
                                    </div>
                                    <button type="button" id="file-remove-btn" class="ms-auto text-slate-400 hover:text-red-600" aria-label="Remove file"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="mt-5">
                                <label for="notes" class="text-sm font-semibold">{{ __('website.additional_notes') }} <span class="text-slate-400 font-normal">({{ __('website.optional') }})</span></label>
                                <textarea id="notes" name="notes" rows="3" placeholder="Any additional information about your payment..." class="web-checkout-textarea mt-2"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-between mt-5">
                            <button type="button" class="web-btn-outline px-8" data-prev-step>{{ __('website.back') }}</button>
                        </div>
                    </div>
                </div>

                {{-- ============ RIGHT: SUMMARY ============ --}}
                <div class="lg:col-span-4">
                    <div class="web-summary-card">
                        <h2 class="font-bold text-lg mb-4">{{ __('website.order_summary') }}</h2>

                        <div id="summary-items" class="flex flex-col">
                            @foreach ($items as $item)
                                @php
                                    $product = $item['product'];
                                    $variant = $item['variant'];
                                    $image = $product->main_image?->url ?? 'https://placehold.co/120x120';
                                    $variantLabel = $variant?->variantAttributes
                                        ->map(fn ($va) => $va->attributeValue?->value)
                                        ->filter()
                                        ->implode(' · ');
                                @endphp
                                <div class="web-summary-mini-item">
                                    <img src="{{ $image }}" alt="{{ $product->name }}" class="web-summary-mini-img">
                                    <div class="min-w-0">
                                        <p class="web-summary-mini-title truncate">{{ $product->name }}</p>
                                        <p class="web-summary-mini-meta">{{ $variantLabel ? $variantLabel.' · ' : '' }}{{ __('website.quantity') }} {{ $item['qty'] }}</p>
                                    </div>
                                    <span class="web-summary-mini-price">{{ money_format($item['total']) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="web-summary-divider"></div>

                        <div class="mb-3">
                            <label for="coupon-input" class="text-xs font-semibold text-slate-500">{{ __('website.have_promo_code') }}</label>
                            <div class="web-coupon-form mt-2">
                                <input id="coupon-input" type="text" placeholder="{{ __('website.enter_coupon_code') }}" autocomplete="off">
                                <button id="coupon-apply-btn" type="button">{{ __('website.apply') }}</button>
                            </div>
                            <div id="coupon-message" class="web-coupon-message hidden"></div>
                            <input type="hidden" name="coupon_code" id="coupon-code-input">
                        </div>

                        <div class="web-summary-row"><span>{{ __('website.subtotal') }}</span><span id="summary-subtotal">{{ money_format($subtotal) }}</span></div>
                        <div id="summary-discount-row" class="web-summary-row text-emerald-600 hidden">
                            <span>{{ __('website.discount') }}</span><span id="summary-discount">&minus;{{ money_format(0) }}</span>
                        </div>
                        <div class="web-summary-row"><span>{{ __('website.shipping') }}</span><span id="summary-shipping">{{ __('website.free') }}</span></div>

                        <div class="web-summary-divider"></div>

                        <div class="web-summary-row web-summary-total"><span>{{ __('website.total') }}</span><span id="summary-total">{{ money_format($subtotal) }}</span></div>

                        <input type="hidden" name="shipping_company_id" id="shipping-company-id-input">

                        <button id="place-order-btn" type="submit" class="web-btn-primary w-full mt-6">
                            <i class="fa-solid fa-lock"></i> <span>{{ __('website.place_order') }}</span>
                        </button>

                        <div class="flex items-center justify-center gap-2 text-xs text-slate-400 mt-4">
                            <i class="fa-solid fa-shield-halved"></i> {{ __('website.order_details_secure') }}
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        window.checkoutData = {
            citiesByGovernorate: @json($citiesByGovernorate),
        };
    </script>
    @vite(['resources/js/website/checkout.js'])
@endpush
