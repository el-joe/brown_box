@php
    $isEdit = $product->exists;
    $validateUrl = $isEdit ? route('admin.products.update.validate', $product) : route('admin.products.validate');
    $submitUrl = $isEdit ? route('admin.products.update', $product) : route('admin.products.store');
    $seo = $product->seoPage;

    $existingImages = $product->exists
        ? $product->productImages->map(fn ($img) => [
            'id' => $img->id,
            'url' => $img->type === 'video' ? $img->path : asset_url($img->path),
            'type' => $img->type,
            'is_primary' => $img->is_primary,
        ])->values()
        : collect();

    $attributesData = $attributes->map(fn ($attr) => [
        'id' => $attr->id,
        'name' => $attr->getTranslation('name', app()->getLocale()),
        'values' => $attr->values->map(fn ($v) => ['id' => $v->id, 'value' => $v->getTranslation('value', app()->getLocale())])->values(),
    ]);

    $existingVariants = $product->exists
        ? $product->variants->map(fn ($v) => [
            'id' => $v->id,
            'sku' => $v->sku,
            'barcode' => $v->barcode,
            'price' => $v->price,
            'compare_price' => $v->compare_price,
            'cost_price' => $v->cost_price,
            'image' => $v->image ? asset_url($v->image) : null,
            'is_active' => $v->is_active,
            'attribute_values' => $v->variantAttributes->pluck('product_attribute_value_id')->values(),
            'label' => $v->variantAttributes->map(fn ($va) => $va->attributeValue?->getTranslation('value', app()->getLocale()))->filter()->implode(' / '),
        ])->values()
        : collect();

    $existingTags = $product->exists ? $product->tags->pluck('slug')->values() : collect();
@endphp

<form
    id="product-form"
    method="POST"
    enctype="multipart/form-data"
    x-data="productForm({
        images: @js($existingImages),
        attributes: @js($attributesData),
        variants: @js($existingVariants),
        hasVariants: {{ $product->has_variants ? 'true' : 'false' }},
    })"
    @submit.prevent="beforeSubmit(); AdminForm.submit('product-form', @js($validateUrl), @js($submitUrl), () => window.location = @js(route('admin.products.index')))"
