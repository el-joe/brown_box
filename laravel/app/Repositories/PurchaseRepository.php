<?php

namespace App\Repositories;

use App\Models\Purchase;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseRepository extends BaseRepository implements PurchaseRepositoryInterface
{
    public function __construct(Purchase $model)
    {
        parent::__construct($model);
    }

    public function createWithItems(array $purchaseData, array $items): Model
    {
        return DB::transaction(function () use ($purchaseData, $items) {
            $purchase = $this->create($purchaseData);

            $this->insertItems($purchase, $items);

            return $purchase->refresh()->load('items');
        });
    }

    public function syncItems(int $purchaseId, array $items): Model
    {
        return DB::transaction(function () use ($purchaseId, $items) {
            $purchase = $this->findOrFail($purchaseId);
            $purchase->items()->delete();

            $this->insertItems($purchase, $items);

            return $purchase->refresh()->load('items');
        });
    }

    public function nextInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = $this->model->newQuery()->whereDate('created_at', now()->toDateString())->count() + 1;

        return 'PO-'.$date.'-'.Str::padLeft((string) $count, 4, '0');
    }

    private function insertItems(Purchase $purchase, array $items): void
    {
        foreach ($items as $item) {
            $purchase->items()->create([
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'qty' => $item['qty'],
                'unit_cost' => $item['unit_cost'],
                'total' => $item['qty'] * $item['unit_cost'],
            ]);
        }
    }
}
