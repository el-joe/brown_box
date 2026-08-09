<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class CatalogCacheObserver
{
    public function saved(): void
    {
        $this->flush();
    }

    public function deleted(): void
    {
        $this->flush();
    }

    private function flush(): void
    {
        Cache::forget('categories.active_tree');
        Cache::forget('flash_sales.active');
        Cache::forget('home.featured_products');
        Cache::forget('home.new_arrivals');
    }
}