>
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="flex items-center gap-1 border-b border-slate-200 mb-6 bg-white rounded-t-xl px-2 pt-2">
        <template x-for="tab in tabs" :key="tab.key">
            <button
                type="button"
                @click="activeTab = tab.key"
                class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors"
                :class="activeTab === tab.key ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                x-text="tab.label"
            ></button>
        </template>
    </div>

    {{-- Tab 1: Basic Info --}}
    <div x-show="activeTab === 'basic'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card :title="__('Names & Descriptions')">
                <x-admin.lang-tabs>
                    <x-slot:en>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name[en]" value="{{ old('name.en', $product->getTranslation('name', 'en')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Short Description (EN)') }}</label>
                            <textarea name="short_description[en]" rows="2" class="w-full rounded-lg border-slate-300 text-sm">{{ old('short_description.en', $product->getTranslation('short_description', 'en')) }}</textarea>
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description (EN)') }}</label>
                            <div id="description-en-editor" class="bg-white rounded-lg border border-slate-300" style="min-height: 160px">{!! old('description.en', $product->getTranslation('description', 'en')) !!}</div>
                            <textarea name="description[en]" class="hidden" data-editor-source="description-en-editor">{{ old('description.en', $product->getTranslation('description', 'en')) }}</textarea>
                        </div>
                    </x-slot:en>
                    <x-slot:ar>
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name[ar]" value="{{ old('name.ar', $product->getTranslation('name', 'ar')) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Short Description (AR)') }}</label>
                            <textarea name="short_description[ar]" rows="2" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('short_description.ar', $product->getTranslation('short_description', 'ar')) }}</textarea>
                        </div>
                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description (AR)') }}</label>
                            <div id="description-ar-editor" class="bg-white rounded-lg border border-slate-300" dir="rtl" style="min-height: 160px">{!! old('description.ar', $product->getTranslation('description', 'ar')) !!}</div>
                            <textarea name="description[ar]" class="hidden" data-editor-source="description-ar-editor">{{ old('description.ar', $product->getTranslation('description', 'ar')) }}</textarea>
                        </div>
                    </x-slot:ar>
                </x-admin.lang-tabs>
            </x-admin.card>

            <x-admin.card :title="__('Organization')">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Category') }}</label>
                        <x-admin.select name="category_id" :options="$categories" :selected="old('category_id', $product->category_id)" :placeholder="__('Select category')" />
                    </div>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Brand') }}</label>
                        <x-admin.select name="brand_id" :options="$brands" :selected="old('brand_id', $product->brand_id)" :placeholder="__('Select brand')" />
                    </div>
                    <div class="admin-field md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tags') }}</label>
                        <select id="tags-select" name="tags[]" multiple class="w-full" data-placeholder="{{ __('Add tags') }}">
                            @foreach ($existingTags as $tag)
                                <option value="{{ $tag }}" selected>{{ $tag }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Identifiers')">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('SKU') }}</label>
                        <div class="flex gap-2">
                            <input type="text" id="sku-input" name="sku" value="{{ old('sku', $product->sku) }}" class="flex-1 rounded-lg border-slate-300 text-sm">
                            <button type="button" @click="generateSku()" class="px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50 whitespace-nowrap">
                                {{ __('Generate') }}
                            </button>
                        </div>
                    </div>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Barcode') }}</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card :title="__('Status')">
                <div class="space-y-3">
                    <x-admin.checkbox name="is_active" :checked="old('is_active', $product->is_active ?? true)" :label="__('Active')" />
                    <x-admin.checkbox name="is_featured" :checked="old('is_featured', $product->is_featured ?? false)" :label="__('Featured')" />
                    <x-admin.checkbox name="is_digital" :checked="old('is_digital', $product->is_digital ?? false)" :label="__('Digital Product')" />
                </div>
            </x-admin.card>
        </div>
    </div>

    {{-- Tab 2: Pricing --}}
    <div x-show="activeTab === 'pricing'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-admin.card :title="__('Pricing (EGP)')">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Price') }}</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? 0) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Compare Price') }}</label>
                    <input type="number" step="0.01" min="0" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Cost Price') }}</label>
                    <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
            </div>
        </x-admin.card>

        <x-admin.card :title="__('Shipping')">
            <div class="grid grid-cols-2 gap-4">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Weight (kg)') }}</label>
                    <input type="number" step="0.01" min="0" name="weight" value="{{ old('weight', $product->weight) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div></div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Width (cm)') }}</label>
                    <input type="number" step="0.01" min="0" name="width" value="{{ old('width', $product->width) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Height (cm)') }}</label>
                    <input type="number" step="0.01" min="0" name="height" value="{{ old('height', $product->height) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Length (cm)') }}</label>
                    <input type="number" step="0.01" min="0" name="length" value="{{ old('length', $product->length) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
            </div>
        </x-admin.card>
    </div>

    {{-- Tab 3: Images --}}
    <div x-show="activeTab === 'images'" x-cloak class="space-y-6">
        <x-admin.card :title="__('Product Images')">
            <p class="text-xs text-slate-500 mb-4">{{ __('Upload multiple images, drag to reorder, and mark one as primary.') }}</p>

            <div x-ref="imageGrid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-4">
                <template x-for="(image, index) in images" :key="image.key">
                    <div class="relative border border-slate-200 rounded-lg overflow-hidden group" :data-index="index">
                        <img :src="image.url" class="w-full h-28 object-cover">
                        <button type="button" @click="removeImage(index)" class="absolute top-1 end-1 w-6 h-6 rounded-full bg-red-600 text-white text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <label class="absolute bottom-1 start-1 flex items-center gap-1 bg-white/90 rounded px-1.5 py-0.5 text-[11px] cursor-pointer">
                            <input type="radio" name="primary_image_radio" :checked="primaryIndex === index" @change="primaryIndex = index">
                            {{ __('Primary') }}
                        </label>
                        <span class="drag-handle absolute top-1 start-1 w-6 h-6 rounded-full bg-white/90 text-slate-500 text-xs flex items-center justify-center cursor-move">
                            <i class="fa-solid fa-up-down-left-right"></i>
                        </span>
                    </div>
                </template>
            </div>

            <input type="file" x-ref="imageInput" multiple accept="image/*" class="hidden" @change="addImages($event.target.files)">
            <button type="button" @click="$refs.imageInput.click()" class="px-4 py-2 rounded-lg border border-dashed border-slate-300 text-sm text-slate-600 hover:border-amber-500">
                <i class="fa-solid fa-upload me-1"></i>{{ __('Upload Images') }}
            </button>

            <input type="hidden" name="existing_image_ids" :value="JSON.stringify(images.filter((i) => i.id).map((i) => i.id))">
            <input type="hidden" name="primary_existing_image_id" :value="primaryIsExisting() ? images[primaryIndex].id : ''">
            <input type="hidden" name="primary_new_image_index" :value="!primaryIsExisting() && primaryIndex !== null ? newIndexOf(primaryIndex) : ''">

            <div class="admin-field mt-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Video URL (YouTube or direct link)') }}</label>
                <input type="url" name="video_url" value="{{ old('video_url', $existingImages->firstWhere('type', 'video')['url'] ?? '') }}" class="w-full rounded-lg border-slate-300 text-sm" placeholder="https://youtube.com/watch?v=...">
            </div>
        </x-admin.card>
    </div>

    {{-- Tab 4: Variants --}}
    <div x-show="activeTab === 'variants'" x-cloak class="space-y-6">
        <x-admin.card>
            <x-admin.checkbox name="has_variants" x-model="hasVariants" :checked="old('has_variants', $product->has_variants ?? false)" :label="__('This product has variants')" />
        </x-admin.card>

        <div x-show="hasVariants" x-cloak class="space-y-6">
            <x-admin.card :title="__('Attributes')">
                <template x-for="(row, rowIndex) in attributeRows" :key="row.key">
                    <div class="flex flex-wrap items-end gap-3 mb-3 pb-3 border-b border-slate-100 last:border-b-0">
                        <div class="admin-field w-56">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Attribute') }}</label>
                            <select class="w-full rounded-lg border-slate-300 text-sm" x-model.number="row.attributeId">
                                <option value="">{{ __('Select attribute') }}</option>
                                <template x-for="attr in attributes" :key="attr.id">
                                    <option :value="attr.id" x-text="attr.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="admin-field flex-1 min-w-[220px]">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Values') }}</label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="value in valuesFor(row.attributeId)" :key="value.id">
                                    <label class="inline-flex items-center gap-1 px-2 py-1 rounded border text-xs cursor-pointer"
                                        :class="row.values.includes(value.id) ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-slate-300 text-slate-600'">
                                        <input type="checkbox" class="hidden" :value="value.id" @change="toggleRowValue(row, value.id)" :checked="row.values.includes(value.id)">
                                        <span x-text="value.value"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                        <button type="button" @click="attributeRows.splice(rowIndex, 1)" class="text-slate-400 hover:text-red-600 mb-2">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </template>

                <button type="button" @click="attributeRows.push({ key: Date.now()+Math.random(), attributeId: '', values: [] })" class="mt-2 px-3 py-1.5 rounded-lg border border-slate-300 text-xs text-slate-600 hover:bg-slate-50">
                    <i class="fa-solid fa-plus me-1"></i>{{ __('Add Attribute') }}
                </button>

                <div class="mt-4">
                    <button type="button" @click="generateVariants()" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        <i class="fa-solid fa-bolt me-1"></i>{{ __('Generate Variants') }}
                    </button>
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Variants')">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-2 py-2 text-start">{{ __('Image') }}</th>
                                <th class="px-2 py-2 text-start">{{ __('Combination') }}</th>
                                <th class="px-2 py-2 text-start">{{ __('SKU') }}</th>
                                <th class="px-2 py-2 text-start">{{ __('Price') }}</th>
                                <th class="px-2 py-2 text-start">{{ __('Compare Price') }}</th>
                                <th class="px-2 py-2 text-start">{{ __('Active') }}</th>
                                <th class="px-2 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(variant, vIndex) in variants" :key="variant.key">
                                <tr class="border-t border-slate-100">
                                    <td class="px-2 py-2">
                                        <label class="w-12 h-12 rounded border border-dashed border-slate-300 flex items-center justify-center overflow-hidden cursor-pointer bg-slate-50">
                                            <img x-show="variant.image" :src="variant.image" class="w-full h-full object-cover">
                                            <i x-show="!variant.image" class="fa-solid fa-image text-slate-300"></i>
                                            <input type="file" accept="image/*" class="hidden" :name="'variants['+vIndex+'][image]'" @change="setVariantImage(variant, $event.target.files[0])">
                                        </label>
                                    </td>
                                    <td class="px-2 py-2">
                                        <span x-text="variant.label || '—'"></span>
                                        <template x-for="valId in variant.attribute_values" :key="valId">
                                            <input type="hidden" :name="'variants['+vIndex+'][attribute_values][]'" :value="valId">
                                        </template>
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" :name="'variants['+vIndex+'][sku]'" x-model="variant.sku" class="w-28 rounded-lg border-slate-300 text-xs">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" step="0.01" min="0" :name="'variants['+vIndex+'][price]'" x-model="variant.price" class="w-24 rounded-lg border-slate-300 text-xs">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" step="0.01" min="0" :name="'variants['+vIndex+'][compare_price]'" x-model="variant.compare_price" class="w-24 rounded-lg border-slate-300 text-xs">
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <input type="checkbox" :name="'variants['+vIndex+'][is_active]'" value="1" x-model="variant.is_active" class="rounded border-slate-300 text-amber-600">
                                    </td>
                                    <td class="px-2 py-2 text-end">
                                        <button type="button" @click="variants.splice(vIndex, 1)" class="text-slate-400 hover:text-red-600">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!variants.length">
                                <td colspan="7" class="px-2 py-6 text-center text-slate-400">{{ __('No variants yet — add attributes and click Generate.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        </div>
    </div>

    {{-- Tab 5: SEO --}}
    <div x-show="activeTab === 'seo'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-admin.card :title="__('Meta Tags')">
            <x-admin.lang-tabs>
                <x-slot:en>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Title (EN)') }}</label>
                        <input type="text" name="meta_title[en]" value="{{ old('meta_title.en', $seo?->getTranslation('title', 'en')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Description (EN)') }}</label>
                        <textarea name="meta_description[en]" rows="3" class="w-full rounded-lg border-slate-300 text-sm">{{ old('meta_description.en', $seo?->getTranslation('description', 'en')) }}</textarea>
                    </div>
                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Keywords (EN)') }}</label>
                        <input type="text" name="meta_keywords[en]" value="{{ old('meta_keywords.en', $seo?->getTranslation('keywords', 'en')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                </x-slot:en>
                <x-slot:ar>
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Title (AR)') }}</label>
                        <input type="text" name="meta_title[ar]" value="{{ old('meta_title.ar', $seo?->getTranslation('title', 'ar')) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                    </div>
                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Description (AR)') }}</label>
                        <textarea name="meta_description[ar]" rows="3" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">{{ old('meta_description.ar', $seo?->getTranslation('description', 'ar')) }}</textarea>
                    </div>
                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Meta Keywords (AR)') }}</label>
                        <input type="text" name="meta_keywords[ar]" value="{{ old('meta_keywords.ar', $seo?->getTranslation('keywords', 'ar')) }}" class="w-full rounded-lg border-slate-300 text-sm" dir="rtl">
                    </div>
                </x-slot:ar>
            </x-admin.lang-tabs>
        </x-admin.card>

        <div class="space-y-6">
            <x-admin.card :title="__('Open Graph')">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Title') }}</label>
                    <input type="text" name="og_title" value="{{ old('og_title', $seo?->og_title) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Description') }}</label>
                    <textarea name="og_description" rows="2" class="w-full rounded-lg border-slate-300 text-sm">{{ old('og_description', $seo?->og_description) }}</textarea>
                </div>
                <div class="admin-field mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('OG Image') }}</label>
                    <x-admin.image-upload name="og_image" :current="$seo?->og_image" />
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Indexing')">
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Robots') }}</label>
                    <x-admin.select name="robots" :options="[
                        'index,follow' => 'index, follow',
                        'noindex,follow' => 'noindex, follow',
                        'index,nofollow' => 'index, nofollow',
                        'noindex,nofollow' => 'noindex, nofollow',
                    ]" :selected="old('robots', $seo?->robots ?? 'index,follow')" />
                </div>
                <div class="admin-field mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Canonical URL') }}</label>
                    <input type="url" name="canonical_url" value="{{ old('canonical_url', $seo?->canonical_url) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Schema JSON') }}</label>
                    <textarea name="schema_json" rows="4" class="w-full rounded-lg border-slate-300 text-xs font-mono">{{ old('schema_json', $seo?->schema_json ? json_encode($seo->schema_json, JSON_PRETTY_PRINT) : '') }}</textarea>
                </div>
            </x-admin.card>
        </div>
    </div>

    <div class="flex items-center gap-2 mt-6 bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <button type="submit" class="px-6 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            {{ $isEdit ? __('Update Product') : __('Create Product') }}
        </button>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
            {{ __('Cancel') }}
        </a>
    </div>
</form>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <script>
        function productForm({ images, attributes, variants, hasVariants }) {
            return {
                tabs: [
                    { key: 'basic', label: @js(__('Basic Info')) },
                    { key: 'pricing', label: @js(__('Pricing')) },
                    { key: 'images', label: @js(__('Images')) },
                    { key: 'variants', label: @js(__('Variants')) },
                    { key: 'seo', label: @js(__('SEO')) },
                ],
                activeTab: 'basic',
                attributes,
                hasVariants,
                images: images.map((img, i) => ({ key: 'existing-'+i, id: img.id, url: img.url, type: img.type, file: null })),
                primaryIndex: (() => {
                    const idx = images.findIndex((img) => img.is_primary);
                    return idx >= 0 ? idx : (images.length ? 0 : null);
                })(),
                attributeRows: [],
                variants: variants.map((v) => ({
                    key: 'existing-'+v.id,
                    sku: v.sku,
                    price: v.price,
                    compare_price: v.compare_price,
                    is_active: !!v.is_active,
                    image: v.image,
                    attribute_values: v.attribute_values,
                    label: v.label,
                })),

                init() {
                    if (this.$refs.imageGrid) {
                        new Sortable(this.$refs.imageGrid, {
                            handle: '.drag-handle',
                            animation: 150,
                            onEnd: (e) => {
                                const moved = this.images.splice(e.oldIndex, 1)[0];
                                this.images.splice(e.newIndex, 0, moved);
                                if (this.primaryIndex === e.oldIndex) this.primaryIndex = e.newIndex;
                            },
                        });
                    }

                    ['en', 'ar'].forEach((locale) => {
                        const el = document.getElementById(`description-${locale}-editor`);
                        if (!el || !window.Quill) return;
                        const quill = new Quill(el, { theme: 'snow' });
                        el.__quill = quill;
                    });
                },

                beforeSubmit() {
                    ['en', 'ar'].forEach((locale) => {
                        const el = document.getElementById(`description-${locale}-editor`);
                        const target = document.querySelector(`[data-editor-source="description-${locale}-editor"]`);
                        if (el && el.__quill && target) {
                            target.value = el.__quill.root.innerHTML;
                        }
                    });
                },

                addImages(fileList) {
                    Array.from(fileList).forEach((file) => {
                        this.images.push({ key: 'new-'+Date.now()+Math.random(), url: URL.createObjectURL(file), type: 'image', file });
                    });
                    if (this.primaryIndex === null && this.images.length) this.primaryIndex = 0;
                    this.syncFileInput();
                },

                removeImage(index) {
                    this.images.splice(index, 1);
                    if (this.primaryIndex === index) this.primaryIndex = this.images.length ? 0 : null;
                    else if (this.primaryIndex > index) this.primaryIndex -= 1;
                    this.syncFileInput();
                },

                syncFileInput() {
                    const dt = new DataTransfer();
                    this.images.forEach((img) => { if (img.file) dt.items.add(img.file); });
                    this.$refs.imageInput.files = dt.files;
                    this.$refs.imageInput.name = 'images[]';
                },

                primaryIsExisting() {
                    return this.primaryIndex !== null && !!this.images[this.primaryIndex]?.id;
                },

                newIndexOf(index) {
                    let count = -1;
                    for (let i = 0; i <= index; i++) {
                        if (!this.images[i].id) count += 1;
                    }
                    return count;
                },

                valuesFor(attributeId) {
                    const attr = this.attributes.find((a) => a.id === attributeId);
                    return attr ? attr.values : [];
                },

                toggleRowValue(row, valueId) {
                    const i = row.values.indexOf(valueId);
                    if (i >= 0) row.values.splice(i, 1);
                    else row.values.push(valueId);
                },

                generateVariants() {
                    const rows = this.attributeRows.filter((r) => r.attributeId && r.values.length);
                    if (!rows.length) { this.variants = []; return; }

                    let combos = [[]];
                    rows.forEach((row) => {
                        const next = [];
                        combos.forEach((combo) => {
                            row.values.forEach((valueId) => next.push([...combo, { attributeId: row.attributeId, valueId }]));
                        });
                        combos = next;
                    });

                    this.variants = combos.map((combo) => {
                        const labels = combo.map((c) => {
                            const attr = this.attributes.find((a) => a.id === c.attributeId);
                            const val = attr?.values.find((v) => v.id === c.valueId);
                            return val ? val.value : '';
                        }).filter(Boolean);

                        return {
                            key: 'gen-'+Date.now()+Math.random(),
                            sku: '',
                            price: 0,
                            compare_price: '',
                            is_active: true,
                            image: null,
                            attribute_values: combo.map((c) => c.valueId),
                            label: labels.join(' / '),
                        };
                    });
                },

                setVariantImage(variant, file) {
                    if (!file) return;
                    variant.image = URL.createObjectURL(file);
                    variant.__file = file;
                },

                async generateSku() {
                    const res = await fetch(@js(route('admin.products.generate-sku')), { headers: { Accept: 'application/json' } });
                    const data = await res.json();
                    document.getElementById('sku-input').value = data.sku;
                },
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.$ && $('#tags-select').length) {
                $('#tags-select').select2({ width: '100%', tags: true, tokenSeparators: [',', ' '] });
            }
        });
    </script>
@endpush
