@extends('admin.layouts.app')

@section('title', __('AI Social Posts'))

@section('breadcrumb')
    <a href="{{ route('admin.ai.dashboard') }}">{{ __('AI Module') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Social Posts') }}</span>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        x-data="{
            ...aiProviderSelector('{{ array_key_first($activeProviders) ?? '' }}'),
            targetType: 'product',
            targetId: '',
            platform: 'Instagram',
            locale: 'en',
            tone: 'exciting',
            generateImage: false,
            loading: false,
            result: null,
            error: null,
            copy(text) { navigator.clipboard.writeText(text); },
            generate() {
                this.loading = true;
                this.error = null;
                fetch(@js(route('admin.ai.social.generate')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        provider: this.provider, model: this.model, locale: this.locale,
                        target_type: this.targetType, target_id: this.targetId,
                        platform: this.platform, tone: this.tone, generate_image: this.generateImage,
                    }),
                })
                    .then(r => r.json())
                    .then(d => { this.result = d.data; this.loading = false; })
                    .catch(() => { this.error = '{{ __('Something went wrong.') }}'; this.loading = false; });
            },
        }">
        <x-admin.card :title="__('Generate Social Post')">
            @include('admin.ai._provider_selector')

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Target Type') }}</label>
                <select x-model="targetType" x-on:change="targetId = ''" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="product">{{ __('Product') }}</option>
                    <option value="category">{{ __('Category') }}</option>
                    <option value="coupon">{{ __('Coupon') }}</option>
                    <option value="flash_sale">{{ __('Flash Sale') }}</option>
                </select>
            </div>

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Target') }}</label>
                <select x-show="targetType === 'product'" x-model="targetId" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">{{ __('Select a product') }}</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->getTranslation('name', 'en') }}</option>
                    @endforeach
                </select>
                <select x-show="targetType === 'category'" x-model="targetId" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">{{ __('Select a category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->getTranslation('name', 'en') }}</option>
                    @endforeach
                </select>
                <select x-show="targetType === 'coupon'" x-model="targetId" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">{{ __('Select a coupon') }}</option>
                    @foreach ($coupons as $coupon)
                        <option value="{{ $coupon->id }}">{{ $coupon->code }}</option>
                    @endforeach
                </select>
                <select x-show="targetType === 'flash_sale'" x-model="targetId" class="w-full rounded-lg border-slate-300 text-sm">
                    <option value="">{{ __('Select a flash sale') }}</option>
                    @foreach ($flashSales as $flashSale)
                        <option value="{{ $flashSale->id }}">{{ $flashSale->getTranslation('name', 'en') }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Platform') }}</label>
                    <select x-model="platform" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="Instagram">Instagram</option>
                        <option value="Facebook">Facebook</option>
                        <option value="TikTok">TikTok</option>
                        <option value="X">X (Twitter)</option>
                    </select>
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Tone') }}</label>
                    <select x-model="tone" class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="exciting">{{ __('Exciting') }}</option>
                        <option value="professional">{{ __('Professional') }}</option>
                        <option value="playful">{{ __('Playful') }}</option>
                        <option value="urgent">{{ __('Urgent') }}</option>
                    </select>
                </div>
            </div>

            <div class="admin-field mb-3">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Language') }}</label>
                <div class="flex gap-1 text-sm">
                    <button type="button" @click="locale = 'en'" class="px-3 py-1 rounded" :class="locale === 'en' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500'">EN</button>
                    <button type="button" @click="locale = 'ar'" class="px-3 py-1 rounded" :class="locale === 'ar' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-slate-500'">AR</button>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm mb-4">
                <input type="checkbox" x-model="generateImage">
                {{ __('Also generate an image (OpenAI only)') }}
            </label>

            <button type="button" @click="generate()" :disabled="loading || !targetId" class="w-full px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700 disabled:opacity-50">
                <i class="fa-solid fa-spinner fa-spin" x-show="loading"></i>
                <span x-text="loading ? '{{ __('Generating...') }}' : '{{ __('Generate') }}'"></span>
            </button>
            <p class="text-red-600 text-xs mt-2" x-show="error" x-text="error"></p>
        </x-admin.card>

        <x-admin.card :title="__('Result')">
            <template x-if="!result">
                <p class="text-sm text-slate-400">{{ __('Generated post content will appear here.') }}</p>
            </template>
            <template x-if="result">
                <div class="space-y-3">
                    <div class="admin-field">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-medium text-slate-500">{{ __('Caption') }}</label>
                            <button type="button" @click="copy(result.caption)" class="text-xs text-amber-600"><i class="fa-regular fa-copy me-1"></i>{{ __('Copy') }}</button>
                        </div>
                        <textarea rows="4" x-model="result.caption" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                    <div class="admin-field">
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Hashtags') }}</label>
                        <div class="flex flex-wrap gap-1">
                            <template x-for="tag in (result.hashtags || [])" :key="tag">
                                <span class="text-xs bg-sky-50 text-sky-700 rounded-full px-2 py-1" x-text="'#' + tag"></span>
                            </template>
                        </div>
                    </div>
                    <template x-if="result.image_url">
                        <div class="admin-field">
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Image') }}</label>
                            <img :src="result.image_url" class="rounded-lg border border-slate-200 max-h-64">
                        </div>
                    </template>
                    <div class="admin-field">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-medium text-slate-500">{{ __('Story Variant') }}</label>
                            <button type="button" @click="copy(result.story_variant)" class="text-xs text-amber-600"><i class="fa-regular fa-copy me-1"></i>{{ __('Copy') }}</button>
                        </div>
                        <textarea rows="2" x-model="result.story_variant" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                </div>
            </template>
        </x-admin.card>
    </div>
@endsection
