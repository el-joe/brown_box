<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function findByOrderNumber(string $orderNumber): ?Model
    {
        return $this->model->newQuery()->where('order_number', $orderNumber)->first();
    }

    public function forCustomer(int $customerId, array $filters = []): Collection
    {
        return $this->applyFilters(
            $this->model->newQuery()->where('customer_id', $customerId),
            $filters
        )->get();
    }

    public function addStatusHistory(int $orderId, string $status, ?string $notes = null, ?int $adminId = null): Model
    {
        return OrderStatusHistory::query()->create([
            'order_id' => $orderId,
            'status' => $status,
            'notes' => $notes,
            'admin_id' => $adminId,
            'created_at' => now(),
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        return $query;
    }
}
