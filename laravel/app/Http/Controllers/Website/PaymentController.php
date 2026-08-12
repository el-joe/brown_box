<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly OrderService $orderService,
    ) {
    }

    public function paymobCallback(Request $request): JsonResponse
    {
        // Paymob sends the transaction either as a flat query string (GET
        // redirect) or as {"type": "...", "obj": {...}} (POST webhook).
        $transaction = $request->input('obj', $request->query());
        $receivedHmac = (string) $request->query('hmac', $request->input('hmac', ''));

        if (! is_array($transaction) || empty($transaction)) {
            Log::warning('Paymob callback received with no transaction payload.');

            return response()->json(['status' => 'ignored']);
        }

        if (! $this->paymentService->verifyPaymobHmac($transaction, $receivedHmac)) {
            Log::warning('Paymob callback HMAC verification failed.', ['transaction' => $transaction]);

            return response()->json(['status' => 'invalid_signature']);
        }

        $merchantOrderId = data_get($transaction, 'order.merchant_order_id');
        $order = $merchantOrderId
            ? Order::query()->where('order_number', $merchantOrderId)->first()
            : null;

        if (! $order) {
            Log::warning('Paymob callback for unknown order.', ['merchant_order_id' => $merchantOrderId]);

            return response()->json(['status' => 'order_not_found']);
        }

        $success = filter_var(data_get($transaction, 'success'), FILTER_VALIDATE_BOOLEAN);

        if ($success) {
            $this->orderService->verifyPayment($order->id, null, 'Payment confirmed via Paymob.');
        } else {
            $this->orderService->rejectPayment($order->id, null, 'Payment declined by Paymob.');
        }

        // Paymob requires a 200 response to consider the callback acknowledged.
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
