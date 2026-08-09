@extends('website.layouts.app')

@section('title', __('website.my_account'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 pt-5">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => __('website.my_account'), 'url' => null],
        ]" />
    </div>

    <section class="max-w-7xl mx-auto px-4 mt-4 pb-16">
        <div class="web-account-layout">
            @include('website.account._sidebar', ['customer' => $customer, 'active' => 'dashboard'])

            <div class="min-w-0">
                <div class="web-account-card">
                    <div class="web-account-card-head">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">{{ __('website.account_overview') }}</h1>
                            <p class="web-account-card-sub">{{ __('website.member_since', ['date' => $customer->created_at?->translatedFormat('F Y')]) }}</p>
                        </div>
                    </div>

                    <p class="text-slate-700 font-medium">{{ $customer->name }}</p>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $customer->email }}</p>
                    @if ($customer->phone)
                        <p class="text-sm text-slate-500 mt-0.5">{{ $customer->phone }}</p>
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6">
                        <a href="{{ route('web.account.orders', ['lang' => current_lang()]) }}" class="web-btn-outline">
                            <i class="fa-solid fa-box"></i> {{ __('website.my_orders') }}
                        </a>
                        <a href="{{ route('web.account.wishlist', ['lang' => current_lang()]) }}" class="web-btn-outline">
                            <i class="fa-regular fa-heart"></i> {{ __('website.wishlist') }}
                        </a>
                        <a href="{{ route('web.account.addresses.index', ['lang' => current_lang()]) }}" class="web-btn-outline">
                            <i class="fa-solid fa-location-dot"></i> {{ __('website.addresses') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
