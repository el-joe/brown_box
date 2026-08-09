<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function paymobCallback(Request $request): JsonResponse
    {
        // TODO: Verify Paymob HMAC signature and update order payment_status accordingly.
        return response()->json(['status' => 'received']);
    }

    public function uploadProof(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $path = $data['proof']->store('payment-proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'pending_verification',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('website.proof_uploaded'),
            ]);
        }

        return back()->with('success', __('website.proof_uploaded'));
    }
}
