<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $suggestionId = $this->route('search_suggestion');

        return [
            'keyword' => [
                'required',
                'string',
                'max:150',
                Rule::unique('search_suggestions', 'keyword')->ignore($suggestionId),
            ],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
