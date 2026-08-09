<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

/**
 * Aggregate reporting queries. Reads directly via Eloquent query builder
 * since these are cross-cutting aggregates rather than single-model CRUD,
 * which does not fit the single-model repository contract cleanly.
 */
class ReportService
{
    public function salesTotals(?string $from = null, ?string $to = null): array
    {
        $query = Order::query()->where('payment_status', 'paid');

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return [
            'orders_count' => (clone $query)->count(),
            'total_sales' => (float) (clone $query)->sum('total_amount'),
            'total_discount' => (float) (clone $query)->sum('discount_amount'),
            'total_shipping' => (float) (clone $query)->sum('shipping_amount'),
        ];
    }

    public function topSellingProducts(int $limit = 10, ?string $from = null, ?string $to = null): \Illuminate\Support\Collection
    {
        $query = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->select('order_items.product_id', 'order_items.product_name')
            ->selectRaw('SUM(order_items.qty) as total_qty')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_qty');

        if ($from) {
            $query->whereDate('orders.created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('orders.created_at', '<=', $to);
        }

        return $query->limit($limit)->get();
    }

    public function lowStockCount(): int
    {
        return Stock::query()->lowStock()->count();
    }

    public function ordersByStatus(): \Illuminate\Support\Collection
    {
        return Order::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }
}
