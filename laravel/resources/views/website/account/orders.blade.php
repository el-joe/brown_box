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

                    @if ($orders->isNotEmpty())
                        <div class="order-tabs mb-6 flex flex-wrap gap-2">
                            <button type="button" class="order-tab active rounded-full px-4 py-2 text-sm font-semibold bg-brand text-white" data-filter="all">{{ __('website.my_orders') }}</button>
                            <button type="button" class="order-tab rounded-full px-4 py-2 text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200" data-filter="processing">{{ __('website.status_processing') }}</button>
                            <button type="button" class="order-tab rounded-full px-4 py-2 text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200" data-filter="shipped">{{ __('website.status_shipped') }}</button>
                            <button type="button" class="order-tab rounded-full px-4 py-2 text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200" data-filter="delivered">{{ __('website.status_delivered') }}</button>
                            <button type="button" class="order-tab rounded-full px-4 py-2 text-sm font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200" data-filter="cancelled">{{ __('website.status_cancelled') }}</button>
                        </div>
                    @endif

                    <div id="orders-list">
                    @forelse ($orders as $order)
                        @php $meta = $statusMeta[$order->status->value] ?? ['icon' => 'fa-circle', 'class' => 'pending']; @endphp
                        <div class="web-order-card" data-status="{{ $order->status->value }}">
                            <div class="web-order-card-top">
                                <div>
                                    <p class="web-order-number">{{ $order->order_number }}</p>
                                    <p class="web-order-date">
                                        {{ __('website.order_placed_on', ['date' => $order->created_at->translatedFormat('F j, Y')]) }}
                                        &middot;
                                        {{ $order->items->count() === 1 ? __('website.item_count', ['count' => 1]) : __('website.items_count', ['count' => $order->items->count()]) }}
                                    </p>
                                </div>
                                <span class="web-order-status-{{ $meta['class'] }}">
                                    <i class="fa-solid {{ $meta['icon'] }}"></i> {{ __('website.status_'.$order->status->value) }}
                                </span>
                            </div>
                            <div class="web-order-card-bottom">
                                <div class="web-order-total">{{ __('website.order_total') }} <b>{{ money_format($order->total_amount) }}</b></div>
                                <div class="flex items-center gap-3">
                                    @if (in_array($order->status->value, ['shipped', 'processing', 'confirmed']))
                                        <a href="{{ route('web.track-order.index', ['lang' => current_lang()]) }}" class="web-btn-outline">
                                            {{ __('website.track_order') }}
                                        </a>
                                    @endif
                                    <a href="{{ route('web.account.orders.show', ['lang' => current_lang(), 'order' => $order->id]) }}" class="web-btn-primary">
                                        {{ __('website.view_details') }}
                                    </a>
                                </div>
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

                    @if ($orders->isNotEmpty())
                        <div id="orders-empty" class="web-account-empty hidden">
                            <i class="fa-solid fa-box-open"></i>
                            <p>{{ __('website.no_orders_yet') }}</p>
                        </div>
                    @endif
                </div>

                @if ($orders->hasPages())
                    <x-website.pagination :paginator="$orders" />
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @vite(['resources/js/website/account.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.order-tab');
            const cards = document.querySelectorAll('#orders-list > [data-status]');
            const emptyState = document.getElementById('orders-empty');
            const activeClasses = ['bg-brand', 'text-white'];
            const inactiveClasses = ['bg-slate-100', 'text-slate-600'];

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabs.forEach(function (t) {
                        t.classList.remove('active', ...activeClasses);
                        t.classList.add(...inactiveClasses);
                    });
                    tab.classList.add('active', ...activeClasses);
                    tab.classList.remove(...inactiveClasses);

                    const filter = tab.dataset.filter;
                    let visibleCount = 0;
                    cards.forEach(function (card) {
                        const show = filter === 'all' || card.dataset.status === filter;
                        card.classList.toggle('hidden', !show);
                        if (show) visibleCount++;
                    });
                    if (emptyState) {
                        emptyState.classList.toggle('hidden', visibleCount !== 0);
                    }
                });
            });
        });
    </script>
@endpush
