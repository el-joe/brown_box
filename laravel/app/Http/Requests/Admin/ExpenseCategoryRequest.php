<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('expense_category');

        return [
            'name.ar' => ['required', 'string', 'max:100'],
            'name.en' => ['required', 'string', 'max:100'],
            'parent_id' => [
                'nullable',
                Rule::exists('expense_categories', 'id')->whereNot('id', $categoryId),
            ],
            'type' => ['required', Rule::in(['debit', 'credit'])],
            'is_active' => ['boolean'],
        ];
    }
}
