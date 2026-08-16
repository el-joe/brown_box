<?php

namespace App\Services\Ai\Contracts;

interface AiDriverInterface
{
    /**
     * @param array<int, array{role: string, content: string}> $messages
     * @param array<string, mixed> $options
     */
    public function chat(array $messages, string $model, array $options = []): string;

    public function generateImage(string $prompt, string $size = '1024x1024'): ?string;

    public function supportsImages(): bool;
}
