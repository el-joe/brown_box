<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\SeoPage;
use App\Services\AiProviderService;
use App\Services\AiService;
use App\Services\SeoPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AiController extends Controller
{
    public function __construct(
        private readonly AiService $ai,
        private readonly AiProviderService $providers,
        private readonly SeoPageService $seoPages,
    ) {
    }

    public function settings(): View
    {
        $providers = AiProvider::query()->orderBy('code')->get();

        return view('admin.ai.settings', compact('providers'));
    }

    public function updateSettings(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'providers' => ['required', 'array'],
            'providers.*.is_active' => ['nullable', 'boolean'],
            'providers.*.config' => ['nullable', 'array'],
            'providers.*.config.api_key' => ['nullable', 'string'],
            'providers.*.config.default_model' => ['nullable', 'string'],
            'providers.*.config.site_url' => ['nullable', 'string'],
            'providers.*.config.site_name' => ['nullable', 'string'],
            'providers.*.available_models' => ['nullable', 'string'],
        ]);

        foreach ($data['providers'] as $code => $payload) {
            $provider = AiProvider::query()->where('code', $code)->first();

            if (! $provider) {
                continue;
            }

            $models = collect(explode(',', (string) ($payload['available_models'] ?? '')))
                ->map(fn ($model) => trim($model))
                ->filter()
                ->values()
                ->all();

            $provider->update([
                'is_active' => (bool) ($payload['is_active'] ?? false),
                'config' => $payload['config'] ?? [],
                'available_models' => $models,
            ]);
        }

        $this->providers->flush();

        return redirect()->route('admin.ai.settings')->with('success', __('AI settings updated.'));
    }

    public function dashboard(): View
    {
        $activeProviders = $this->ai->activeProviders();

        return view('admin.ai.dashboard', compact('activeProviders'));
    }

    public function seo(): View
    {
        $activeProviders = $this->ai->activeProviders();
        $products = Product::query()->select(['id', 'name'])->orderBy('id', 'desc')->get();
        $categories = Category::query()->select(['id', 'name'])->orderBy('id', 'desc')->get();

        return view('admin.ai.seo', compact('activeProviders', 'products', 'categories'));
    }

    public function generateSeo(Request $request): JsonResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string'],
            'model' => ['required', 'string'],
            'locale' => ['required', 'in:en,ar'],
            'target_type' => ['required', 'in:product,category,page'],
            'target_id' => ['nullable', 'integer'],
            'page_key' => ['nullable', 'string'],
        ]);

        $context = $this->buildSeoContext($data);
        $locale = $data['locale'];

        $prompt = <<<PROMPT
You are an SEO expert writing metadata for an e-commerce store. Context:
{$context}

