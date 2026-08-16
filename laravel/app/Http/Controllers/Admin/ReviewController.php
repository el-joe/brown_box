<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = ProductReview::query()
            ->with(['product', 'customer'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', [
            'reviews' => $reviews,
        ]);
    }

    public function approve(ProductReview $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', __('Review approved.'));
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', __('Review deleted.'));
    }
}
