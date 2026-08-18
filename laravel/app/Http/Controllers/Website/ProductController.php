<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Request $request): View
    {
        $slug = $request->route('slug');

        $product = Product::query()
            ->with([
                'category',
                'brand',
                'productImages',
                'variants' => fn ($q) => $q->active()->orderBy('sort_order'),
                'variants.variantAttributes.attribute',
                'variants.variantAttributes.attributeValue',
                'variants.stocks',
                'stocks',
                'flashSaleItems.flashSale',
                'reviews' => fn ($q) => $q->approved()->with('customer')->latest()->take(10),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        abort_if(! $product->is_active, 404);

        $relatedProducts = Product::query()->active()
            ->with(['productImages'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(8)
            ->get();

        $activeFlashSaleItem = $product->flashSaleItems
            ->whereNull('variant_id')
            ->first(fn ($item) => $item->flashSale
                && $item->flashSale->is_active
                && $item->flashSale->starts_at <= now()
                && $item->flashSale->ends_at >= now());

        // Build a JSON-serialisable map of variant id => attributes/price/image for the
        // client-side variant selector script.
        $variantsJson = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) ($variant->price ?? $variant->product?->price ?? 0),
                'compare_price' => $variant->compare_price ? (float) $variant->compare_price : null,
                'image' => $variant->image ? asset_url($variant->image) : null,
                'stock' => (int) $variant->stocks->sum('qty'),
                'attributes' => $variant->variantAttributes->mapWithKeys(fn ($va) => [
                    $va->product_attribute_id => $va->product_attribute_value_id,
                ]),
            ];
        })->values();

        $attributeOptions = $product->variants
            ->flatMap->variantAttributes
            ->groupBy('product_attribute_id')
            ->map(function ($items) {
                $attribute = $items->first()->attribute;

                return [
                    'id' => $attribute?->id,
                    'name' => $attribute?->name,
                    'values' => $items->pluck('attributeValue')->unique('id')->values()->map(fn ($v) => [
                        'id' => $v->id,
                        'value' => $v->value,
                    ]),
                ];
            })->values();

        $customerId = Auth::guard('customer')->id();
        $sessionId = session()->getId();

        $isWishlisted = Wishlist::query()
            ->forOwner($customerId, $sessionId)
            ->where('product_id', $product->id)
            ->exists();

        $wishlistCount = Wishlist::query()
            ->forOwner($customerId, $sessionId)
            ->count();

        return view('website.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'activeFlashSaleItem' => $activeFlashSaleItem,
            'variantsJson' => $variantsJson,
            'attributeOptions' => $attributeOptions,
            'averageRating' => $product->reviews->avg('rating') ?? 0,
            'reviewCount' => $product->reviews->count(),
            'isWishlisted' => $isWishlisted,
            'wishlistCount' => $wishlistCount,
        ]);
    }
}
