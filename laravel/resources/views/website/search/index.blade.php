@extends('website.layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-10">
        <x-website.section-title :title="__('website.search').': '.$keyword" />

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <x-website.product-card :product="$product" />
            @endforeach
        </div>

        <x-website.pagination :paginator="$products" class="mt-8" />
    </div>
@endsection