Write SEO metadata in {$locale} language. Respond ONLY with a JSON object (no markdown fences) with keys:
meta_title (max 60 chars), meta_description (max 160 chars), keywords (comma separated), og_title, og_description.
PROMPT;

        $result = $this->ai->chat($data['provider'], $data['model'], [
            ['role' => 'user', 'content' => $prompt],
        ]);

        $parsed = json_decode($this->stripJsonFences($result), true) ?: [];

        return response()->json(['success' => true, 'data' => $parsed]);
    }

    public function saveSeo(Request $request): JsonResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:en,ar'],
            'target_type' => ['required', 'in:product,category,page'],
            'target_id' => ['nullable', 'integer'],
            'page_key' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'keywords' => ['nullable', 'string'],
            'og_title' => ['nullable', 'string'],
            'og_description' => ['nullable', 'string'],
        ]);

        if ($data['target_type'] === 'page') {
            $seoPage = $this->seoPages->findOrNewForPage($data['page_key']);
        } else {
            $seoPage = $this->seoPages->findOrNewForModel($data['target_type'], (int) $data['target_id']);
        }

        $locale = $data['locale'];
        $seoPage->setTranslation('title', $locale, $data['meta_title'] ?? '');
        $seoPage->setTranslation('description', $locale, $data['meta_description'] ?? '');
        $seoPage->setTranslation('keywords', $locale, $data['keywords'] ?? '');
        $seoPage->setTranslation('og_title', $locale, $data['og_title'] ?? '');
        $seoPage->setTranslation('og_description', $locale, $data['og_description'] ?? '');
        $seoPage->save();

        return response()->json(['success' => true]);
    }

    public function blog(): View
    {
        $activeProviders = $this->ai->activeProviders();
        $blogCategories = BlogCategory::query()->orderBy('id', 'desc')->get();
        $categories = Category::query()->select(['id', 'name'])->orderBy('id', 'desc')->get();

        return view('admin.ai.blog', compact('activeProviders', 'blogCategories', 'categories'));
    }

    public function generateBlog(Request $request): JsonResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string'],
            'model' => ['required', 'string'],
            'locale' => ['required', 'in:en,ar'],
            'topic' => ['required', 'string'],
            'tone' => ['nullable', 'string'],
            'word_count' => ['nullable', 'integer'],
            'category_ids' => ['nullable', 'array'],
        ]);

        $locale = $data['locale'];
        $tone = $data['tone'] ?? 'friendly';
        $wordCount = $data['word_count'] ?? 800;

        $categoryNames = Category::query()
            ->whereIn('id', $data['category_ids'] ?? [])
            ->get()
            ->map(fn (Category $category) => $category->getTranslation('name', $locale))
            ->implode(', ');

        $productNames = Product::query()
            ->when(! empty($data['category_ids']), fn ($q) => $q->whereIn('category_id', $data['category_ids']))
            ->limit(10)
            ->get()
            ->map(fn (Product $product) => $product->getTranslation('name', $locale))
            ->implode(', ');

        $prompt = <<<PROMPT
You are a content writer for an e-commerce store named Brown Box. Write a blog post about: "{$data['topic']}".
Tone: {$tone}. Target length: about {$wordCount} words. Language: {$locale}.
Relevant store categories: {$categoryNames}
Related products to mention naturally: {$productNames}

Respond ONLY with a JSON object (no markdown fences) with keys:
title, excerpt (1-2 sentences), content (HTML with headings/paragraphs), meta_title, meta_description.
PROMPT;

        $result = $this->ai->chat($data['provider'], $data['model'], [
            ['role' => 'user', 'content' => $prompt],
        ], ['max_tokens' => 3000]);

        $parsed = json_decode($this->stripJsonFences($result), true) ?: [];

        return response()->json(['success' => true, 'data' => $parsed]);
    }

    public function saveBlog(Request $request): JsonResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'in:en,ar'],
            'title' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'blog_category_id' => ['nullable', 'integer'],
        ]);

        $locale = $data['locale'];

        $post = new BlogPost();
        $post->setTranslation('title', $locale, $data['title']);
        $post->setTranslation('excerpt', $locale, $data['excerpt'] ?? '');
        $post->setTranslation('content', $locale, $data['content']);
        $post->setTranslation('meta_title', $locale, $data['meta_title'] ?? '');
        $post->setTranslation('meta_description', $locale, $data['meta_description'] ?? '');
        $post->blog_category_id = $data['blog_category_id'] ?? null;
        $post->author_id = Auth::guard('admin')->id();
        $post->is_published = false;
        $post->save();

        return response()->json([
            'success' => true,
            'edit_url' => route('admin.blog.edit', $post),
        ]);
    }

    public function trending(): View
    {
        $activeProviders = $this->ai->activeProviders();
        $categories = Category::query()->select(['id', 'name'])->orderBy('id', 'desc')->get();

        return view('admin.ai.trending', compact('activeProviders', 'categories'));
    }

    public function generateTrending(Request $request): JsonResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string'],
            'model' => ['required', 'string'],
            'category_ids' => ['nullable', 'array'],
            'market' => ['nullable', 'string'],
        ]);

        $categoryNames = Category::query()
            ->whereIn('id', $data['category_ids'] ?? [])
            ->get()
            ->map(fn (Category $category) => $category->getTranslation('name', 'en'))
            ->implode(', ');

        $market = $data['market'] ?? 'Egypt';

        $prompt = <<<PROMPT
