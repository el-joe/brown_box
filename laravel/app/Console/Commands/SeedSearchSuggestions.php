<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\SearchSuggestion;
use Illuminate\Console\Command;

class SeedSearchSuggestions extends Command
{
    protected $signature = 'search:seed';

    protected $description = 'Populate search suggestions from top searched keywords and catalog data';

    public function handle(): int
    {
        $keywords = collect();

        // Keywords already tracked via search usage (App\Services\SearchService::log),
        // ordered by popularity.
        $keywords = $keywords->merge(
            SearchSuggestion::query()->orderByDesc('hits')->limit(50)->pluck('keyword')
        );

        $keywords = $keywords->merge(
            Product::query()->active()->latest('id')->limit(50)->get()
                ->map(fn (Product $product) => $product->getTranslation('name', 'en'))
        );

        $keywords = $keywords->merge(
            Category::query()->active()->get()
                ->map(fn (Category $category) => $category->getTranslation('name', 'en'))
        );

        $keywords = $keywords->filter()->map(fn ($keyword) => trim($keyword))->filter()->unique();

        $seeded = 0;

        foreach ($keywords as $keyword) {
            $suggestion = SearchSuggestion::query()->firstOrCreate(
                ['keyword' => $keyword],
                ['hits' => 0, 'is_active' => true, 'sort_order' => 0]
            );

            if ($suggestion->wasRecentlyCreated) {
                $seeded++;
            }
        }

        $this->info("Seeded {$seeded} search suggestion(s).");

        return self::SUCCESS;
    }
}
