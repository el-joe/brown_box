@extends('website.layouts.app')

@section('title', __('website.my_orders'))

@php
    $statusMeta = [
        'pending' => ['icon' => 'fa-clock', 'class' => 'pending'],
        'processing' => ['icon' => 'fa-clock', 'class' => 'processing'],
        'shipped' => ['icon' => 'fa-truck-fast', 'class' => 'shipped'],
        'delivered' => ['icon' => 'fa-circle-check', 'class' => 'delivered'],
        'cancelled' => ['icon' => 'fa-circle-xmark', 'class' => 'cancelled'],
        'refunded' => ['icon' => 'fa-rotate-left', 'class' => 'refunded'],
    ];
@endphp

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.my_orders'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="web-account-layout">
            @include('website.account._sidebar', ['active' => 'orders'])

            <div class="min-w-0">
                <div class="web-account-card">
                    <div class="web-account-card-head">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">{{ __('website.my_orders') }}</h1>
                            <p class="web-account-card-sub">{{ __('website.orders_track_manage') }}</p>
                        </div>
                    </div>

                    @forelse ($orders as $order)
                        @php $meta = $statusMeta[$order->status->value] ?? ['icon' => 'fa-circle', 'class' => 'pending']; @endphp
                        <div class="web-order-card">
                            <div class="web-order-card-top">
                                <div>
                                    <p class="web-order-number">{{ $order->order_number }}</p>
                                    <p class="web-order-date">
                                        {{ __('website.order_placed_on', ['date' => $order->created_at->translatedFormat('F j, Y')]) }}
                                        &middot;
                                        {{ $order->items->count() === 1 ? __('website.item_count', ['count' => 1]) : __('website.items_count', ['count' => $order->items->count()]) }}
                                    </p>
                                </div>
                                <span class="web-order-status {{ $meta['class'] }}">
                                    <i class="fa-solid {{ $meta['icon'] }}"></i> {{ __('website.status_'.$order->status->value) }}
                                </span>
                            </div>
                            <div class="web-order-card-bottom">
                                <div class="web-order-total">{{ __('website.order_total') }} <b>{{ money_format($order->total_amount) }}</b></div>
                                <a href="{{ route('web.account.orders.show', ['lang' => current_lang(), 'order' => $order->id]) }}" class="web-btn-primary">
                                    {{ __('website.view_details') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="web-account-empty">
                            <i class="fa-solid fa-box-open"></i>
                            <p>{{ __('website.no_orders_yet') }}</p>
                            <a href="{{ route('web.products.index', ['lang' => current_lang()]) }}" class="web-btn-primary mt-4">{{ __('website.browse_products') }}</a>
                        </div>
                    @endforelse
                </div>

                @if ($orders->hasPages())
                    <x-website.pagination :paginator="$orders" />
                @endif
            </div>
        </div>
    </section>
@endsection
