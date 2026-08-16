<?php

namespace App\Services\Ai\Drivers;

use App\Services\Ai\Contracts\AiDriverInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiDriver implements AiDriverInterface
{
    public function __construct(private readonly string $apiKey)
    {
    }

    public function chat(array $messages, string $model, array $options = []): string
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', array_merge([
                'model' => $model,
                'messages' => $messages,
            ], $options));

        if ($response->failed()) {
            throw new RuntimeException('OpenAI request failed: '.$response->body());
        }

        return trim((string) $response->json('choices.0.message.content'));
    }

    public function generateImage(string $prompt, string $size = '1024x1024'): ?string
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'size' => $size,
                'n' => 1,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI image request failed: '.$response->body());
        }

        return $response->json('data.0.url');
    }

    public function supportsImages(): bool
    {
        return true;
    }
}
