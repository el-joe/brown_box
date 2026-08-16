<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->routeIs('admin.banners.store') || $this->routeIs('admin.banners.validate');

        return [
            'title.ar' => ['required', 'string', 'max:150'],
            'title.en' => ['required', 'string', 'max:150'],
            'image' => [$isCreate ? 'required' : 'nullable', 'image', 'max:2048'],
            'type' => ['required', 'in:product,category,external'],
            'url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
