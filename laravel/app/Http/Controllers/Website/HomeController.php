<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Do NOT wrap Eloquent collections in Cache::remember() with the
        // database cache driver — unserialize() can't restore model objects
        // before autoloading completes, causing "incomplete object" errors.
        $categories = Category::query()->active()->roots()->orderBy('sort_order')->get();

        $categorySections = $categories->take(3)->map(function (Category $category) {
            $products = Product::query()
                ->active()
                ->where('category_id', $category->id)
                ->with(['productImages', 'category'])
                ->latest()
                ->take(8)
                ->get();

            return ['category' => $category, 'products' => $products];
        })->filter(fn (array $section) => $section['products']->isNotEmpty())->values();

        return view('website.home.index', [
            'categories' => $categories,
            'banners' => Banner::query()->active()->orderBy('sort_order')->get(),
            'featuredProducts' => Product::query()->active()->featured()->with(['productImages', 'category'])->latest()->take(12)->get(),
            'flashSale' => FlashSale::query()->active()->with('items.product.productImages')->first(),
            'newArrivals' => Product::query()->active()->with(['productImages', 'category'])->latest()->take(12)->get(),
            'brands' => Brand::query()->active()->take(12)->get(),
            'categorySections' => $categorySections,
        ]);
    }
}