You are an e-commerce market research analyst. Research trending opportunities for an online store in {$market}.
Focus categories: {$categoryNames}

Respond ONLY with a JSON object (no markdown fences) with keys:
trending_products (array of strings), emerging_niches (array of strings), seasonal_opportunities (array of strings), content_ideas (array of strings).
PROMPT;

        $result = $this->ai->chat($data['provider'], $data['model'], [
            ['role' => 'user', 'content' => $prompt],
        ]);

        $parsed = json_decode($this->stripJsonFences($result), true) ?: [];

        return response()->json(['success' => true, 'data' => $parsed]);
    }

    public function social(): View
    {
        $activeProviders = $this->ai->activeProviders();
        $products = Product::query()->select(['id', 'name'])->orderBy('id', 'desc')->get();
        $categories = Category::query()->select(['id', 'name'])->orderBy('id', 'desc')->get();
        $coupons = Coupon::query()->valid()->get();
        $flashSales = FlashSale::query()->active()->get();

        return view('admin.ai.social', compact('activeProviders', 'products', 'categories', 'coupons', 'flashSales'));
    }

    public function generateSocial(Request $request): JsonResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string'],
            'model' => ['required', 'string'],
            'locale' => ['required', 'in:en,ar'],
            'target_type' => ['required', 'in:product,category,coupon,flash_sale'],
            'target_id' => ['required', 'integer'],
            'platform' => ['nullable', 'string'],
            'tone' => ['nullable', 'string'],
            'generate_image' => ['nullable', 'boolean'],
        ]);

        $context = $this->buildSocialContext($data);
        $locale = $data['locale'];
        $platform = $data['platform'] ?? 'Instagram';
        $tone = $data['tone'] ?? 'exciting';

        $prompt = <<<PROMPT
You are a social media copywriter for an e-commerce store. Platform: {$platform}. Tone: {$tone}. Language: {$locale}.
Context:
{$context}

Respond ONLY with a JSON object (no markdown fences) with keys:
caption, hashtags (array of strings without #), image_prompt (a descriptive prompt for an image generator), story_variant (a shorter version for stories).
PROMPT;

        $result = $this->ai->chat($data['provider'], $data['model'], [
            ['role' => 'user', 'content' => $prompt],
        ]);

        $parsed = json_decode($this->stripJsonFences($result), true) ?: [];

        if (! empty($data['generate_image']) && ! empty($parsed['image_prompt'])) {
            $driver = $this->ai->driver($data['provider']);

            if ($driver->supportsImages()) {
                $parsed['image_url'] = $this->ai->generateImage($data['provider'], $parsed['image_prompt']);
            }
        }

        return response()->json(['success' => true, 'data' => $parsed]);
    }

    public function productDescription(): View
    {
        $activeProviders = $this->ai->activeProviders();
        $products = Product::query()->select(['id', 'name', 'sku'])->orderBy('id', 'desc')->get();

        return view('admin.ai.product-description', compact('activeProviders', 'products'));
    }

    public function generateProductDescription(Request $request): JsonResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string'],
            'model' => ['required', 'string'],
            'locale' => ['required', 'in:en,ar'],
            'product_id' => ['required', 'integer'],
            'tone' => ['nullable', 'string'],
        ]);

        $product = Product::query()->with(['category', 'brand', 'productImages'])->findOrFail($data['product_id']);
        $locale = $data['locale'];
        $tone = $data['tone'] ?? 'persuasive';

        $name = $product->getTranslation('name', $locale);
        $categoryName = $product->category?->getTranslation('name', $locale);
        $brandName = $product->brand?->name;

        $prompt = <<<PROMPT
