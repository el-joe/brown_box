<?php

namespace App\Http\Requests\Admin;

use App\Enums\Robots;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeoPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title.ar' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'description.ar' => ['nullable', 'string', 'max:500'],
            'description.en' => ['nullable', 'string', 'max:500'],
            'keywords.ar' => ['nullable', 'string', 'max:255'],
            'keywords.en' => ['nullable', 'string', 'max:255'],
            'og_title.ar' => ['nullable', 'string', 'max:255'],
            'og_title.en' => ['nullable', 'string', 'max:255'],
            'og_description.ar' => ['nullable', 'string', 'max:500'],
            'og_description.en' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'max:2048'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots' => ['required', Rule::enum(Robots::class)],
            'schema_json' => ['nullable', 'json'],
        ];
    }
}
