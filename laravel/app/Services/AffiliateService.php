<?php

namespace App\Services;

use App\Enums\CommissionTierType;
use App\Enums\CommissionType;
use App\Events\AffiliatePayoutApproved;
use App\Models\Admin;
use App\Models\Affiliate;
use App\Models\AffiliateCategoryCommission;
use App\Models\Order;
use App\Models\Transaction;
use App\Notifications\PayoutRequestedNotification;
use App\Repositories\Contracts\AffiliateCommissionRepositoryInterface;
use App\Repositories\Contracts\AffiliateRepositoryInterface;
use App\Repositories\Contracts\PayoutRequestRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;
use RuntimeException;

class AffiliateService
{
    public function __construct(
        private readonly AffiliateRepositoryInterface $affiliates,
        private readonly AffiliateCommissionRepositoryInterface $commissions,
        private readonly PayoutRequestRepositoryInterface $payoutRequests,
    ) {}

    public function findByCode(string $code): ?Model
    {
        return $this->affiliates->findByCode($code);
    }

    /**
     * Compute the commission amount for an order attributed to an affiliate.
     *
     * - fixed_all_orders: a single fixed percentage applied to the order subtotal.
     * - per_category: each order item is matched against the affiliate's
     *   category commission config; fixed_percentage rates apply directly,
     *   tiered rates are matched against the order subtotal. Items whose
     *   category has no configured commission earn nothing.
     */
    public function calculateCommission(Model $affiliate, Order $order): float
    {
        $subtotal = (float) $order->subtotal;

        if ($affiliate->commission_type === CommissionType::FixedAllOrders->value) {
            return round($subtotal * ((float) $affiliate->fixed_commission_rate / 100), 2);
        }

        $items = $order->items()->with('product')->get();

        $categoryCommissions = AffiliateCategoryCommission::query()
            ->where('affiliate_id', $affiliate->id)
            ->whereIn('category_id', $items->pluck('product.category_id')->filter()->unique())
            ->with('tiers')
            ->get()
            ->keyBy('category_id');

        $total = 0.0;

        foreach ($items as $item) {
            $categoryId = $item->product?->category_id;
            $categoryCommission = $categoryId ? $categoryCommissions->get($categoryId) : null;

            if (! $categoryCommission) {
                continue;
            }

            $itemTotal = (float) $item->price * (int) $item->quantity;

            if ($categoryCommission->tier_type === CommissionTierType::FixedPercentage->value) {
                $total += $itemTotal * ((float) $categoryCommission->rate / 100);

                continue;
            }

            $tier = $categoryCommission->tiers
                ->filter(fn ($tier) => (float) $tier->min_amount <= $subtotal
                    && ($tier->max_amount === null || (float) $tier->max_amount >= $subtotal))
                ->sortByDesc('min_amount')
                ->first();

            if ($tier) {
                $total += $itemTotal * ((float) $tier->rate / 100);
            }
        }

        return round($total, 2);
    }

    /**
     * Create a pending commission record for an order and attach it to the order.
     */
    public function recordCommissionForOrder(Model $affiliate, Order $order): Model
    {
        return DB::transaction(function () use ($affiliate, $order) {
            $amount = $this->calculateCommission($affiliate, $order);

            $commission = $affiliate->commissions()->create([
                'order_id' => $order->id,
                'amount' => $amount,
                'status' => 'pending',
                'available_at' => ($order->delivered_at ?? now())->copy()->addDays(14),
            ]);

            $order->update(['affiliate_commission' => $amount]);

            return $commission;
        });
    }

