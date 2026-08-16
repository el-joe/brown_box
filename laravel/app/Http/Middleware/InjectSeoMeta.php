<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\SeoPageRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class InjectSeoMeta
{
    public function __construct(private readonly SeoPageRepositoryInterface $seoPages)
    {
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
                        ? $this->seoPages->findForModel(Product::class, $product->id)
                        : null;
                }

                if ($routeName === 'web.categories.show') {
                    $category = Category::query()->where('slug', $request->route('categorySlug'))->first();

                    return $category
                        ? $this->seoPages->findForModel(Category::class, $category->id)
                        : null;
                }

                return $this->seoPages->findByPageKey($routeName);
            },
        );

        View::share('seo', $seoPage);

        return $next($request);
    }
}
