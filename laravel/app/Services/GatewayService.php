<?php

namespace App\Services;

use App\Models\Gateway;
use Illuminate\Support\Facades\Cache;

class GatewayService
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

    public function flush(): void
    {
        Cache::forget('gateways.all');
    }

    private function all(): array
    {
        return Cache::remember('gateways.all', self::TTL, function () {
            return Gateway::query()->get()->keyBy('code')->map(fn ($g) => [
                'is_active' => (bool) $g->is_active,
                'config'    => (array) ($g->config ?? []),
            ])->all();
        });
    }
}
