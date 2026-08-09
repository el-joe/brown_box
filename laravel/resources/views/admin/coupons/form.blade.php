@extends('admin.layouts.app')

@php
    $isEdit = $coupon->exists;
    $validateUrl = $isEdit ? route('admin.coupons.update.validate', $coupon) : route('admin.coupons.validate');
    $submitUrl = $isEdit ? route('admin.coupons.update', $coupon) : route('admin.coupons.store');
    $applyTo = old('apply_to', $coupon->categories?->isNotEmpty() ? 'specific_categories' : ($coupon->products?->isNotEmpty() ? 'specific_products' : 'all'));
@endphp

@section('title', $isEdit ? __('Edit Coupon') : __('Create Coupon'))

@section('breadcrumb')
    <a href="{{ route('admin.coupons.index') }}" class="hover:text-slate-700">{{ __('Coupons') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form
    id="coupon-form"
    method="POST"
    x-data="{ type: @js(old('type', $coupon->type ?? 'percentage')), applyTo: @js($applyTo) }"
    @submit.prevent="AdminForm.submit('coupon-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.coupons.index')))"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('Coupon Details')">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="admin-field md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Code') }}</label>
                        <div class="flex gap-2">
                            <input id="code-input" type="text" name="code" value="{{ old('code', $coupon->code) }}" class="w-full rounded-lg border-slate-300 text-sm uppercase">
                            <button type="button" onclick="generateCouponCode()" class="px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50 whitespace-nowrap">
                                <i class="fa-solid fa-dice me-1"></i>{{ __('Generate') }}
                            </button>
                        </div>
                        @error('code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Type') }}</label>
                        <select name="type" x-model="type" class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="free_shipping" @selected(old('type', $coupon->type ?? '') === 'free_shipping')>{{ __('Free Shipping') }}</option>
                            <option value="percentage" @selected(old('type', $coupon->type ?? 'percentage') === 'percentage')>{{ __('Percentage') }}</option>
                            <option value="fixed" @selected(old('type', $coupon->type ?? '') === 'fixed')>{{ __('Fixed Amount') }}</option>
                        </select>
                        @error('type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="admin-field" x-show="type === 'percentage'" x-cloak>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Discount %') }}</label>
                        <input type="number" min="0" max="100" step="0.01" name="value" value="{{ old('value', $coupon->type === 'percentage' ? $coupon->value : '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>

                    <div class="admin-field" x-show="type === 'fixed'" x-cloak>
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Discount Amount (EGP)') }}</label>
                        <input type="number" min="0" step="0.01" name="value" value="{{ old('value', $coupon->type === 'fixed' ? $coupon->value : '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    @error('value')<p class="text-xs text-red-600 mt-1 md:col-span-2">{{ $message }}</p>@enderror

                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Min Order Amount') }}</label>
                        <input type="number" min="0" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        @error('min_order_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Max Uses (0 = unlimited)') }}</label>
                        <input type="number" min="0" step="1" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        @error('max_uses')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Starts At') }}</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        @error('starts_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Expires At') }}</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        @error('expires_at')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Apply To')">
                <div class="admin-field">
                    <select name="apply_to" x-model="applyTo" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="all" @selected($applyTo === 'all')>{{ __('All Products') }}</option>
                        <option value="specific_products" @selected($applyTo === 'specific_products')>{{ __('Specific Products') }}</option>
                        <option value="specific_categories" @selected($applyTo === 'specific_categories')>{{ __('Specific Categories') }}</option>
                    </select>
                </div>

                <div class="admin-field mt-4" x-show="applyTo === 'specific_products'" x-cloak>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Products') }}</label>
                    <x-admin.select
                        name="product_ids"
                        multiple
                        :options="$products"
                        :selected="old('product_ids', $coupon->products?->pluck('id')->all() ?? [])"
                        :placeholder="__('Select products')"
                    />
                    @error('product_ids')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="admin-field mt-4" x-show="applyTo === 'specific_categories'" x-cloak>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Categories') }}</label>
                    <x-admin.select
                        name="category_ids"
                        multiple
                        :options="$categories"
                        :selected="old('category_ids', $coupon->categories?->pluck('id')->all() ?? [])"
                        :placeholder="__('Select categories')"
                    />
                    @error('category_ids')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Status')">
                <x-admin.checkbox name="is_active" :checked="old('is_active', $coupon->is_active ?? true)" :label="__('Active')" />
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Coupon') : __('Create Coupon') }}
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
    <script>
        async function generateCouponCode() {
            const response = await fetch(@js(route('admin.coupons.generate-code')));
            const data = await response.json();
            document.getElementById('code-input').value = data.code;
        }
    </script>
@endpush
