@extends('website.layouts.app')

@section('title', $category?->name ?? __('website.shop'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 web-category" x-data="{ filtersOpen: false }">
        <x-website.breadcrumb :items="[
            ['label' => __('website.home'), 'url' => route('web.home', ['lang' => current_lang()])],
            ['label' => $category?->name ?? __('website.shop'), 'url' => null],
        ]" />

        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">{{ $category?->name ?? __('website.shop') }}</h1>
        @if ($category)
            <p class="text-sm text-slate-500 mt-1">{{ __('website.products_found') }}: {{ $products->total() }}</p>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-6">

            {{-- ============ FILTERS SIDEBAR ============ --}}
            <aside class="lg:col-span-3">
                <button type="button" @click="filtersOpen = true"
                    class="lg:hidden w-full flex items-center justify-center gap-2 border border-slate-300 rounded-full py-2.5 text-sm font-semibold mb-4">
                    <i class="fa-solid fa-sliders"></i> {{ __('website.filters_and_sort') }}
                </button>

                <div x-show="filtersOpen" x-cloak @click="filtersOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

                <div
                    x-show="filtersOpen || window.innerWidth >= 1024"
                    x-cloak
                    class="web-filters-panel fixed lg:sticky top-0 lg:top-24 start-0 h-full lg:h-auto w-80 max-w-[85vw] lg:w-auto bg-white z-50 lg:z-auto overflow-y-auto lg:overflow-visible"
                >
                    <div class="flex items-center justify-between p-4 border-b border-slate-100 lg:hidden">
                        <span class="font-bold text-lg">{{ __('website.filter_by') }}</span>
                        <button type="button" @click="filtersOpen = false" class="text-xl" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                    </div>

                    <form method="GET" action="{{ url()->current() }}" class="p-4 lg:p-0 border border-slate-100 lg:border-0 rounded-2xl lg:rounded-none">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-bold text-base text-slate-900">{{ __('website.filter_by') }}</h2>
                            <a href="{{ url()->current() }}" class="text-xs font-semibold text-brand hover:underline">{{ __('website.clear_all') }}</a>
                        </div>

                        {{-- Categories --}}
                        <div class="web-filter-group" x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="web-filter-acc-btn">
                                <span class="web-filter-title">{{ __('website.categories_filter') }}</span>
                                <i class="fa-solid fa-chevron-up text-[11px] text-slate-400 transition-transform" :class="{ 'rotate-180': !open }"></i>
                            </button>
                            <div x-show="open" x-collapse class="flex flex-col gap-2 mt-3">
                                <a href="{{ route('web.categories.show', ['lang' => current_lang()] + request()->query()) }}"
                                    class="text-sm {{ ! $category ? 'text-brand font-semibold' : 'text-slate-600 hover:text-brand' }}">
                                    {{ __('website.all_categories') }}
                                </a>
                                @foreach ($categories as $cat)
                                    <a href="{{ route('web.categories.show', ['lang' => current_lang(), 'categorySlug' => $cat->slug] + request()->query()) }}"
                                        class="text-sm {{ $category?->id === $cat->id ? 'text-brand font-semibold' : 'text-slate-600 hover:text-brand' }}">
                                        {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="web-filter-group" x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="web-filter-acc-btn">
                                <span class="web-filter-title">{{ __('website.price') }}</span>
                                <i class="fa-solid fa-chevron-up text-[11px] text-slate-400 transition-transform" :class="{ 'rotate-180': !open }"></i>
                            </button>
                            <div x-show="open" x-collapse class="flex items-center gap-2 mt-3">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ __('website.min_price') }}"
                                    class="w-1/2 rounded-lg border border-slate-200 px-3 py-1.5 text-sm">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ __('website.max_price') }}"
                                    class="w-1/2 rounded-lg border border-slate-200 px-3 py-1.5 text-sm">
                            </div>
                        </div>

                        {{-- Brand --}}
                        @if ($brands->isNotEmpty())
                            <div class="web-filter-group" x-data="{ open: true }">
                                <button type="button" @click="open = !open" class="web-filter-acc-btn">
                                    <span class="web-filter-title">{{ __('website.brand') }}</span>
                                    <i class="fa-solid fa-chevron-up text-[11px] text-slate-400 transition-transform" :class="{ 'rotate-180': !open }"></i>
                                </button>
                                <div x-show="open" x-collapse class="flex flex-col gap-2 mt-3">
                                    @foreach ($brands as $brand)
                                        <label class="web-filter-check">
                                            <input type="radio" name="brand_id" value="{{ $brand->id }}" {{ (string) request('brand_id') === (string) $brand->id ? 'checked' : '' }}>
                                            <span>{{ $brand->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <input type="hidden" name="sort" value="{{ request('sort') }}">

                        <button type="submit" class="web-btn-primary w-full mt-5">{{ __('website.apply_filters') }}</button>
                    </form>
                </div>
            </aside>

            {{-- ============ PRODUCTS ============ --}}
            <div class="lg:col-span-9">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <p class="text-sm text-slate-500">
                        <span class="font-semibold text-slate-900">{{ $products->total() }}</span> {{ __('website.products_found') }}
                    </p>
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                        @foreach (request()->except(['sort', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="sort-select" class="text-xs text-slate-500 hidden sm:inline">{{ __('website.sort_by') }}</label>
                        <select id="sort-select" name="sort" onchange="this.form.submit()"
                            class="text-sm border border-slate-200 rounded-full px-4 py-2 outline-none focus:ring-2 focus:ring-brand">
                            <option value="" {{ request('sort') ? '' : 'selected' }}>{{ __('website.sort_default') }}</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>{{ __('website.sort_price_asc') }}</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>{{ __('website.sort_price_desc') }}</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>{{ __('website.sort_newest') }}</option>
                        </select>
                    </form>
                </div>

                @if ($products->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                        @foreach ($products as $product)
                            <x-website.product-card :product="$product" />
                        @endforeach
                    </div>

                    <x-website.pagination :paginator="$products" class="mt-10 flex justify-center" />
                @else
                    <div class="text-center py-16 text-slate-500">
                        <i class="fa-solid fa-box-open text-3xl mb-3"></i>
                        <p>{{ __('website.no_products_found') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/website/products.js'])
@endpush
