<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Purchase;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseService
{
    public function __construct(
        private readonly PurchaseRepositoryInterface $purchases,
        private readonly StockService $stocks,
        private readonly AccountingService $accounting,
    ) {
    }

    public function find(int $id): ?Model
    {
        return $this->purchases->find($id);
    }

    public function nextInvoiceNumber(): string
    {
        return $this->purchases->nextInvoiceNumber();
    }

    public function create(array $data, array $items, int $adminId): Model
    {
        $totals = $this->calculateTotals($items, (float) ($data['discount_amount'] ?? 0), (float) ($data['tax_amount'] ?? 0));

        $purchase = $this->purchases->createWithItems([
            ...$data,
            'admin_id' => $adminId,
            'status' => $data['status'] ?? 'draft',
            'total_amount' => $totals['total_amount'],
            'net_amount' => $totals['net_amount'],
        ], $items);

        if (($data['status'] ?? 'draft') === 'confirmed') {
            $purchase = $this->confirm($purchase->id, $adminId);
        }

        return $purchase;
    }

    public function update(int $id, array $data, array $items, int $adminId): Model
    {
        $purchase = $this->purchases->findOrFail($id);

        if ($purchase->status === 'confirmed') {
            throw new RuntimeException(__('Confirmed purchases cannot be edited.'));
        }

        $totals = $this->calculateTotals($items, (float) ($data['discount_amount'] ?? 0), (float) ($data['tax_amount'] ?? 0));

        $purchase = $this->purchases->update($id, [
            ...$data,
            'total_amount' => $totals['total_amount'],
            'net_amount' => $totals['net_amount'],
        ]);

        $this->purchases->syncItems($id, $items);

        if (($data['status'] ?? 'draft') === 'confirmed') {
            $purchase = $this->confirm($id, $adminId);
        }

        return $purchase->refresh()->load('items');
    }

    /**
     * Confirm a draft purchase: apply stock additions and post the accounting entry.
     * Draft purchases have no effect on stock or accounting until confirmed.
     */
    public function confirm(int $id, int $adminId): Model
    {
        return DB::transaction(function () use ($id, $adminId) {
            $purchase = $this->purchases->findOrFail($id)->load('items');

            if ($purchase->status === 'confirmed') {
                return $purchase;
            }

            $purchase = $this->purchases->update($id, ['status' => 'confirmed']);

            foreach ($purchase->items as $item) {
                $this->stocks->add(
                    productId: $item->product_id,
                    warehouseId: $purchase->warehouse_id,
                    qty: $item->qty,
                    variantId: $item->variant_id,
                    referenceType: Purchase::class,
                    referenceId: $purchase->id,
                    adminId: $adminId,
                    note: __('Purchase :invoice', ['invoice' => $purchase->invoice_number]),
                    type: StockMovementType::Purchase->value,
                );
            }

            $this->accounting->createEntry([
                'type' => 'debit',
                'category' => 'purchases',
                'amount' => (float) $purchase->total_amount,
                'description' => __('Purchase cost — :invoice', ['invoice' => $purchase->invoice_number]),
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'date' => now()->toDateString(),
                'admin_id' => $adminId,
            ]);

            if ((float) $purchase->discount_amount > 0) {
                $this->accounting->createEntry([
                    'type' => 'credit',
                    'category' => 'discounts',
                    'amount' => (float) $purchase->discount_amount,
                    'description' => __('Supplier discount — :invoice', ['invoice' => $purchase->invoice_number]),
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'date' => now()->toDateString(),
                    'admin_id' => $adminId,
                ]);
            }

            if ((float) $purchase->tax_amount > 0) {
                $this->accounting->createEntry([
                    'type' => 'debit',
                    'category' => 'taxes',
                    'amount' => (float) $purchase->tax_amount,
                    'description' => __('Tax on purchase — :invoice', ['invoice' => $purchase->invoice_number]),
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'date' => now()->toDateString(),
                    'admin_id' => $adminId,
                ]);
            }

            return $purchase->refresh()->load('items');
        });
    }

    public function delete(int $id): bool
    {
        $purchase = $this->purchases->findOrFail($id);

        if ($purchase->status === 'confirmed') {
            throw new RuntimeException(__('Confirmed purchases cannot be deleted.'));
        }

        return $this->purchases->delete($id);
    }

    /**
     * @return array{total_amount: float, net_amount: float}
     */
    private function calculateTotals(array $items, float $discount, float $tax): array
    {
        $total = collect($items)->sum(fn (array $item) => $item['qty'] * $item['unit_cost']);
        $net = max(0, $total - $discount + $tax);

        return [
            'total_amount' => round($total, 2),
            'net_amount' => round($net, 2),
        ];
    }
}
