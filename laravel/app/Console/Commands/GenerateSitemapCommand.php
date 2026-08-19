<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\StaticPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'seo:sitemap';

    protected $description = 'Generate public/sitemap_index.xml and its sub-sitemaps for both languages';

    private const LANGS = ['ar', 'en'];

    private const STATIC_ROUTES = [
        ['route' => 'web.home', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['route' => 'web.products.index', 'priority' => '0.8', 'changefreq' => 'daily'],
        ['route' => 'web.blog.index', 'priority' => '0.6', 'changefreq' => 'weekly'],
        ['route' => 'web.faqs.index', 'priority' => '0.4', 'changefreq' => 'monthly'],
        ['route' => 'web.contact.index', 'priority' => '0.4', 'changefreq' => 'monthly'],
    ];

    public function handle(): int
    {
        $sitemaps = [];

        $staticUrls = [];
        foreach (self::STATIC_ROUTES as $item) {
            if (! Route::has($item['route'])) {
                continue;
            }

            foreach (self::LANGS as $lang) {
                $staticUrls[] = [
                    'loc' => route($item['route'], ['lang' => $lang]),
                    'lastmod' => now()->toAtomString(),
                    'priority' => $item['priority'],
                    'changefreq' => $item['changefreq'],
                ];
            }
        }

        foreach (StaticPage::query()->active()->get() as $page) {
            foreach (self::LANGS as $lang) {
                $staticUrls[] = [
                    'loc' => route('web.pages.show', ['lang' => $lang, 'slug' => $page->slug]),
                    'lastmod' => $page->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'priority' => '0.4',
                    'changefreq' => 'monthly',
                ];
            }
        }

        File::put(public_path('sitemap_static.xml'), $this->buildXml($staticUrls));
        $sitemaps[] = url('/sitemap_static.xml');

        $catUrls = [];
        Category::query()->active()->orderBy('id')->chunk(200, function ($categories) use (&$catUrls): void {
            foreach ($categories as $category) {
                foreach (self::LANGS as $lang) {
                    $catUrls[] = [
                        'loc' => route('web.categories.show', ['lang' => $lang, 'categorySlug' => $category->slug]),
                        'lastmod' => $category->updated_at?->toAtomString() ?? now()->toAtomString(),
                        'priority' => '0.6',
                        'changefreq' => 'weekly',
                    ];
                }
            }
        });
        File::put(public_path('sitemap_categories.xml'), $this->buildXml($catUrls));
        $sitemaps[] = url('/sitemap_categories.xml');

        $productBatch = [];
        $productIndex = 0;
        Product::query()->active()->orderBy('id')->chunk(500, function ($products) use (&$productBatch, &$productIndex, &$sitemaps): void {
            foreach ($products as $product) {
                foreach (self::LANGS as $lang) {
                    $productBatch[] = [
                        'loc' => route('web.products.show', ['lang' => $lang, 'slug' => $product->slug]),
                        'lastmod' => $product->updated_at?->toAtomString() ?? now()->toAtomString(),
                        'priority' => '0.7',
                        'changefreq' => 'weekly',
                    ];
                }
            }

            if (count($productBatch) >= 1000) {
                $filename = 'sitemap_products_'.$productIndex.'.xml';
                File::put(public_path($filename), $this->buildXml($productBatch));
                $sitemaps[] = url('/'.$filename);
                $productBatch = [];
                $productIndex++;
            }
        });
        if (! empty($productBatch)) {
            $filename = 'sitemap_products_'.$productIndex.'.xml';
            File::put(public_path($filename), $this->buildXml($productBatch));
            $sitemaps[] = url('/'.$filename);
        }

        $blogUrls = [];
        BlogPost::query()->published()->orderBy('id')->chunk(500, function ($posts) use (&$blogUrls): void {
            foreach ($posts as $post) {
                foreach (self::LANGS as $lang) {
                    $blogUrls[] = [
                        'loc' => route('web.blog.show', ['lang' => $lang, 'slug' => $post->slug]),
                        'lastmod' => $post->updated_at?->toAtomString() ?? now()->toAtomString(),
                        'priority' => '0.6',
                        'changefreq' => 'monthly',
                    ];
                }
            }
        });
        if (! empty($blogUrls)) {
            File::put(public_path('sitemap_blog.xml'), $this->buildXml($blogUrls));
            $sitemaps[] = url('/sitemap_blog.xml');
        }

        File::put(public_path('sitemap_index.xml'), $this->buildIndex($sitemaps));

        if (File::exists(public_path('sitemap.xml'))) {
            File::delete(public_path('sitemap.xml'));
        }

        $this->info('Sitemap index generated with '.count($sitemaps).' sub-sitemap(s).');

        return self::SUCCESS;
    }

    private function buildXml(array $urls): string
    {
        $lines = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($urls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1).'</loc>';
            $lines[] = '    <lastmod>'.$url['lastmod'].'</lastmod>';
            $lines[] = '    <changefreq>'.$url['changefreq'].'</changefreq>';
            $lines[] = '    <priority>'.$url['priority'].'</priority>';
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines);
    }

    private function buildIndex(array $sitemapUrls): string
    {
        $lines = ['<?xml version="1.0" encoding="UTF-8"?>', '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($sitemapUrls as $url) {
            $lines[] = '  <sitemap>';
            $lines[] = '    <loc>'.htmlspecialchars($url, ENT_XML1).'</loc>';
            $lines[] = '    <lastmod>'.now()->toAtomString().'</lastmod>';
            $lines[] = '  </sitemap>';
        }

        $lines[] = '</sitemapindex>';

        return implode("\n", $lines);
    }
}
