<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SeoPage;
use App\Models\StaticPage;
use App\Repositories\Contracts\SeoPageRepositoryInterface;
use Illuminate\Support\Str;

class SeoPageService
{
    /**
     * @var array<string, string>
     */
    private const STATIC_PAGES = [
        'home' => 'Home',
        'blog' => 'Blog',
        'category-list' => 'Category List',
        'cart' => 'Cart',
        'checkout' => 'Checkout',
        'about-us' => 'About Us',
        'contact-us' => 'Contact Us',
    ];

    /**
     * @var array<string, class-string>
     */
    private const MODEL_MAP = [
        'product' => Product::class,
        'category' => Category::class,
        'blog_post' => BlogPost::class,
        'brand' => Brand::class,
        'static_page' => StaticPage::class,
    ];

    public function __construct(private readonly SeoPageRepositoryInterface $seoPages)
    {
    }

    public function staticPageGroups(): array
    {
        return collect(self::STATIC_PAGES)->map(function (string $label, string $key) {
            $seo = $this->seoPages->findByPageKey($key);

            return [
                'page_key' => $key,
                'label' => $label,
                'filled' => $this->isFilled($seo),
                'seo' => $seo,
            ];
        })->values()->all();
    }

    public function productGroup(): array
    {
        return Product::query()
            ->select(['id', 'name'])
            ->with('seoPage')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->getTranslation('name', 'en') ?: $product->getTranslation('name', 'ar'),
                'filled' => $this->isFilled($product->seoPage),
            ])->all();
    }

    public function categoryGroup(): array
    {
        return Category::query()
            ->select(['id', 'name'])
            ->with('seoPage')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->getTranslation('name', 'en') ?: $category->getTranslation('name', 'ar'),
                'filled' => $this->isFilled($category->seoPage),
            ])->all();
    }

    public function blogPostGroup(): array
    {
        return BlogPost::query()
            ->select(['id', 'title', 'slug'])
            ->with('seoPage')
            ->latest()
            ->get()
            ->map(fn (BlogPost $post) => [
                'id' => $post->id,
                'name' => $post->getTranslation('title', 'en') ?: $post->getTranslation('title', 'ar'),
                'filled' => $this->isFilled($post->seoPage),
            ])->all();
    }

    public function brandGroup(): array
    {
        return Brand::query()
            ->select(['id', 'name'])
            ->with('seoPage')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn (Brand $brand) => [
                'id' => $brand->id,
                'name' => $brand->getTranslation('name', 'en') ?: $brand->getTranslation('name', 'ar'),
                'filled' => $this->isFilled($brand->seoPage),
            ])->all();
    }

    public function staticPageGroup(): array
    {
        return StaticPage::query()
            ->select(['id', 'title', 'slug'])
            ->with('seoPage')
            ->get()
            ->map(fn (StaticPage $page) => [
                'id' => $page->id,
                'name' => $page->getTranslation('title', 'en') ?: $page->getTranslation('title', 'ar'),
                'filled' => $this->isFilled($page->seoPage),
            ])->all();
    }

    public function pageLabel(string $pageKey): string
    {
        return self::STATIC_PAGES[$pageKey] ?? Str::headline($pageKey);
    }

    public function isValidPageKey(string $pageKey): bool
    {
        return array_key_exists($pageKey, self::STATIC_PAGES);
    }

    public function isValidModelType(string $modelType): bool
    {
        return array_key_exists($modelType, self::MODEL_MAP);
    }

    public function modelClass(string $modelType): string
    {
        return self::MODEL_MAP[$modelType] ?? abort(404);
    }

    public function findOrNewForPage(string $pageKey): SeoPage
    {
        return $this->seoPages->findByPageKey($pageKey) ?? new SeoPage(['page_key' => $pageKey]);
    }

    public function findOrNewForModel(string $modelType, int $modelId): SeoPage
    {
        $class = $this->modelClass($modelType);

        return $this->seoPages->findForModel($class, $modelId)
            ?? new SeoPage(['model_type' => $class, 'model_id' => $modelId]);
    }

    public function save(SeoPage $seoPage, array $data): SeoPage
    {
        $seoPage->fill($data);
        $seoPage->save();

        return $seoPage;
    }

    public function bulkApplyToProductsMissingSeo(array $template): int
    {
        $count = 0;

        Product::query()->doesntHave('seoPage')->chunkById(100, function ($products) use ($template, &$count) {
            foreach ($products as $product) {
                $name = ['en' => $product->getTranslation('name', 'en'), 'ar' => $product->getTranslation('name', 'ar')];

                SeoPage::query()->create([
                    'model_type' => Product::class,
                    'model_id' => $product->id,
                    'title' => [
                        'en' => str_replace('{name}', $name['en'], $template['title']['en'] ?? '{name}'),
                        'ar' => str_replace('{name}', $name['ar'], $template['title']['ar'] ?? '{name}'),
                    ],
                    'description' => [
                        'en' => str_replace('{name}', $name['en'], $template['description']['en'] ?? ''),
                        'ar' => str_replace('{name}', $name['ar'], $template['description']['ar'] ?? ''),
                    ],
                    'robots' => $template['robots'] ?? 'index,follow',
                ]);

                $count++;
            }
        });

        return $count;
    }

    private function isFilled(?SeoPage $seoPage): bool
    {
        if (! $seoPage) {
            return false;
        }

        return filled($seoPage->getTranslation('title', 'en')) || filled($seoPage->getTranslation('title', 'ar'));
    }
}
