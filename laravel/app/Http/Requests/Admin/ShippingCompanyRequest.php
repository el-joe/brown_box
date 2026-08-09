<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'max:1024'],
            'is_active' => ['boolean'],
            'rates' => ['array'],
            'rates.*.governorate_id' => ['required', 'integer', 'exists:governorates,id'],
            'rates.*.city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'rates.*.price' => ['required', 'numeric', 'min:0'],
            'rates.*.estimated_days' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
