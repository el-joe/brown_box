<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function findByOrderNumber(string $orderNumber): ?Model;

    public function forCustomer(int $customerId, array $filters = []): Collection;

    public function addStatusHistory(int $orderId, string $status, ?string $notes = null, ?int $adminId = null): Model;
}