    /**
     * Add a manual, order-independent commission entry for an affiliate
     * (e.g. a bonus or correction), available for approval immediately.
     */
    public function addManualCommission(int $affiliateId, float $amount, ?string $notes = null): Model
    {
        $affiliate = $this->affiliates->findOrFail($affiliateId);

        return $affiliate->commissions()->create([
            'order_id' => null,
            'amount' => $amount,
            'status' => 'pending',
            'available_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function approveCommission(int $commissionId): void
    {
        DB::transaction(function () use ($commissionId) {
            $commission = $this->commissions->findOrFail($commissionId);
            $commission->update(['status' => 'approved']);
            $affiliate = $this->affiliates->incrementBalance($commission->affiliate_id, (float) $commission->amount);
            $affiliate->increment('total_earned', (float) $commission->amount);

            $this->recordTransaction(
                $affiliate,
                'credit',
                (float) $commission->amount,
                __('Commission approved for order #:order', ['order' => $commission->order?->order_number ?? $commission->id]),
            );
        });
    }

    /**
     * Request a payout for an affiliate, ensuring the requested amount does
     * not exceed the available balance. Deducts the balance immediately via
     * a Transaction ledger entry and notifies admins.
     */
    public function requestPayout(int $affiliateId, float $amount, string $paymentMethod, array $paymentDetails = []): Model
    {
        return DB::transaction(function () use ($affiliateId, $amount, $paymentMethod, $paymentDetails) {
            $affiliate = $this->affiliates->findOrFail($affiliateId);

            $minPayout = (float) setting('affiliate.min_payout_amount', 0);

            if ($amount <= 0 || $amount > (float) $affiliate->balance) {
                throw new InvalidArgumentException('Requested amount exceeds available balance.');
            }

            if ($minPayout > 0 && $amount < $minPayout) {
                throw new InvalidArgumentException("Minimum payout amount is {$minPayout}.");
            }

            $payout = $this->payoutRequests->create([
                'affiliate_id' => $affiliateId,
                'amount' => $amount,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_details' => $paymentDetails,
            ]);

            $affiliate = $this->affiliates->decrementBalance($affiliateId, $amount);

            $this->recordTransaction(
                $affiliate,
                'debit',
                $amount,
                __('Payout request #:id (:method)', ['id' => $payout->id, 'method' => $paymentMethod]),
            );

            $admins = Admin::query()->active()->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new PayoutRequestedNotification($payout));
            }

            return $payout;
        });
    }

    private function recordTransaction(Affiliate $affiliate, string $type, float $amount, string $description): void
    {
        $before = (float) $affiliate->balance + ($type === 'debit' ? $amount : -$amount);

        Transaction::query()->create([
            'type' => $type,
            'model_type' => Affiliate::class,
            'model_id' => $affiliate->id,
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => (float) $affiliate->balance,
            'description' => $description,
        ]);
    }

    public function approvePayout(int $payoutRequestId): Model
    {
        return DB::transaction(function () use ($payoutRequestId) {
            $payout = $this->payoutRequests->findOrFail($payoutRequestId);

            if ($payout->status !== 'pending') {
                throw new RuntimeException('Only pending payout requests can be approved.');
            }

            $payout->update([
                'status' => 'approved',
                'processed_at' => now(),
            ]);

            event(new AffiliatePayoutApproved($payout));

            return $payout;
        });
    }

    /**
     * Mark a payout request as paid. The affiliate's balance was already
     * deducted when the payout was requested.
     */
    public function markPayoutAsPaid(int $payoutRequestId): Model
    {
        return DB::transaction(function () use ($payoutRequestId) {
            $payout = $this->payoutRequests->findOrFail($payoutRequestId);

            if (! in_array($payout->status, ['pending', 'approved'], true)) {
                throw new RuntimeException('Only pending or approved payout requests can be marked as paid.');
            }

            $payout->update([
                'status' => 'paid',
                'processed_at' => now(),
            ]);

            return $payout;
        });
    }

    /**
     * Reject a pending payout request, refunding the previously deducted
     * amount back to the affiliate's balance.
     */
    public function rejectPayout(int $payoutRequestId, ?string $notes = null): Model
    {
        $payout = $this->payoutRequests->findOrFail($payoutRequestId);

        if ($payout->status !== 'pending') {
            throw new RuntimeException('Only pending payout requests can be rejected.');
        }

        return DB::transaction(function () use ($payout, $notes) {
            $affiliate = $this->affiliates->incrementBalance($payout->affiliate_id, (float) $payout->amount);

            $this->recordTransaction(
                $affiliate,
                'credit',
                (float) $payout->amount,
                __('Payout request #:id rejected, amount refunded', ['id' => $payout->id]),
            );

            $payout->update([
                'status' => 'rejected',
                'admin_notes' => $notes,
                'processed_at' => now(),
            ]);

            return $payout;
        });
    }
}
