<?php

namespace App\Http\Middleware;

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
        $pageKey = $request->route()?->getName();

        if (! $pageKey || $request->expectsJson()) {
            return $next($request);
        }

        $seoPage = Cache::remember(
            "seo.page.{$pageKey}",
            now()->addHours(6),
            fn () => $this->seoPages->findByPageKey($pageKey),
        );

        View::share('seo', $seoPage);

        return $next($request);
    }
}
