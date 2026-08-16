<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductAttributeRequest extends FormRequest
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
            'values' => ['nullable', 'array'],
            'values.*.id' => ['nullable', 'integer'],
            'values.*.value.ar' => ['required', 'string', 'max:100'],
            'values.*.value.en' => ['required', 'string', 'max:100'],
            'values.*.extra_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
