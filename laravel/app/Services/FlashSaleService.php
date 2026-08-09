<?php

namespace App\Services;

use App\Repositories\Contracts\FlashSaleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FlashSaleService
{
    public function __construct(private readonly FlashSaleRepositoryInterface $flashSales)
    {
    }

    public function find(int $id): ?Model
    {
        return $this->flashSales->find($id);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $flashSale = $this->flashSales->create([
                'name' => $data['name'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->syncItems($flashSale, $data['items'] ?? []);

            return $flashSale->fresh('items');
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $flashSale = $this->flashSales->update($id, [
                'name' => $data['name'],
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->syncItems($flashSale, $data['items'] ?? []);

            return $flashSale->fresh('items');
        });
    }

    public function delete(int $id): bool
    {
        return $this->flashSales->delete($id);
    }

    private function syncItems(Model $flashSale, array $items): void
    {
        $flashSale->items()->delete();

        foreach ($items as $item) {
            if (empty($item['product_id'])) {
                continue;
            }

            $flashSale->items()->create([
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'discount_type' => $item['discount_type'],
                'discount_value' => $item['discount_value'],
                'max_qty' => $item['max_qty'] ?? null,
            ]);
        }
    }
}