You are an e-commerce copywriter. Write a product description in {$locale} language, {$tone} tone, for:
Name: {$name}
Category: {$categoryName}
Brand: {$brandName}
SKU: {$product->sku}
Price: {$product->price}

Respond ONLY with a JSON object (no markdown fences) with keys:
description (HTML with paragraphs/bullet points), short_description (1-2 sentences, plain text).
PROMPT;

        $result = $this->ai->chat($data['provider'], $data['model'], [
            ['role' => 'user', 'content' => $prompt],
        ]);

        $parsed = json_decode($this->stripJsonFences($result), true) ?: [];

        return response()->json(['success' => true, 'data' => $parsed]);
    }

    public function saveProductDescription(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'locale' => ['required', 'in:en,ar'],
            'description' => ['required', 'string'],
            'short_description' => ['nullable', 'string'],
        ]);

        $product = Product::query()->findOrFail($data['product_id']);
        $locale = $data['locale'];

        $product->setTranslation('description', $locale, $data['description']);
        $product->setTranslation('short_description', $locale, $data['short_description'] ?? '');
        $product->save();

        return response()->json(['success' => true]);
    }

    public function models(Request $request): JsonResponse
    {
        $provider = (string) $request->query('provider');

        return response()->json([
            'models' => $this->ai->modelsFor($provider),
            'default_model' => $this->ai->defaultModel($provider),
        ]);
    }

    private function buildSeoContext(array $data): string
    {
        if ($data['target_type'] === 'product') {
            $product = Product::query()->with('category')->find($data['target_id']);

            if (! $product) {
                return '';
            }

            return sprintf(
                "Product: %s\nCategory: %s\nPrice: %s\nSKU: %s",
                $product->getTranslation('name', 'en'),
                $product->category?->getTranslation('name', 'en'),
                $product->price,
                $product->sku,
            );
        }

        if ($data['target_type'] === 'category') {
            $category = Category::query()->find($data['target_id']);

            if (! $category) {
                return '';
            }

            return sprintf('Category: %s', $category->getTranslation('name', 'en'));
        }

        return sprintf('Static page: %s', $this->seoPages->pageLabel((string) ($data['page_key'] ?? '')));
    }

    private function buildSocialContext(array $data): string
    {
        if ($data['target_type'] === 'product') {
            $product = Product::query()->with('category')->find($data['target_id']);

            if (! $product) {
                return '';
            }

            return sprintf(
                "Product: %s\nCategory: %s\nPrice: %s",
                $product->getTranslation('name', 'en'),
                $product->category?->getTranslation('name', 'en'),
                $product->price,
            );
        } elseif ($data['target_type'] === 'category') {
            $category = Category::query()->find($data['target_id']);

            if (! $category) {
                return '';
            }

            return sprintf('Category: %s', $category->getTranslation('name', 'en'));
        } elseif ($data['target_type'] === 'coupon') {
            $coupon = Coupon::query()->find($data['target_id']);

            if (! $coupon) {
                return '';
            }

            return sprintf(
                "Coupon code: %s\nType: %s\nValue: %s\nExpires: %s",
                $coupon->code,
                $coupon->type,
                $coupon->value,
                optional($coupon->expires_at)->toDateString(),
            );
        } elseif ($data['target_type'] === 'flash_sale') {
            $flashSale = FlashSale::query()->with('items.product')->find($data['target_id']);

            if (! $flashSale) {
                return '';
            }

            $products = $flashSale->items->map(fn ($item) => $item->product?->getTranslation('name', 'en'))->filter()->implode(', ');

            return sprintf(
                "Flash sale: %s\nEnds: %s\nProducts: %s",
                $flashSale->getTranslation('name', 'en'),
                optional($flashSale->ends_at)->toDateTimeString(),
                $products,
            );
        }

        return '';
    }

    private function stripJsonFences(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);

        return trim((string) $text);
    }
}
