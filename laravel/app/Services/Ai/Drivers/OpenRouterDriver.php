<?php

namespace App\Services\Ai\Drivers;

use App\Services\Ai\Contracts\AiDriverInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenRouterDriver implements AiDriverInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $siteUrl = '',
        private readonly string $siteName = 'Brown Box',
    ) {
    }

    public function chat(array $messages, string $model, array $options = []): string
    {
        $headers = array_filter([
            'HTTP-Referer' => $this->siteUrl,
            'X-Title' => $this->siteName,
        ]);

        $response = Http::withToken($this->apiKey)
            ->withHeaders($headers)
            ->timeout(60)
            ->post('https://openrouter.ai/api/v1/chat/completions', array_merge([
                'model' => $model,
                'messages' => $messages,
            ], $options));

        if ($response->failed()) {
            throw new RuntimeException('OpenRouter request failed: '.$response->body());
        }

        return trim((string) $response->json('choices.0.message.content'));
    }

    public function generateImage(string $prompt, string $size = '1024x1024'): ?string
    {
        return null;
    }

    public function supportsImages(): bool
    {
        return false;
    }
}
