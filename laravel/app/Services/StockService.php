<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Repositories\Contracts\StockRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function __construct(private readonly StockRepositoryInterface $stocks)
    {
    }

    public function lowStock(): Collection
    {
        return $this->stocks->lowStock();
    }

    /**
     * Deduct stock for every item on an order against the default warehouse.
     * Order creation already deducts stock per item against the selected
     * warehouse, so this is a no-op if a movement for the order already exists.
     */
    public function deductForOrder(Order $order): void
    {
        $alreadyDeducted = StockMovement::query()
            ->where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->exists();

        if ($alreadyDeducted) {
            return;
        }

        $warehouse = Warehouse::query()->where('is_default', true)->first();

        if (! $warehouse) {
            return;
        }

        foreach ($order->items as $item) {
            $this->deduct(
                productId: $item->product_id,
                warehouseId: $warehouse->id,
                qty: $item->qty,
                variantId: $item->variant_id,
                referenceType: 'order',
                referenceId: $order->id,
                note: "Order #{$order->order_number}",
            );
        }
    }

    /**
     * Add qty to a warehouse's stock for a product/variant and record the movement.
     */
    public function add(int $productId, int $warehouseId, int $qty, ?int $variantId = null, ?string $referenceType = null, ?int $referenceId = null, ?int $adminId = null, ?string $note = null, string $type = 'in'): Model
    {
        return DB::transaction(function () use ($productId, $warehouseId, $qty, $variantId, $referenceType, $referenceId, $adminId, $note, $type) {
            $stock = $this->stocks->firstOrCreateFor($productId, $warehouseId, $variantId);
            $before = $stock->qty;
            $stock = $this->stocks->incrementQty($stock->id, $qty);

            $this->stocks->recordMovement([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'warehouse_id' => $warehouseId,
                'type' => $type,
                'qty' => $qty,
                'before_qty' => $before,
                'after_qty' => $stock->qty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
                'admin_id' => $adminId,
            ]);

            return $stock;
        });
    }

    /**
     * Deduct qty from a warehouse's stock for a product/variant and record the movement.
     */
    public function deduct(int $productId, int $warehouseId, int $qty, ?int $variantId = null, ?string $referenceType = null, ?int $referenceId = null, ?int $adminId = null, ?string $note = null): Model
    {
        return DB::transaction(function () use ($productId, $warehouseId, $qty, $variantId, $referenceType, $referenceId, $adminId, $note) {
            $stock = $this->stocks->firstOrCreateFor($productId, $warehouseId, $variantId);

            if ($stock->qty < $qty) {
                throw new RuntimeException(__('Insufficient stock for product :productId in warehouse :warehouseId.', ['productId' => $productId, 'warehouseId' => $warehouseId]));
            }

            $before = $stock->qty;
            $stock = $this->stocks->decrementQty($stock->id, $qty);

            $this->stocks->recordMovement([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'warehouse_id' => $warehouseId,
                'type' => 'out',
                'qty' => $qty,
                'before_qty' => $before,
                'after_qty' => $stock->qty,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
                'admin_id' => $adminId,
            ]);

            return $stock;
        });
    }

    /**
     * Manually adjust (add or deduct) a warehouse's stock for a product/variant,
     * recording the movement as type 'adjustment'.
     */
    public function adjust(int $productId, int $warehouseId, int $qty, string $direction, ?int $variantId = null, ?int $adminId = null, ?string $note = null): Model
    {
        return DB::transaction(function () use ($productId, $warehouseId, $qty, $direction, $variantId, $adminId, $note) {
            $stock = $this->stocks->firstOrCreateFor($productId, $warehouseId, $variantId);

            if ($direction === 'deduct' && $stock->qty < $qty) {
                throw new RuntimeException(__('Insufficient stock for product :productId in warehouse :warehouseId.', ['productId' => $productId, 'warehouseId' => $warehouseId]));
            }

            $before = $stock->qty;
            $stock = $direction === 'deduct'
                ? $this->stocks->decrementQty($stock->id, $qty)
                : $this->stocks->incrementQty($stock->id, $qty);

            $this->stocks->recordMovement([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'warehouse_id' => $warehouseId,
                'type' => StockMovementType::Adjustment->value,
                'qty' => $direction === 'deduct' ? -$qty : $qty,
                'before_qty' => $before,
                'after_qty' => $stock->qty,
                'reference_type' => null,
                'reference_id' => null,
                'note' => $note,
                'admin_id' => $adminId,
            ]);

            return $stock;
        });
    }

    /**
     * Transfer qty of a product/variant from one warehouse to another.
     */
    public function transfer(int $productId, int $fromWarehouseId, int $toWarehouseId, int $qty, ?int $variantId = null, ?int $adminId = null, ?string $note = null): void
    {
        DB::transaction(function () use ($productId, $fromWarehouseId, $toWarehouseId, $qty, $variantId, $adminId, $note) {
            $this->deduct($productId, $fromWarehouseId, $qty, $variantId, 'transfer', null, $adminId, $note ?? "Transfer to warehouse {$toWarehouseId}");
            $this->add($productId, $toWarehouseId, $qty, $variantId, 'transfer', null, $adminId, $note ?? "Transfer from warehouse {$fromWarehouseId}");
        });
    }
}
