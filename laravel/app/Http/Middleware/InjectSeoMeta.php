<?php

namespace App\Http\Middleware;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\StaticPage;
use App\Repositories\Contracts\SeoPageRepositoryInterface;
use App\Services\SeoDefaultsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class InjectSeoMeta
{
    private const NOINDEX_ROUTES = [
        'web.cart.index',
        'web.checkout.index',
        'web.checkout.success',
        'web.wishlist.index',
        'web.track-order.index',
        'web.search.index',
        'web.search.suggestions',
        'web.account.index',
        'web.account.orders',
        'web.account.orders.show',
        'web.account.profile',
        'web.account.addresses.index',
        'web.account.wishlist',
        'web.account.login',
        'web.account.register',
    ];

    private const ROUTE_PAGE_KEYS = [
        'web.home' => 'home',
        'web.blog.index' => 'blog',
        'web.cart.index' => 'cart',
        'web.checkout.index' => 'checkout',
        'web.contact.index' => 'contact-us',
    ];

    public function __construct(
        private readonly SeoPageRepositoryInterface $seoPages,
        private readonly SeoDefaultsService $seoDefaults,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if (! $routeName || $request->expectsJson()) {
            return $next($request);
        }

        $cacheKey = 'seo.page.' . md5($request->path());

        $seoPage = Cache::remember(
            $cacheKey,
            now()->addHours(6),
            function () use ($request, $routeName) {
                if ($routeName === 'web.products.show') {
                    $product = Product::query()->where('slug', $request->route('slug'))->first();

                    return $product
                        ? ($this->seoPages->findForModel(Product::class, $product->id) ?? $this->seoDefaults->forModel($product))
                        : null;
                }

                if ($routeName === 'web.categories.show') {
                    $slug = $request->route('categorySlug');

                    if (! $slug) {
                        return $this->seoPages->findByPageKey('category-list') ?? $this->seoDefaults->forPageKey('category-list');
                    }

                    $category = Category::query()->where('slug', $slug)->first();

                    return $category
                        ? ($this->seoPages->findForModel(Category::class, $category->id) ?? $this->seoDefaults->forModel($category))
                        : null;
                }

                if ($routeName === 'web.blog.show') {
                    $post = BlogPost::query()->where('slug', $request->route('slug'))->first();

                    return $post
                        ? ($this->seoPages->findForModel(BlogPost::class, $post->id) ?? $this->seoDefaults->forModel($post))
                        : null;
                }

                if ($routeName === 'web.pages.show') {
                    $page = StaticPage::query()->where('slug', $request->route('slug'))->first();

                    return $page
                        ? ($this->seoPages->findForModel(StaticPage::class, $page->id) ?? $this->seoDefaults->forModel($page))
                        : null;
                }

                $pageKey = self::ROUTE_PAGE_KEYS[$routeName] ?? $routeName;

                return $this->seoPages->findByPageKey($pageKey) ?? $this->seoDefaults->forPageKey($pageKey);
            },
        );

        View::share('seo', $seoPage);

        $isNoIndex = in_array($routeName, self::NOINDEX_ROUTES, true)
            || ($request->filled('page') && $request->integer('page') > 1);

        View::share('pageRobots', $isNoIndex ? 'noindex,follow' : null);

        return $next($request);
    }
}
