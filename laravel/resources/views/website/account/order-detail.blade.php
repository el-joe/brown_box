@extends('website.layouts.app')

@section('title', $order->order_number)

@php
    $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
    $statusIcons = [
        'pending' => 'fa-file-circle-check',
        'processing' => 'fa-boxes-packing',
        'shipped' => 'fa-truck-fast',
        'delivered' => 'fa-circle-check',
        'cancelled' => 'fa-circle-xmark',
        'refunded' => 'fa-rotate-left',
    ];
    $address = $order->customer_address ?? [];
    $hasRefundRequest = $order->refundRequests->isNotEmpty();
    $refundEligible = in_array($order->status->value, ['delivered', 'shipped'], true) && ! $hasRefundRequest;
@endphp

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.my_orders'), 'url' => route('web.account.orders', ['lang' => current_lang()])],
            ['label' => $order->order_number, 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="web-account-layout">
            @include('website.account._sidebar', ['active' => 'orders'])

            <div class="min-w-0">
                {{-- Header --}}
                <div class="web-account-card">
                    <div class="web-account-card-head">
                        <div>
                            <p class="web-account-card-sub">{{ __('website.order_number') }}</p>
                            <h1 class="text-xl font-bold text-slate-900 mt-1">{{ $order->order_number }}</h1>
                            <p class="web-account-card-sub mt-1">{{ __('website.order_placed_on', ['date' => $order->created_at->translatedFormat('F j, Y')]) }}</p>
                        </div>
                        <span class="web-order-status {{ $order->status->value }}">
                            <i class="fa-solid {{ $statusIcons[$order->status->value] ?? 'fa-circle' }}"></i> {{ __('website.status_'.$order->status->value) }}
                        </span>
                    </div>
                </div>

                {{-- Status timeline --}}
                @if ($order->statusHistories->isNotEmpty())
                    <div class="web-account-card">
                        <h2 class="text-lg font-bold text-slate-900 mb-4">{{ __('website.order_status') }}</h2>
                        <div class="web-order-timeline">
                            @foreach ($order->statusHistories->sortBy('created_at')->values() as $index => $history)
                                @php
                                    $isLast = $index === $order->statusHistories->count() - 1;
                                    $stepClass = $history->status === 'cancelled' ? 'cancelled' : ($isLast ? 'active' : 'done');
                                @endphp
                                <div class="web-timeline-step {{ $stepClass }}">
                                    <span class="web-timeline-connector"></span>
                                    <span class="web-timeline-icon">
                                        <i class="fa-solid {{ $statusIcons[$history->status] ?? 'fa-circle' }}"></i>
                                    </span>
                                    <div>
                                        <p class="web-timeline-label">{{ __('website.status_'.$history->status) ?? $history->status }}</p>
                                        <p class="web-timeline-date">{{ $history->created_at?->translatedFormat('F j, Y · g:i A') }}</p>
                                        @if ($history->notes)
                                            <p class="text-sm text-slate-500 mt-1">{{ $history->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Items --}}
                <div class="web-account-card">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">{{ __('website.order_items') }}</h2>
                    <div>
                        @foreach ($order->items as $item)
                            <div class="web-order-item-row">
                                <span class="web-order-item-icon"><i class="fa-solid fa-box"></i></span>
                                <div class="min-w-0 flex-1">
                                    <p class="web-order-item-title truncate">{{ $item->product_name }}</p>
                                    @if ($item->variant_label)
                                        <p class="web-order-item-meta">{{ $item->variant_label }}</p>
                                    @endif
                                    <p class="web-order-item-meta">{{ __('website.quantity') }}: {{ $item->qty }}</p>
                                </div>
                                <div class="web-order-item-price">
                                    <b>{{ money_format($item->total_price) }}</b>
                                    <span>{{ money_format($item->unit_price) }} {{ __('website.each') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="web-summary-divider mt-4"></div>
                    <div class="web-summary-row"><span>{{ __('website.subtotal') }}</span><span>{{ money_format($order->subtotal) }}</span></div>
                    @if ($order->discount_amount > 0)
                        <div class="web-summary-row text-emerald-600"><span>{{ __('website.discount') }}</span><span>&minus;{{ money_format($order->discount_amount) }}</span></div>
                    @endif
                    <div class="web-summary-row"><span>{{ __('website.shipping') }}</span><span>{{ $order->shipping_amount > 0 ? money_format($order->shipping_amount) : __('website.free') }}</span></div>
                    <div class="web-summary-divider"></div>
                    <div class="web-summary-row web-summary-total"><span>{{ __('website.total') }}</span><span>{{ money_format($order->total_amount) }}</span></div>
                </div>

                {{-- Shipping & payment info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="web-account-card">
                        <h2 class="text-lg font-bold text-slate-900 mb-3">{{ __('website.shipping_information') }}</h2>
                        @if (!empty($address))
                            <p class="text-sm font-medium text-slate-800">{{ $address['name'] ?? '' }}</p>
                            <p class="text-sm text-slate-500 mt-0.5">{{ $address['phone'] ?? '' }}</p>
                            <p class="text-sm text-slate-500 mt-1">{{ $address['address_line'] ?? '' }}</p>
                        @endif
                        @if ($order->shippingCompany)
                            <p class="text-sm text-slate-500 mt-2">{{ __('website.shipping_company') }}: {{ $order->shippingCompany->name ?? '' }}</p>
                        @endif
                        @if ($order->tracking_number)
                            <p class="text-sm text-slate-500 mt-1">{{ __('website.tracking_number') }}: {{ $order->tracking_number }}</p>
                        @endif
                    </div>

                    <div class="web-account-card">
                        <h2 class="text-lg font-bold text-slate-900 mb-3">{{ __('website.payment_information') }}</h2>
                        <p class="text-sm text-slate-500">{{ __('website.payment_gateway') }}: {{ $order->payment_gateway?->value }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ __('website.payment_status') }}: {{ $order->payment_status->value }}</p>
                    </div>
                </div>

                {{-- Refund request --}}
                @if ($hasRefundRequest)
                    <div class="web-account-card">
                        <p class="text-sm text-slate-500"><i class="fa-solid fa-circle-info text-brand me-1.5"></i>{{ __('website.refund_already_requested') }}</p>
                    </div>
                @elseif ($refundEligible)
                    <div class="web-account-card">
                        <h2 class="text-lg font-bold text-slate-900 mb-1">{{ __('website.request_refund') }}</h2>
                        <form id="refund-form" method="POST" action="{{ route('web.account.orders.refund', ['lang' => current_lang(), 'order' => $order->id]) }}" class="mt-4 space-y-3">
                            @csrf
                            <div class="web-checkout-field">
                                <label for="refund-reason">{{ __('website.refund_reason') }}</label>
                                <input id="refund-reason" name="reason" type="text" required>
                            </div>
                            <div class="web-checkout-field">
                                <label for="refund-details">{{ __('website.refund_details_optional') }}</label>
                                <textarea id="refund-details" name="details" rows="3" class="web-checkout-textarea"></textarea>
                            </div>
                            <button id="refund-submit-btn" type="submit" class="web-btn-outline">{{ __('website.submit_request') }}</button>
                        </form>
                    </div>
                @endif
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
