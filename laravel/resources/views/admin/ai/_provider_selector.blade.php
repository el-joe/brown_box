@php
    $activeProviders = $activeProviders ?? [];
@endphp

@if (empty($activeProviders))
    <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 mb-4 flex items-center justify-between">
        <span><i class="fa-solid fa-triangle-exclamation me-2"></i>{{ __('No active AI providers. Configure one to use AI tools.') }}</span>
        <a href="{{ route('admin.ai.settings') }}" class="font-medium underline">{{ __('Go to Settings') }}</a>
    </div>
@else
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="admin-field">
            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Provider') }}</label>
            <select x-model="provider" @change="fetchModels()" class="w-full rounded-lg border-slate-300 text-sm">
                @foreach ($activeProviders as $code => $label)
                    <option value="{{ $code }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="admin-field">
            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Model') }}</label>
            <select x-model="model" class="w-full rounded-lg border-slate-300 text-sm">
                <template x-for="m in models" :key="m">
                    <option :value="m" x-text="m"></option>
                </template>
            </select>
        </div>
    </div>
@endif

@once
    @push('scripts')
        <script>
            function aiProviderSelector(defaultProvider = '') {
                return {
                    provider: defaultProvider,
                    model: '',
                    models: [],
                    fetchModels() {
                        if (!this.provider) return;
                        fetch(`{{ route('admin.ai.models') }}?provider=${encodeURIComponent(this.provider)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        })
                            .then(r => r.json())
                            .then(data => {
                                this.models = data.models || [];
                                this.model = data.default_model || (this.models[0] ?? '');
                            });
                    },
                    init() {
                        if (this.provider) this.fetchModels();
                    },
                };
            }
        </script>
    @endpush
@endonce
