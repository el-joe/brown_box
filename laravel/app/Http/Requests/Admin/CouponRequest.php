<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon');

        return [
            'code' => [
                'required', 'string', 'max:50',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'type' => ['required', Rule::in(['free_shipping', 'percentage', 'fixed'])],
            'value' => [Rule::requiredIf(in_array($this->input('type'), ['percentage', 'fixed'], true)), 'nullable', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'apply_to' => ['required', Rule::in(['all', 'specific_products', 'specific_categories'])],
            'product_ids' => ['required_if:apply_to,specific_products', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'category_ids' => ['required_if:apply_to,specific_categories', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'is_active' => ['boolean'],
        ];
    }
}
