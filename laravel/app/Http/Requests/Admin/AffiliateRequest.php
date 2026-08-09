<?php

namespace App\Http\Requests\Admin;

use App\Enums\CommissionTierType;
use App\Enums\CommissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AffiliateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $affiliateId = $this->route('affiliate');

        return [
            'user_id' => ['required_without:new_customer', 'nullable', 'integer', 'exists:customers,id'],
            'new_customer' => ['nullable', 'array'],
            'new_customer.name' => ['required_with:new_customer', 'nullable', 'string', 'max:191'],
            'new_customer.email' => ['required_with:new_customer', 'nullable', 'email', 'max:191', 'unique:customers,email'],
            'new_customer.phone' => ['nullable', 'string', 'max:30'],

            'code' => [
                'required', 'string', 'max:50',
                Rule::unique('affiliates', 'code')->ignore($affiliateId),
            ],
            'commission_type' => ['required', Rule::in(array_column(CommissionType::cases(), 'value'))],
            'fixed_commission_rate' => [
                Rule::requiredIf($this->input('commission_type') === CommissionType::FixedAllOrders->value),
                'nullable', 'numeric', 'min:0', 'max:100',
            ],

            'categories' => ['nullable', 'array'],
            'categories.*.category_id' => ['required', 'integer', 'exists:categories,id'],
            'categories.*.tier_type' => ['required', Rule::in(array_column(CommissionTierType::cases(), 'value'))],
            'categories.*.rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'categories.*.tiers' => ['nullable', 'array'],
            'categories.*.tiers.*.min_amount' => ['required', 'numeric', 'min:0'],
            'categories.*.tiers.*.max_amount' => ['nullable', 'numeric', 'gte:categories.*.tiers.*.min_amount'],
            'categories.*.tiers.*.rate' => ['required', 'numeric', 'min:0', 'max:100'],

            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'approved_at' => ['nullable', 'date'],
        ];
    }
}
