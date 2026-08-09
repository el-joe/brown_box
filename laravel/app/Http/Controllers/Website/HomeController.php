<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('website.home.index', [
            'categories' => Cache::remember(
                'categories.active_tree',
                now()->addHours(6),
                fn () => Category::query()->active()->roots()->orderBy('sort_order')->get(),
            ),
            'featuredProducts' => Cache::remember(
                'home.featured_products',
                now()->addMinutes(15),
                fn () => Product::query()->active()->featured()->with(['images', 'category'])->latest()->take(12)->get(),
            ),
            'flashSale' => Cache::remember(
                'flash_sales.active',
                now()->addMinutes(15),
                fn () => FlashSale::query()->active()->with('items.product.images')->first(),
            ),
            'newArrivals' => Cache::remember(
                'home.new_arrivals',
                now()->addMinutes(15),
                fn () => Product::query()->active()->with(['images', 'category'])->latest()->take(12)->get(),
            ),
            'brands' => Brand::query()->active()->take(12)->get(),
        ]);
    }
}
