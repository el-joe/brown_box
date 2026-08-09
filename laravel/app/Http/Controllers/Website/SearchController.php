<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = $request->string('q')->toString();

        $products = Product::query()->active()
            ->search($keyword)
            ->with(['images', 'brand'])
            ->paginate(24)
            ->withQueryString();

        return view('website.search.index', [
            'keyword' => $keyword,
            'products' => $products,
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $keyword = $request->string('q')->toString();

        $products = Product::query()->active()
            ->search($keyword)
            ->take(8)
            ->get(['id', 'name', 'slug', 'price']);

        return response()->json([
            'results' => $products->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
            ]),
        ]);
    }
}
