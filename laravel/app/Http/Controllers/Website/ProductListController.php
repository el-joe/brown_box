<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductListController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->route('categorySlug');
        $category = $categorySlug
            ? Category::query()->active()->where('slug', $categorySlug)->firstOrFail()
            : null;

        $query = Product::query()->active();

        if ($category) {
            $categoryIds = $category->children()->pluck('id')->push($category->id);
            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->float('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->float('max_price'));
        }

        $query->when($request->filled('q'), fn ($q) => $q->search($request->string('q')->toString()));

        $sort = $request->string('sort')->toString();
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest(),
            default => $query->orderBy('name'),
        };

        $products = $query->with(['images', 'brand'])->paginate(24)->withQueryString();

        return view('website.products.index', [
            'category' => $category,
            'products' => $products,
            'categories' => Category::query()->active()->roots()->get(),
            'brands' => Brand::query()->active()->get(),
        ]);
    }
}
