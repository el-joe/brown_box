<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Return available shipping companies/rates for a given city.
     */
    public function companies(Request $request): JsonResponse
    {
        $data = $request->validate([
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ]);

        $rates = ShippingRate::query()
            ->with('shippingCompany')
            ->where('city_id', $data['city_id'])
            ->whereHas('shippingCompany', fn ($q) => $q->where('is_active', true))
            ->get()
            ->map(fn (ShippingRate $rate) => [
                'id' => $rate->id,
                'shipping_company_id' => $rate->shipping_company_id,
                'name' => $rate->shippingCompany->name,
                'logo' => $rate->shippingCompany->logo ? asset_url($rate->shippingCompany->logo) : null,
                'price' => (float) $rate->price,
                'estimated_days' => $rate->estimated_days,
            ])
            ->values();

        return response()->json(['rates' => $rates]);
    }
}
