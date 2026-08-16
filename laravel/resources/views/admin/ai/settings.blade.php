@extends('admin.layouts.app')

@section('title', __('AI Settings'))

@section('breadcrumb')
    <a href="{{ route('admin.ai.dashboard') }}">{{ __('AI Module') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Settings') }}</span>
@endsection

@section('content')
    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach ($providers as $provider)
            @php $config = $provider->config ?? []; @endphp

            <x-admin.card :title="$provider->label">
                <form method="POST" action="{{ route('admin.ai.settings.update') }}"
                    x-data="{ active: {{ old('providers.'.$provider->code.'.is_active', $provider->is_active) ? 'true' : 'false' }}, showKey: false }">
                    @csrf

                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-slate-500">{{ __('Status') }}</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="providers[{{ $provider->code }}][is_active]" value="1" x-model="active" class="sr-only peer">
                            <div class="w-10 h-6 bg-slate-200 rounded-full peer-checked:bg-emerald-500 transition-colors"></div>
                            <div class="absolute w-4 h-4 bg-white rounded-full shadow left-1 top-1 transition-transform peer-checked:translate-x-4"></div>
                        </label>
                    </div>

                    <div class="space-y-3">
                        <div class="admin-field">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('API Key') }}</label>
                            <div class="relative">
                                <input :type="showKey ? 'text' : 'password'" name="providers[{{ $provider->code }}][config][api_key]"
                                    value="{{ old('providers.'.$provider->code.'.config.api_key', $config['api_key'] ?? '') }}"
                                    class="w-full rounded-lg border-slate-300 text-sm pe-10">
                                <button type="button" @click="showKey = !showKey" class="absolute inset-y-0 end-2 flex items-center text-slate-400">
                                    <i class="fa-solid" :class="showKey ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <div class="admin-field">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Default Model') }}</label>
                            <input type="text" name="providers[{{ $provider->code }}][config][default_model]"
                                value="{{ old('providers.'.$provider->code.'.config.default_model', $config['default_model'] ?? '') }}"
                                class="w-full rounded-lg border-slate-300 text-sm">
                        </div>

                        @if ($provider->code === 'openrouter')
                            <div class="admin-field">
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Site URL') }}</label>
                                <input type="text" name="providers[{{ $provider->code }}][config][site_url]"
                                    value="{{ old('providers.'.$provider->code.'.config.site_url', $config['site_url'] ?? '') }}"
                                    class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div class="admin-field">
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Site Name') }}</label>
                                <input type="text" name="providers[{{ $provider->code }}][config][site_name]"
                                    value="{{ old('providers.'.$provider->code.'.config.site_name', $config['site_name'] ?? '') }}"
                                    class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                        @endif

                        <div class="admin-field">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Available Models (comma separated)') }}</label>
                            <textarea name="providers[{{ $provider->code }}][available_models]" rows="3"
                                class="w-full rounded-lg border-slate-300 text-sm">{{ old('providers.'.$provider->code.'.available_models', implode(', ', $provider->available_models ?? [])) }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="mt-4 w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                        {{ __('Save') }}
                    </button>
                </form>
            </x-admin.card>
        @endforeach
    </div>
@endsection
