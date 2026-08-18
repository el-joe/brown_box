<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name.ar' => ['required', 'string', 'max:100'],
            'name.en' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'max:1024'],
            'is_active' => ['boolean'],
            'seo.title.en' => ['nullable', 'string', 'max:255'],
            'seo.title.ar' => ['nullable', 'string', 'max:255'],
            'seo.description.en' => ['nullable', 'string', 'max:500'],
            'seo.description.ar' => ['nullable', 'string', 'max:500'],
            'seo.keywords.en' => ['nullable', 'string', 'max:500'],
            'seo.keywords.ar' => ['nullable', 'string', 'max:500'],
            'seo.og_title.en' => ['nullable', 'string', 'max:255'],
            'seo.og_title.ar' => ['nullable', 'string', 'max:255'],
            'seo.og_description.en' => ['nullable', 'string', 'max:500'],
            'seo.og_description.ar' => ['nullable', 'string', 'max:500'],
            'seo.og_image' => ['nullable', 'image', 'max:2048'],
            'seo.canonical_url' => ['nullable', 'url', 'max:500'],
            'seo.robots' => ['nullable', 'string'],
            'seo.schema_json' => ['nullable', 'string'],
        ];
    }
}
