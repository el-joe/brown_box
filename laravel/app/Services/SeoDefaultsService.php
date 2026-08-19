<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SeoPage;
use App\Models\StaticPage;
use Illuminate\Support\Str;

/**
 * Generates in-memory (non-persisted) SeoPage fallbacks for pages/records
 * that don't have an admin-authored SeoPage yet, so every page still ships
 * a meaningful title and description instead of falling back to just the
 * site name.
 */
class SeoDefaultsService
{
    private const PAGE_KEY_DESCRIPTIONS = [
        'home' => [
            'en' => 'Shop the latest products at :site — quality guaranteed, fast delivery, and unbeatable prices.',
            'ar' => 'تسوق أحدث المنتجات في :site - جودة مضمونة، توصيل سريع، وأفضل الأسعار.',
        ],
        'blog' => [
            'en' => 'Tips, guides, and news from :site.',
            'ar' => 'نصائح وأدلة وأخبار من :site.',
        ],
        'category-list' => [
            'en' => 'Browse all product categories at :site.',
            'ar' => 'تصفح جميع فئات المنتجات في :site.',
        ],
        'cart' => [
            'en' => 'Review the items in your shopping cart at :site.',
            'ar' => 'راجع العناصر الموجودة في سلة التسوق الخاصة بك في :site.',
        ],
        'checkout' => [
            'en' => 'Complete your purchase securely at :site.',
            'ar' => 'أكمل عملية الشراء بأمان في :site.',
        ],
        'about-us' => [
            'en' => 'Learn more about :site and our mission.',
            'ar' => 'تعرف على المزيد حول :site ورسالتنا.',
        ],
        'contact-us' => [
            'en' => 'Get in touch with the :site team.',
            'ar' => 'تواصل مع فريق :site.',
        ],
    ];

    private const PAGE_KEY_LABELS = [
        'home' => ['en' => 'Home', 'ar' => 'الرئيسية'],
        'blog' => ['en' => 'Blog', 'ar' => 'المدونة'],
        'category-list' => ['en' => 'Categories', 'ar' => 'الفئات'],
        'cart' => ['en' => 'Cart', 'ar' => 'سلة التسوق'],
        'checkout' => ['en' => 'Checkout', 'ar' => 'إتمام الطلب'],
        'about-us' => ['en' => 'About Us', 'ar' => 'من نحن'],
        'contact-us' => ['en' => 'Contact Us', 'ar' => 'اتصل بنا'],
    ];

    public function forPageKey(string $pageKey): SeoPage
    {
        $seo = new SeoPage(['page_key' => $pageKey]);

        foreach (['en', 'ar'] as $lang) {
            $siteName = $this->siteName($lang);
            $label = self::PAGE_KEY_LABELS[$pageKey][$lang] ?? Str::headline($pageKey);
            $seo->setTranslation('title', $lang, $pageKey === 'home' ? $siteName : $label);

            if ($template = self::PAGE_KEY_DESCRIPTIONS[$pageKey][$lang] ?? null) {
                $seo->setTranslation('description', $lang, str_replace(':site', $siteName, $template));
            }
        }

        return $seo;
    }

    public function forModel(object $model): ?SeoPage
    {
        return match (true) {
            $model instanceof Product => $this->forProduct($model),
            $model instanceof Category => $this->forCategory($model),
            $model instanceof BlogPost => $this->forBlogPost($model),
            $model instanceof Brand => $this->forBrand($model),
            $model instanceof StaticPage => $this->forStaticPage($model),
            default => null,
        };
    }

    private function forProduct(Product $product): SeoPage
    {
        $seo = new SeoPage(['model_type' => Product::class, 'model_id' => $product->id]);

        foreach (['en', 'ar'] as $lang) {
            $name = $product->getTranslation('name', $lang, false) ?: $product->getTranslation('name', 'en', false);
            $seo->setTranslation('title', $lang, $name);

            $desc = $product->getTranslation('short_description', $lang, false)
                ?: $product->getTranslation('description', $lang, false);

            $seo->setTranslation('description', $lang, $desc
                ? Str::limit(strip_tags($desc), 155)
                : $this->genericDescription($lang, $name));
        }

        return $seo;
    }

    private function forCategory(Category $category): SeoPage
    {
        $seo = new SeoPage(['model_type' => Category::class, 'model_id' => $category->id]);

        foreach (['en', 'ar'] as $lang) {
            $name = $category->getTranslation('name', $lang, false) ?: $category->getTranslation('name', 'en', false);
            $siteName = $this->siteName($lang);

            $seo->setTranslation('title', $lang, $name);
            $seo->setTranslation('description', $lang, $lang === 'ar'
                ? "تسوق {$name} في {$siteName}. تصفح مجموعتنا الواسعة من منتجات {$name} بأفضل الأسعار."
                : "Shop {$name} at {$siteName}. Browse our wide range of {$name} products at great prices.");
        }

        return $seo;
    }

    private function forBrand(Brand $brand): SeoPage
    {
        $seo = new SeoPage(['model_type' => Brand::class, 'model_id' => $brand->id]);

        foreach (['en', 'ar'] as $lang) {
            $name = $brand->getTranslation('name', $lang, false) ?: $brand->getTranslation('name', 'en', false);
            $siteName = $this->siteName($lang);

            $seo->setTranslation('title', $lang, $lang === 'ar' ? "منتجات {$name}" : "{$name} Products");
            $seo->setTranslation('description', $lang, $lang === 'ar'
                ? "تسوق منتجات {$name} في {$siteName}. اكتشف مجموعتنا من منتجات {$name}."
                : "Shop {$name} products at {$siteName}. Discover our collection of {$name} items.");
        }

        return $seo;
    }

    private function forBlogPost(BlogPost $post): SeoPage
    {
        $seo = new SeoPage(['model_type' => BlogPost::class, 'model_id' => $post->id]);

        foreach (['en', 'ar'] as $lang) {
            $title = $post->getTranslation('title', $lang, false) ?: $post->getTranslation('title', 'en', false);
            $seo->setTranslation('title', $lang, $title);

            $desc = $post->getTranslation('excerpt', $lang, false) ?: $post->getTranslation('content', $lang, false);
            $seo->setTranslation('description', $lang, $desc ? Str::limit(strip_tags($desc), 155) : $title);
        }

        return $seo;
    }

    private function forStaticPage(StaticPage $page): SeoPage
    {
        $seo = new SeoPage(['model_type' => StaticPage::class, 'model_id' => $page->id]);

        foreach (['en', 'ar'] as $lang) {
            $title = $page->getTranslation('title', $lang, false) ?: $page->getTranslation('title', 'en', false);
            $seo->setTranslation('title', $lang, $title);

            $content = $page->getTranslation('content', $lang, false);
            $seo->setTranslation('description', $lang, $content ? Str::limit(strip_tags($content), 155) : $title);
        }

        return $seo;
    }

    private function genericDescription(string $lang, string $name): string
    {
        $siteName = $this->siteName($lang);

        return $lang === 'ar'
            ? "تسوق {$name} في {$siteName}. جودة مضمونة وتوصيل سريع."
            : "Shop {$name} at {$siteName}. Quality guaranteed with fast delivery.";
    }

    private function siteName(string $lang): string
    {
        return setting('site_name_' . $lang) ?: setting('site_name_en') ?: config('app.name', 'Brown Box');
    }
}
