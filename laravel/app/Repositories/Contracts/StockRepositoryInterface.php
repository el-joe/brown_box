<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface StockRepositoryInterface extends BaseRepositoryInterface
{
    public function findByProductAndWarehouse(int $productId, int $warehouseId, ?int $variantId = null): ?Model;

    public function firstOrCreateFor(int $productId, int $warehouseId, ?int $variantId = null): Model;

    public function incrementQty(int $stockId, int $qty): Model;

    public function decrementQty(int $stockId, int $qty): Model;

    public function recordMovement(array $data): Model;

    public function lowStock(): \Illuminate\Database\Eloquent\Collection;
}
