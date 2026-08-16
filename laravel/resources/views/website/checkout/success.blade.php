@extends('website.layouts.app')

@section('title', __('website.order_placed_successfully'))

@section('content')
    <div class="web-checkout max-w-xl mx-auto px-4 py-16 text-center">
        <div class="web-success-icon"><i class="fa-solid fa-check"></i></div>
        <h1 class="text-2xl font-extrabold text-slate-900 mt-4">{{ __('website.order_placed_successfully') }}</h1>
        <p class="text-sm text-slate-500 mt-2">{{ __('website.order_placed_thanks') }}</p>

        @if ($order)
            <p class="text-sm font-semibold mt-4">
                {{ __('website.order_number') }}: <span class="text-brand">#{{ $order->order_number }}</span>
            </p>

            <div class="web-summary-card text-start mt-6">
                <div id="summary-items" class="flex flex-col">
                    @foreach ($order->items as $item)
                        <div class="web-summary-mini-item">
                            <div class="min-w-0">
                                <p class="web-summary-mini-title truncate">{{ $item->product_name }}</p>
                                <p class="web-summary-mini-meta">{{ __('website.quantity') }} {{ $item->qty }}</p>
                            </div>
                            <span class="web-summary-mini-price">{{ money_format($item->total_price) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="web-summary-divider"></div>

                <div class="web-summary-row"><span>{{ __('website.subtotal') }}</span><span>{{ money_format($order->subtotal) }}</span></div>
                @if ($order->discount_amount > 0)
                    <div class="web-summary-row text-emerald-600"><span>{{ __('website.discount') }}</span><span>&minus;{{ money_format($order->discount_amount) }}</span></div>
                @endif
                <div class="web-summary-row"><span>{{ __('website.shipping') }}</span><span>{{ $order->shipping_amount > 0 ? money_format($order->shipping_amount) : __('website.free') }}</span></div>
                <div class="web-summary-divider"></div>
                <div class="web-summary-row web-summary-total"><span>{{ __('website.total') }}</span><span>{{ money_format($order->total_amount) }}</span></div>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 mt-8">
            <a href="{{ route('web.home', ['lang' => current_lang()]) }}" class="web-btn-outline flex-1 justify-center">{{ __('website.continue_shopping') }}</a>
            <a href="{{ route('web.track-order.index', ['lang' => current_lang()]) }}" class="web-btn-primary flex-1 justify-center">{{ __('website.track_order_cta') }}</a>
        </div>
    </div>
@endsection
