<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaticPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pageId = $this->route('static_page');

        return [
            'title.ar' => ['required', 'string', 'max:150'],
            'title.en' => ['required', 'string', 'max:150'],
            'content.ar' => ['nullable', 'string'],
            'content.en' => ['nullable', 'string'],
            'slug' => [
                'required',
                'string',
                'max:150',
                'alpha_dash',
                Rule::unique('static_pages', 'slug')->ignore($pageId),
            ],
            'is_active' => ['boolean'],
        ];
    }
}
