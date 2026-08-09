<?php

namespace App\Services;

use App\Events\PaymentVerified;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function __construct(private readonly OrderRepositoryInterface $orders)
    {
    }

    /**
     * Handle a payment gateway callback: locate the order by its payment
     * reference and mark it paid or failed depending on the gateway status.
     */
    public function handleGatewayCallback(string $paymentReference, string $gatewayStatus, array $payload = []): Model
    {
        return DB::transaction(function () use ($paymentReference, $gatewayStatus, $payload) {
            $order = $this->orders->all(['payment_reference' => $paymentReference])->first();

            if (! $order) {
                throw new RuntimeException("No order found for payment reference {$paymentReference}.");
            }

            $paymentStatus = match ($gatewayStatus) {
                'success', 'paid', 'completed' => 'paid',
                'failed', 'declined' => 'failed',
                default => 'pending',
            };

            $order->update([
                'payment_status' => $paymentStatus,
                'payment_reference' => $payload['reference'] ?? $order->payment_reference,
            ]);

            if ($paymentStatus === 'paid') {
                $this->orders->addStatusHistory($order->id, $order->status, 'Payment confirmed via gateway.');
                event(new PaymentVerified($order));
            }

            return $order->fresh();
        });
    }

    /**
     * Manual verification by an admin (e.g. bank transfer proof review).
     */
    public function verifyManually(int $orderId, int $adminId, bool $approved, ?string $notes = null): Model
    {
        return DB::transaction(function () use ($orderId, $adminId, $approved, $notes) {
            $order = $this->orders->findOrFail($orderId);

            $order->update([
                'payment_status' => $approved ? 'paid' : 'failed',
            ]);

            $this->orders->addStatusHistory(
                $orderId,
                $order->status,
                $notes ?? ($approved ? 'Payment verified manually.' : 'Payment rejected manually.'),
                $adminId,
            );

            if ($approved) {
                event(new PaymentVerified($order));
            }

            return $order->fresh();
        });
    }
}
