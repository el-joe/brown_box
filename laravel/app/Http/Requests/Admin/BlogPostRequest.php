<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['required', 'string', 'max:255'],
            'content.ar' => ['required', 'string'],
            'content.en' => ['required', 'string'],
            'excerpt.ar' => ['nullable', 'string', 'max:500'],
            'excerpt.en' => ['nullable', 'string', 'max:500'],
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
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
