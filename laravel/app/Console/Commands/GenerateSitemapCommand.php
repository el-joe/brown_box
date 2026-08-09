<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\StaticPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'seo:sitemap';

    protected $description = 'Generate public/sitemap.xml with products, categories, and static pages';

    private const STATIC_ROUTES = [
        ['route' => 'web.home', 'priority' => '1.0'],
        ['route' => 'web.products.index', 'priority' => '0.8'],
        ['route' => 'web.cart.index', 'priority' => '0.3'],
        ['route' => 'web.checkout.index', 'priority' => '0.3'],
        ['route' => 'web.blog.index', 'priority' => '0.5'],
        ['route' => 'web.faqs.index', 'priority' => '0.3'],
        ['route' => 'web.contact.index', 'priority' => '0.3'],
    ];

    public function handle(): int
    {
        $lang = config('app.locale', 'en');
        $urls = [];

        foreach (self::STATIC_ROUTES as $static) {
            if (! Route::has($static['route'])) {
                continue;
            }

            $urls[] = [
                'loc' => route($static['route'], ['lang' => $lang]),
                'lastmod' => now()->toAtomString(),
                'priority' => $static['priority'],
            ];
        }

        foreach (StaticPage::query()->active()->get() as $page) {
            $urls[] = [
                'loc' => route('web.pages.show', ['lang' => $lang, 'slug' => $page->slug]),
                'lastmod' => $page->updated_at?->toAtomString() ?? now()->toAtomString(),
                'priority' => '0.4',
            ];
        }

        foreach (Category::query()->active()->get() as $category) {
            $urls[] = [
                'loc' => route('web.categories.show', ['lang' => $lang, 'categorySlug' => $category->slug]),
                'lastmod' => $category->updated_at?->toAtomString() ?? now()->toAtomString(),
                'priority' => '0.6',
            ];
        }

        foreach (Product::query()->active()->get() as $product) {
            $urls[] = [
                'loc' => route('web.products.show', ['lang' => $lang, 'slug' => $product->slug]),
                'lastmod' => $product->updated_at?->toAtomString() ?? now()->toAtomString(),
                'priority' => '0.7',
            ];
        }

        $xml = $this->buildXml($urls);

        File::put(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap generated with '.count($urls).' URL(s) at public/sitemap.xml.');

        return self::SUCCESS;
    }

    private function buildXml(array $urls): string
    {
        $lines = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($urls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1).'</loc>';
            $lines[] = '    <lastmod>'.$url['lastmod'].'</lastmod>';
            $lines[] = '    <priority>'.$url['priority'].'</priority>';
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines);
    }
}
