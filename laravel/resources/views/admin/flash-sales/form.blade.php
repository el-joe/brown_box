@extends('admin.layouts.app')

@php
    $isEdit = $flashSale->exists;
    $validateUrl = $isEdit ? route('admin.flash-sales.update.validate', $flashSale) : route('admin.flash-sales.validate');
    $submitUrl = $isEdit ? route('admin.flash-sales.update', $flashSale) : route('admin.flash-sales.store');

    $initialItems = $isEdit
        ? $flashSale->items->map(fn ($item) => [
            'key' => 'item-'.$item->id,
            'product_id' => $item->product_id,
            'text' => $item->product?->getTranslation('name', app()->getLocale()).' ('.$item->product?->sku.')',
            'sku' => $item->product?->sku,
            'price' => (float) $item->product?->price,
            'has_variants' => (bool) $item->product?->has_variants,
            'variants' => $item->product?->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) ($variant->price ?? $item->product->price),
            ])->values() ?? [],
            'variant_id' => $item->variant_id,
            'discount_type' => $item->discount_type,
            'discount_value' => (float) $item->discount_value,
            'max_qty' => $item->max_qty,
        ])->values()
        : collect();
@endphp

@section('title', $isEdit ? __('Edit Flash Sale') : __('Create Flash Sale'))

@section('breadcrumb')
    <a href="{{ route('admin.flash-sales.index') }}" class="hover:text-slate-700">{{ __('Flash Sales') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form
    id="flash-sale-form"
    method="POST"
    x-data="flashSaleForm({ searchUrl: @js(route('admin.flash-sales.search-products')), initialItems: @js($initialItems) })"
    x-init="init()"
    @submit.prevent="AdminForm.submit('flash-sale-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.flash-sales.index')))"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('General')">
                <x-admin.lang-tabs>
                    <x-slot:en>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name[en]" value="{{ old('name.en', $flashSale->getTranslation('name', 'en')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm">
                            @error('name.en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </x-slot:en>
                    <x-slot:ar>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name[ar]" value="{{ old('name.ar', $flashSale->getTranslation('name', 'ar')) }}"
                                class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                            @error('name.ar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </x-slot:ar>
                </x-admin.lang-tabs>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Starts At') }}</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($flashSale->starts_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        @error('starts_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Ends At') }}</label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($flashSale->ends_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        @error('ends_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Items')">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-2 py-2 text-start w-1/4">{{ __('Product') }}</th>
                                <th class="px-2 py-2 text-start">{{ __('Variant') }}</th>
                                <th class="px-2 py-2 text-start w-32">{{ __('Discount Type') }}</th>
                                <th class="px-2 py-2 text-start w-28">{{ __('Discount') }}</th>
                                <th class="px-2 py-2 text-start w-24">{{ __('Max Qty') }}</th>
                                <th class="px-2 py-2 text-start w-28">{{ __('Preview') }}</th>
                                <th class="px-2 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in items" :key="row.key">
                                <tr class="border-t border-slate-100 align-top">
                                    <td class="px-2 py-2">
                                        <select class="product-search w-full" :data-row-index="index" x-init="initProductSelect($el, row)"></select>
                                        <input type="hidden" :name="'items['+index+'][product_id]'" :value="row.product_id">
                                        <p class="text-[11px] text-slate-400 mt-1" x-show="row.sku" x-text="row.sku"></p>
                                    </td>
                                    <td class="px-2 py-2">
                                        <select x-show="row.has_variants" x-model="row.variant_id" :name="row.has_variants ? ('items['+index+'][variant_id]') : ''" class="w-full rounded-lg border-slate-300 text-xs">
                                            <option value="">{{ __('Select variant') }}</option>
                                            <template x-for="variant in row.variants" :key="variant.id">
                                                <option :value="variant.id" x-text="variant.sku"></option>
                                            </template>
                                        </select>
                                        <span x-show="!row.has_variants" class="text-slate-300 text-xs">—</span>
                                    </td>
                                    <td class="px-2 py-2">
                                        <select x-model="row.discount_type" :name="'items['+index+'][discount_type]'" class="w-full rounded-lg border-slate-300 text-xs">
                                            <option value="percentage">{{ __('Percentage') }}</option>
                                            <option value="fixed">{{ __('Fixed') }}</option>
                                        </select>
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" min="0" step="0.01" :name="'items['+index+'][discount_value]'" x-model.number="row.discount_value" class="w-24 rounded-lg border-slate-300 text-xs">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" min="0" step="1" placeholder="0" :name="'items['+index+'][max_qty]'" x-model.number="row.max_qty" class="w-20 rounded-lg border-slate-300 text-xs">
                                    </td>
                                    <td class="px-2 py-2 text-sm font-medium text-slate-700" x-text="effectivePrice(row)"></td>
                                    <td class="px-2 py-2 text-end">
                                        <button type="button" @click="removeItem(index)" class="text-slate-400 hover:text-red-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!items.length">
                                <td colspan="7" class="px-2 py-6 text-center text-slate-400">{{ __('No items yet — search and add a product.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" @click="addItem()" class="mt-4 px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">
                    <i class="fa-solid fa-plus me-1"></i>{{ __('Add Item') }}
                </button>
                @error('items')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Status')">
                <x-admin.checkbox name="is_active" :checked="old('is_active', $flashSale->is_active ?? true)" :label="__('Active')" />
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Flash Sale') : __('Create Flash Sale') }}
                </button>
                <a href="{{ route('admin.flash-sales.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
    <script>
        function flashSaleForm({ searchUrl, initialItems }) {
            return {
                items: [],
                searchUrl,

                init() {
                    this.items = (initialItems || []).map((item) => ({ ...item }));
                },

                addItem() {
                    this.items.push({
                        key: Date.now() + Math.random(),
                        product_id: '',
                        text: '',
                        sku: '',
                        price: 0,
                        has_variants: false,
                        variants: [],
                        variant_id: '',
                        discount_type: 'percentage',
                        discount_value: 0,
                        max_qty: 0,
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                effectivePrice(row) {
                    const price = Number(row.price) || 0;
                    const value = Number(row.discount_value) || 0;
                    const discounted = row.discount_type === 'percentage'
                        ? price - (price * value / 100)
                        : price - value;

                    return Math.max(0, discounted).toFixed(2);
                },

                initProductSelect(el, row) {
                    $(el).select2({
                        width: '100%',
                        placeholder: @js(__('Search product or SKU')),
                        minimumInputLength: 2,
                        ajax: {
                            url: this.searchUrl,
                            dataType: 'json',
                            delay: 250,
                            data: (params) => ({ q: params.term }),
                            processResults: (data) => ({ results: data }),
                        },
                        templateResult: (product) => product.text || product.loading,
                        templateSelection: (product) => product.text || row.text || row.sku,
                    }).on('select2:select', (e) => {
                        const data = e.params.data;
                        row.product_id = data.id;
                        row.text = data.text;
                        row.sku = data.sku;
                        row.price = data.price;
                        row.has_variants = data.has_variants;
                        row.variants = data.variants || [];
                        row.variant_id = row.has_variants && row.variants.length === 1 ? row.variants[0].id : '';
                    });

                    if (row.product_id) {
                        const option = new Option(row.text, row.product_id, true, true);
                        $(el).append(option).trigger('change');
                    }
                },
            };
        }
    </script>
@endpush
