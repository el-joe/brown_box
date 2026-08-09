@extends('admin.layouts.app')

@php
    $isEdit = $affiliate->exists;
    $validateUrl = $isEdit ? route('admin.affiliates.update.validate', $affiliate) : route('admin.affiliates.validate');
    $submitUrl = $isEdit ? route('admin.affiliates.update', $affiliate) : route('admin.affiliates.store');
@endphp

@section('title', $isEdit ? __('Edit Affiliate') : __('Create Affiliate'))

@section('breadcrumb')
    <a href="{{ route('admin.affiliates.index') }}" class="hover:text-slate-700">{{ __('Affiliates') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $isEdit ? __('Edit') : __('Create') }}</span>
@endsection

@section('content')
<form
    id="affiliate-form"
    method="POST"
    x-data="affiliateForm({
        commissionType: @js(old('commission_type', $affiliate->commission_type ?? 'fixed_all_orders')),
        categories: @js(old('categories', $existingCategories)),
    })"
    @submit.prevent="submit(@js($validateUrl), @js($submitUrl), @js(route('admin.affiliates.index')))"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('Customer')">
                @unless ($isEdit)
                    <div class="admin-field mb-3" x-data="{ mode: 'existing' }">
                        <div class="flex items-center gap-4 mb-3 text-sm">
                            <label class="inline-flex items-center gap-1"><input type="radio" x-model="mode" value="existing" checked> {{ __('Existing customer') }}</label>
                            <label class="inline-flex items-center gap-1"><input type="radio" x-model="mode" value="new"> {{ __('New customer') }}</label>
                        </div>

                        <div x-show="mode === 'existing'" x-cloak>
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Select Customer') }}</label>
                            <x-admin.select name="user_id" :options="$customers" :selected="old('user_id')" :placeholder="__('Search customer...')" />
                            @error('user_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div x-show="mode === 'new'" x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name') }}</label>
                                <input type="text" name="new_customer[name]" value="{{ old('new_customer.name') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                @error('new_customer.name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Email') }}</label>
                                <input type="email" name="new_customer[email]" value="{{ old('new_customer.email') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                @error('new_customer.email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="admin-field">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Phone') }}</label>
                                <input type="text" name="new_customer[phone]" value="{{ old('new_customer.phone') }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-sm">
                        <div class="font-medium text-slate-800">{{ $affiliate->customer?->name }}</div>
                        <div class="text-slate-500">{{ $affiliate->customer?->email }}</div>
                    </div>
                    <input type="hidden" name="user_id" value="{{ $affiliate->user_id }}">
                @endunless
            </x-admin.card>

            <x-admin.card :title="__('Affiliate Code')">
                <div class="flex gap-2">
                    <input id="code-input" type="text" name="code" value="{{ old('code', $affiliate->code) }}" class="w-full rounded-lg border-slate-300 text-sm uppercase">
                    <button type="button" onclick="generateAffiliateCode()" class="px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50 whitespace-nowrap">
                        <i class="fa-solid fa-dice me-1"></i>{{ __('Generate') }}
                    </button>
                </div>
                @error('code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </x-admin.card>

            <x-admin.card :title="__('Commission Structure')">
                <div class="admin-field mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Commission Type') }}</label>
                    <select name="commission_type" x-model="commissionType" class="w-full rounded-lg border-slate-300 text-sm">
                        @foreach ($commissionTypes as $type)
                            <option value="{{ $type->value }}" @selected(old('commission_type', $affiliate->commission_type ?? 'fixed_all_orders') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    @error('commission_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="admin-field" x-show="commissionType === 'fixed_all_orders'" x-cloak>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Fixed Rate (%)') }}</label>
                    <input type="number" min="0" max="100" step="0.01" name="fixed_commission_rate" value="{{ old('fixed_commission_rate', $affiliate->fixed_commission_rate) }}" class="w-full rounded-lg border-slate-300 text-sm md:w-64">
                    @error('fixed_commission_rate')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div x-show="commissionType === 'per_category'" x-cloak class="space-y-4">
                    <template x-for="(row, index) in categories" :key="index">
                        <div class="border border-slate-200 rounded-lg p-4 space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Category') }}</label>
                                    <select :name="`categories[${index}][category_id]`" x-model="row.category_id" class="w-full rounded-lg border-slate-300 text-sm">
                                        <option value="">{{ __('Select category') }}</option>
                                        @foreach ($categories as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Tier Type') }}</label>
                                    <select :name="`categories[${index}][tier_type]`" x-model="row.tier_type" class="w-full rounded-lg border-slate-300 text-sm">
                                        @foreach ($tierTypes as $type)
                                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex-1" x-show="row.tier_type === 'fixed_percentage'">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Rate (%)') }}</label>
                                    <input type="number" min="0" max="100" step="0.01" :name="`categories[${index}][rate]`" x-model.number="row.rate" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <button type="button" @click="categories.splice(index, 1)" class="mt-5 text-red-500 hover:text-red-700">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>

                            <div x-show="row.tier_type === 'tiered'" class="ps-2 border-s-2 border-slate-100 space-y-2">
                                <div class="text-xs font-medium text-slate-500">{{ __('Tiers (based on order total)') }}</div>
                                <template x-for="(tier, tIndex) in row.tiers" :key="tIndex">
                                    <div class="flex items-center gap-2">
                                        <input type="number" min="0" step="0.01" :name="`categories[${index}][tiers][${tIndex}][min_amount]`" x-model.number="tier.min_amount" placeholder="{{ __('Min amount') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                        <input type="number" min="0" step="0.01" :name="`categories[${index}][tiers][${tIndex}][max_amount]`" x-model.number="tier.max_amount" placeholder="{{ __('Max amount (optional)') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                        <input type="number" min="0" max="100" step="0.01" :name="`categories[${index}][tiers][${tIndex}][rate]`" x-model.number="tier.rate" placeholder="{{ __('Rate (%)') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                        <button type="button" @click="row.tiers.splice(tIndex, 1)" class="text-red-500 hover:text-red-700">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="row.tiers.push({ min_amount: 0, max_amount: null, rate: 0 })" class="text-xs text-amber-600 hover:underline">
                                    <i class="fa-solid fa-plus me-1"></i>{{ __('Add Tier') }}
                                </button>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addCategory()" class="px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">
                        <i class="fa-solid fa-plus me-1"></i>{{ __('Add Category') }}
                    </button>
                    @error('categories')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Opening Balance')">
                <input type="number" min="0" step="0.01" name="opening_balance" value="{{ old('opening_balance', $affiliate->balance ?? 0) }}" class="w-full rounded-lg border-slate-300 text-sm" @disabled($isEdit)>
                @if ($isEdit)
                    <p class="text-xs text-slate-400 mt-1">{{ __('Opening balance can only be set on creation.') }}</p>
                @endif
            </x-admin.card>

            <x-admin.card :title="__('Status')">
                <x-admin.checkbox name="is_active" :checked="old('is_active', $affiliate->is_active ?? true)" :label="__('Active')" />

                <div class="admin-field mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Approved At') }}</label>
                    <input type="datetime-local" name="approved_at" value="{{ old('approved_at', optional($affiliate->approved_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Affiliate') : __('Create Affiliate') }}
                </button>
                <a href="{{ route('admin.affiliates.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
    <script>
        async function generateAffiliateCode() {
            const response = await fetch(@js(route('admin.affiliates.generate-code')));
            const data = await response.json();
            document.getElementById('code-input').value = data.code;
        }

        function affiliateForm({ commissionType, categories }) {
            return {
                commissionType,
                categories: categories && categories.length ? categories : [],

                addCategory() {
                    this.categories.push({ category_id: '', tier_type: 'fixed_percentage', rate: 0, tiers: [] });
                },

                submit(validateUrl, submitUrl, redirectUrl) {
                    AdminForm.submit('affiliate-form', validateUrl, submitUrl, () => window.location = redirectUrl);
                },
            };
        }
    </script>
@endpush
