<?php

namespace App\Services;

use App\Services\Ai\Contracts\AiDriverInterface;
use App\Services\Ai\Drivers\OpenAiDriver;
use App\Services\Ai\Drivers\OpenRouterDriver;
use RuntimeException;

class AiService
{
    public function __construct(private readonly AiProviderService $providers)
    {
    }

    public function driver(string $providerCode): AiDriverInterface
    {
        if (! $this->providers->isActive($providerCode)) {
            throw new RuntimeException("AI provider [{$providerCode}] is not active.");
        }

        $config = $this->providers->config($providerCode);
        $apiKey = (string) ($config['api_key'] ?? '');

        if ($apiKey === '') {
            throw new RuntimeException("AI provider [{$providerCode}] is missing an API key.");
        }

        return match ($providerCode) {
            'openai' => new OpenAiDriver($apiKey),
            'openrouter' => new OpenRouterDriver(
                $apiKey,
                (string) ($config['site_url'] ?? ''),
                (string) ($config['site_name'] ?? 'Brown Box'),
            ),
            default => throw new RuntimeException("Unknown AI provider [{$providerCode}]."),
        };
    }

    public function chat(string $providerCode, string $model, array $messages, array $options = []): string
    {
        return $this->driver($providerCode)->chat($messages, $model, $options);
    }

    public function generateImage(string $providerCode, string $prompt, string $size = '1024x1024'): ?string
    {
        $driver = $this->driver($providerCode);

        if (! $driver->supportsImages()) {
            throw new RuntimeException("AI provider [{$providerCode}] does not support image generation.");
        }

        return $driver->generateImage($prompt, $size);
    }

    public function activeProviders(): array
    {
        return $this->providers->activeProviders();
    }

    public function modelsFor(string $providerCode): array
    {
        return $this->providers->models($providerCode);
    }

    public function defaultModel(string $providerCode): ?string
    {
        return $this->providers->defaultModel($providerCode);
    }
}
