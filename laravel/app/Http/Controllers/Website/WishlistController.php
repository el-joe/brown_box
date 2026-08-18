<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $customerId = Auth::guard('customer')->id();
        $sessionId = session()->getId();

        $items = Wishlist::query()
            ->with('product', 'variant')
            ->forOwner($customerId, $sessionId)
            ->latest()
            ->get();

        return view('website.wishlist.index', [
            'items' => $items,
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
        ]);

        $customerId = Auth::guard('customer')->id();
        $sessionId = session()->getId();

        $query = Wishlist::query()
            ->forOwner($customerId, $sessionId)
            ->where('product_id', $data['product_id'])
            ->where('variant_id', $data['variant_id'] ?? null);

        $existing = $query->first();

        if ($existing) {
            $existing->delete();

            $count = Wishlist::query()->forOwner($customerId, $sessionId)->count();

            return response()->json([
                'success'        => true,
                'wishlisted'     => false,
                'wishlist_count' => $count,
                'message'        => __('website.removed_from_wishlist'),
            ]);
        }

        Wishlist::query()->create([
            'customer_id' => $customerId,
            'session_id' => $customerId ? null : $sessionId,
            'product_id' => $data['product_id'],
            'variant_id' => $data['variant_id'] ?? null,
        ]);

        $count = Wishlist::query()->forOwner($customerId, $sessionId)->count();

        return response()->json([
            'success'        => true,
            'wishlisted'     => true,
            'wishlist_count' => $count,
            'message'        => __('website.added_to_wishlist'),
        ]);
    }
}
