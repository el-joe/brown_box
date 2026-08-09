<?php

namespace App\Repositories;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Repositories\Contracts\StockRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class StockRepository extends BaseRepository implements StockRepositoryInterface
{
    public function __construct(Stock $model)
    {
        parent::__construct($model);
    }

    public function findByProductAndWarehouse(int $productId, int $warehouseId, ?int $variantId = null): ?Model
    {
        return $this->model->newQuery()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('variant_id', $variantId)
            ->first();
    }

    public function firstOrCreateFor(int $productId, int $warehouseId, ?int $variantId = null): Model
    {
        return $this->model->newQuery()->firstOrCreate([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'variant_id' => $variantId,
        ], [
            'qty' => 0,
            'min_qty_alert' => 0,
        ]);
    }

    public function incrementQty(int $stockId, int $qty): Model
    {
        $stock = $this->findOrFail($stockId);
        $stock->increment('qty', $qty);

        return $stock->refresh();
    }

    public function decrementQty(int $stockId, int $qty): Model
    {
        $stock = $this->findOrFail($stockId);
        $stock->decrement('qty', $qty);

        return $stock->refresh();
    }

    public function recordMovement(array $data): Model
    {
        return StockMovement::query()->create($data);
    }

    public function lowStock(): Collection
    {
        return $this->model->newQuery()->lowStock()->get();
    }
}
