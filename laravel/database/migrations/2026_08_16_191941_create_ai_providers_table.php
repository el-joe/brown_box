<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->boolean('is_active')->default(false);
            $table->json('config')->nullable();
            $table->json('available_models')->nullable();
            $table->timestamps();
        });

        \App\Models\AiProvider::query()->insert([
            [
                'code' => 'openai',
                'label' => 'OpenAI',
                'is_active' => false,
                'config' => json_encode([
                    'api_key' => '',
                    'default_model' => 'gpt-4o-mini',
                ]),
                'available_models' => json_encode([
                    'gpt-4o',
                    'gpt-4o-mini',
                    'gpt-4-turbo',
                    'gpt-4.1',
                    'gpt-4.1-mini',
                    'o3-mini',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'openrouter',
                'label' => 'OpenRouter',
                'is_active' => false,
                'config' => json_encode([
                    'api_key' => '',
                    'default_model' => 'openai/gpt-4o-mini',
                    'site_url' => '',
                    'site_name' => 'Brown Box',
                ]),
                'available_models' => json_encode([
                    'openai/gpt-4o',
                    'openai/gpt-4o-mini',
                    'anthropic/claude-3.5-sonnet',
                    'anthropic/claude-3-haiku',
                    'google/gemini-pro-1.5',
                    'meta-llama/llama-3.1-70b-instruct',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
