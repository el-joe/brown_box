<?php

namespace App\Http\Controllers\Website;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:2000'],
        ]);

        $hasDeliveredOrder = $customer->orders()
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->where('status', OrderStatus::Delivered)
            ->exists();

        if (! $hasDeliveredOrder) {
            return back()->with('error', __('website.review_requires_purchase'));
        }

        $alreadyReviewed = ProductReview::query()
            ->where('product_id', $product->id)
            ->where('customer_id', $customer->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', __('website.review_already_submitted'));
        }

        ProductReview::query()->create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'is_approved' => false,
        ]);

        return back()->with('success', __('website.review_submitted_pending'));
    }
}
