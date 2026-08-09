<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $coupon = Coupon::query()->valid()->where('code', $data['code'])->first();

        if (! $coupon) {
            return response()->json(['valid' => false, 'message' => __('website.invalid_coupon')], 422);
        }

        return response()->json([
            'valid' => true,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
        ]);
    }
}
