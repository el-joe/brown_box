<?php

namespace App\Services;

use App\Enums\PaymentGateway;
use App\Enums\RefundStatus;
use App\Enums\TransactionType;
use App\Jobs\ProcessRefundNotification;
use App\Jobs\SendCustomerNotification;
use App\Models\RefundRequest;
use App\Models\Transaction;
use App\Notifications\RefundApproved;
use App\Notifications\RefundRejected;
use Illuminate\Support\Facades\DB;

class RefundService
{
    public function __construct(private readonly AccountingService $accountingService)
    {
    }

    public function approve(RefundRequest $refundRequest, float $amount, ?string $notes, ?int $adminId = null): RefundRequest
    {
        return DB::transaction(function () use ($refundRequest, $amount, $notes, $adminId) {
            $refundRequest->update([
                'status' => RefundStatus::Approved,
                'refund_amount' => $amount,
                'admin_notes' => $notes,
            ]);

            $order = $refundRequest->order;

            // Bank transfer / manual gateways have no online refund API — the
            // refund is settled offline by the finance team, so we simply
            // mark the order and refund as refunded/processed here.
            $order->update([
                'status' => 'refunded',
                'payment_status' => 'refunded',
            ]);

            $refundRequest->update([
                'status' => RefundStatus::Processed,
                'processed_at' => now(),
            ]);

            Transaction::query()->create([
                'type' => TransactionType::Debit,
                'model_type' => \App\Models\Order::class,
                'model_id' => $order->id,
                'amount' => $amount,
                'description' => __('Refund for order :number', ['number' => $order->order_number]),
                'reference' => $order->order_number,
            ]);

            $this->accountingService->recordExpense(
                category: 'sales',
                amount: $amount,
                description: __('Refund — Order :number', ['number' => $order->order_number]),
                referenceType: RefundRequest::class,
                referenceId: $refundRequest->id,
                adminId: $adminId,
            );

            $refundRequest->refresh();

            ProcessRefundNotification::dispatch($refundRequest);

            if ($refundRequest->customer) {
                SendCustomerNotification::dispatch($refundRequest->customer, new RefundApproved($order, $amount));
            }

            return $refundRequest;
        });
    }

    public function reject(RefundRequest $refundRequest, string $notes, ?int $adminId = null): RefundRequest
    {
        return DB::transaction(function () use ($refundRequest, $notes) {
            $refundRequest->update([
                'status' => RefundStatus::Rejected,
                'admin_notes' => $notes,
                'processed_at' => now(),
            ]);

            $refundRequest->refresh();

            ProcessRefundNotification::dispatch($refundRequest);

            if ($refundRequest->customer && $refundRequest->order) {
                SendCustomerNotification::dispatch($refundRequest->customer, new RefundRejected($refundRequest->order, $notes));
            }

            return $refundRequest;
        });
    }
}
