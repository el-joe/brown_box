@extends('website.layouts.app')

@section('title', __('website.track_order') . ' - ' . __('website.site_name'))

@push('styles')
    @vite(['resources/js/website/track-order.js'])
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.track_order'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-3xl mx-auto px-4 mt-6 pb-16">
        <div class="web-track-hero">
            <i class="fa-solid fa-truck-fast text-3xl"></i>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-4">{{ __('website.track_order_title') }}</h1>
            <p class="text-sm text-white/80 mt-2">{{ __('website.track_order_subtitle') }}</p>

            <form id="track-form" class="web-track-form" method="POST" action="{{ route('web.track-order.track', ['lang' => current_lang()]) }}" novalidate>
                @csrf
                <input id="track-order-number" name="order_number" type="text" placeholder="{{ __('website.track_order_number_placeholder') }}" autocomplete="off" value="{{ old('order_number') }}" required>
                <input id="track-phone" name="phone" type="text" placeholder="{{ __('website.track_order_phone_placeholder') }}" autocomplete="tel" value="{{ old('phone') }}" required>
                <button type="submit" aria-label="{{ __('website.track_order') }}"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <p id="track-error" class="web-track-error {{ isset($notFound) && $notFound ? '' : 'hidden' }}">
                <i class="fa-solid fa-circle-exclamation"></i> {{ __('website.track_order_not_found') }}
            </p>
        </div>

        @if ($order)
            @php
                $statusMeta = [
                    'pending' => ['icon' => 'fa-clock', 'label' => __('website.status_pending')],
                    'processing' => ['icon' => 'fa-boxes-packing', 'label' => __('website.status_processing')],
                    'shipped' => ['icon' => 'fa-truck-fast', 'label' => __('website.status_shipped')],
                    'delivered' => ['icon' => 'fa-house', 'label' => __('website.status_delivered')],
                    'cancelled' => ['icon' => 'fa-circle-xmark', 'label' => __('website.status_cancelled')],
                    'refunded' => ['icon' => 'fa-rotate-left', 'label' => __('website.status_refunded')],
                ];
                $steps = ['pending', 'processing', 'shipped', 'delivered'];
                $currentIndex = array_search($order->status, $steps, true);
                $meta = $statusMeta[$order->status] ?? ['icon' => 'fa-circle', 'label' => $order->status];
            @endphp
            <div class="mt-8">
                <div class="web-order-page-card">
                    <div class="flex items-center justify-between flex-wrap gap-3 mb-2">
                        <div>
                            <p class="text-xs text-slate-400 font-semibold">{{ __('website.track_order_number_label') }}</p>
                            <p class="text-xl font-extrabold text-slate-900">#{{ $order->order_number }}</p>
                        </div>
                        <span class="web-order-status web-order-status-{{ $order->status }}"><i class="fa-solid {{ $meta['icon'] }}"></i> {{ $meta['label'] }}</span>
                    </div>

                    @if ($order->status !== 'cancelled')
                        <div class="web-order-timeline mt-6">
                            @foreach ($steps as $index => $step)
                                <div class="web-timeline-step {{ $currentIndex !== false && $index < $currentIndex ? 'done' : '' }} {{ $currentIndex !== false && $index === $currentIndex ? 'active' : '' }}">
                                    <div class="web-timeline-connector"></div>
                                    <span class="web-timeline-icon"><i class="fa-solid {{ $statusMeta[$step]['icon'] }}"></i></span>
                                    <div>
                                        <p class="web-timeline-label">{{ $statusMeta[$step]['label'] }}</p>
                                        <p class="web-timeline-date">
                                            {{ $order->statusHistories->firstWhere('status', $step)?->created_at?->format('M j') ?? __('website.track_order_pending') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($order->tracking_number)
                        <div class="web-info-card mt-2">
                            <p class="web-info-card-label"><i class="fa-solid fa-truck"></i> {{ __('website.track_order_courier_update') }}</p>
                            <p>{{ __('website.track_order_tracking_number') }}: <b>{{ $order->tracking_number }}</b></p>
                        </div>
                    @endif
                </div>

                <div class="web-order-page-card">
                    <h2 class="font-bold text-lg mb-3">{{ __('website.track_order_package_contents') }}</h2>
                    @foreach ($order->items as $item)
                        <div class="web-track-summary-item">
                            <img class="web-track-summary-img" src="{{ $item->product_snapshot['image'] ?? asset('images/placeholder.png') }}" alt="{{ $item->product_name }}">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold">{{ $item->product_name }}</p>
                                <p class="text-xs text-slate-400">{{ __('website.track_order_qty') }} {{ $item->qty }}</p>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex flex-col sm:flex-row gap-3 mt-5">
                        <a href="{{ route('web.home', ['lang' => current_lang()]) }}" class="web-btn-outline flex-1 justify-center">{{ __('website.continue_shopping') }}</a>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
