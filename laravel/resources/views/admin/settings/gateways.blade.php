@extends('admin.layouts.app')

@section('title', __('Payment Gateways'))

@section('breadcrumb')
    <span>{{ __('Payment Gateways') }}</span>
@endsection

@php
    $labels = [
        'paymob' => __('Paymob'),
        'bank_transfer' => __('Bank Transfer'),
        'vodafone_cash' => __('Vodafone Cash'),
        'instapay' => __('InstaPay'),
    ];
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach ($labels as $code => $label)
            @php $gateway = $gateways[$code]; $config = $gateway->config ?? []; @endphp

            <x-admin.card :title="$label">
                <form id="gateway-form-{{ $code }}" method="POST" action="{{ route('admin.gateways.update', $gateway) }}" x-data="{ active: {{ old('is_active', $gateway->is_active) ? 'true' : 'false' }} }">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-slate-500">{{ __('Status') }}</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="active" class="sr-only peer">
                            <div class="w-10 h-6 bg-slate-200 rounded-full peer-checked:bg-emerald-500 transition-colors"></div>
                            <div class="absolute w-4 h-4 bg-white rounded-full shadow left-1 top-1 transition-transform peer-checked:translate-x-4"></div>
                        </label>
                    </div>

                    <div class="space-y-3">
                        @if ($code === 'paymob')
                            <div class="admin-field">
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('API Key') }}</label>
                                <input type="text" name="api_key" value="{{ old('api_key', $config['api_key'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Integration ID') }}</label>
                                <input type="text" name="integration_id" value="{{ old('integration_id', $config['integration_id'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('HMAC Secret') }}</label>
                                <input type="text" name="hmac_secret" value="{{ old('hmac_secret', $config['hmac_secret'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Iframe ID') }}</label>
                                <input type="text" name="iframe_id" value="{{ old('iframe_id', $config['iframe_id'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                        @elseif ($code === 'bank_transfer')
                            <div class="grid grid-cols-2 gap-3">
                                <div class="admin-field">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Bank Name') }}</label>
                                    <input type="text" name="bank_name" value="{{ old('bank_name', $config['bank_name'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Account Name') }}</label>
                                    <input type="text" name="account_name" value="{{ old('account_name', $config['account_name'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Account Number') }}</label>
                                    <input type="text" name="account_number" value="{{ old('account_number', $config['account_number'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('IBAN') }}</label>
                                    <input type="text" name="iban" value="{{ old('iban', $config['iban'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                            </div>
                            <x-admin.lang-tabs>
                                <x-slot:en>
                                    <div class="admin-field">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Instructions (EN)') }}</label>
                                        <textarea name="instructions[en]" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('instructions.en', $config['instructions']['en'] ?? '') }}</textarea>
                                    </div>
                                </x-slot:en>
                                <x-slot:ar>
                                    <div class="admin-field">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Instructions (AR)') }}</label>
                                        <textarea name="instructions[ar]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('instructions.ar', $config['instructions']['ar'] ?? '') }}</textarea>
                                    </div>
                                </x-slot:ar>
                            </x-admin.lang-tabs>
                        @elseif ($code === 'vodafone_cash')
                            <div class="grid grid-cols-2 gap-3">
                                <div class="admin-field">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Number') }}</label>
                                    <input type="text" name="number" value="{{ old('number', $config['number'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="admin-field">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                                    <input type="text" name="name" value="{{ old('name', $config['name'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                            </div>
                            <x-admin.lang-tabs>
                                <x-slot:en>
                                    <div class="admin-field">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Instructions (EN)') }}</label>
                                        <textarea name="instructions[en]" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('instructions.en', $config['instructions']['en'] ?? '') }}</textarea>
                                    </div>
                                </x-slot:en>
                                <x-slot:ar>
                                    <div class="admin-field">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Instructions (AR)') }}</label>
                                        <textarea name="instructions[ar]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('instructions.ar', $config['instructions']['ar'] ?? '') }}</textarea>
                                    </div>
                                </x-slot:ar>
                            </x-admin.lang-tabs>
                        @elseif ($code === 'instapay')
                            <div class="admin-field">
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Number / Address') }}</label>
                                <input type="text" name="address" value="{{ old('address', $config['address'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <x-admin.lang-tabs>
                                <x-slot:en>
                                    <div class="admin-field">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Instructions (EN)') }}</label>
                                        <textarea name="instructions[en]" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('instructions.en', $config['instructions']['en'] ?? '') }}</textarea>
                                    </div>
                                </x-slot:en>
                                <x-slot:ar>
                                    <div class="admin-field">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Instructions (AR)') }}</label>
                                        <textarea name="instructions[ar]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('instructions.ar', $config['instructions']['ar'] ?? '') }}</textarea>
                                    </div>
                                </x-slot:ar>
                            </x-admin.lang-tabs>
                        @endif
                    </div>

                    <button type="submit" class="mt-4 w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                        {{ __('Save') }}
                    </button>
                </form>
            </x-admin.card>
        @endforeach
    </div>
@endsection
