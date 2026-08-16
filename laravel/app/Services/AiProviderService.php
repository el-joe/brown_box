<?php

namespace App\Services;

use App\Models\AiProvider;
use Illuminate\Support\Facades\Cache;

class AiProviderService
{
    private const TTL = 3600;

    public function config(string $code): array
    {
        return $this->all()[$code]['config'] ?? [];
    }

    public function isActive(string $code): bool
    {
        return (bool) ($this->all()[$code]['is_active'] ?? false);
    }

    public function defaultModel(string $code): ?string
    {
        return $this->config($code)['default_model'] ?? null;
    }

    public function models(string $code): array
    {
        return $this->all()[$code]['available_models'] ?? [];
    }

    /**
     * @return array<string, string> code => label, active providers only.
     */
    public function activeProviders(): array
    {
        return collect($this->all())
            ->filter(fn (array $provider) => $provider['is_active'])
            ->mapWithKeys(fn (array $provider, string $code) => [$code => $provider['label']])
            ->all();
    }

    public function flush(): void
    {
        Cache::forget('ai_providers.all');
    }

    private function all(): array
    {
        return Cache::remember('ai_providers.all', self::TTL, function () {
            return AiProvider::query()->get()->keyBy('code')->map(fn (AiProvider $provider) => [
                'label' => $provider->label,
                'is_active' => (bool) $provider->is_active,
                'config' => (array) ($provider->config ?? []),
                'available_models' => (array) ($provider->available_models ?? []),
            ])->all();
        });
    }
}
