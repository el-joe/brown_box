<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('website.home.index', [
            'categories' => Category::query()->active()->roots()->orderBy('sort_order')->get(),
            'featuredProducts' => Product::query()->active()->featured()->with(['images', 'category'])->latest()->take(12)->get(),
            'flashSale' => FlashSale::query()->active()->with('items.product.images')->first(),
            'newArrivals' => Product::query()->active()->with(['images', 'category'])->latest()->take(12)->get(),
            'brands' => Brand::query()->active()->take(12)->get(),
        ]);
    }
}
