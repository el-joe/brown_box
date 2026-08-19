<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            // Tab 1 - Basic Info
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'name.en' => ['required', 'string', 'max:191'],
            'name.ar' => ['required', 'string', 'max:191'],
            'short_description.en' => ['nullable', 'string', 'max:500'],
            'short_description.ar' => ['nullable', 'string', 'max:500'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_hero' => ['boolean'],
            'is_digital' => ['boolean'],

            // Tab 2 - Pricing
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'gte:price'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],

            // Tab 3 - Images
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:2048'],
            'existing_image_ids' => ['nullable', 'json'],
            'primary_existing_image_id' => ['nullable', 'integer'],
            'primary_new_image_index' => ['nullable', 'integer', 'min:0'],
            'video_url' => ['nullable', 'url', 'max:500'],

            // Tab 4 - Variants
            'has_variants' => ['boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.barcode' => ['nullable', 'string', 'max:100'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.compare_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.is_active' => ['boolean'],
            'variants.*.image' => ['nullable', 'image', 'max:2048'],
            'variants.*.attribute_values' => ['nullable', 'array'],
            'variants.*.attribute_values.*' => ['integer', 'exists:product_attribute_values,id'],

            // Tab 5 - SEO
            'meta_title.en' => ['nullable', 'string', 'max:191'],
            'meta_title.ar' => ['nullable', 'string', 'max:191'],
            'meta_description.en' => ['nullable', 'string', 'max:500'],
            'meta_description.ar' => ['nullable', 'string', 'max:500'],
            'meta_keywords.en' => ['nullable', 'string', 'max:500'],
            'meta_keywords.ar' => ['nullable', 'string', 'max:500'],
            'og_title' => ['nullable', 'string', 'max:191'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'image', 'max:2048'],
            'robots' => ['nullable', Rule::in(['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'])],
            'canonical_url' => ['nullable', 'url', 'max:500'],
            'schema_json' => ['nullable', 'json'],
        ];
    }
}
