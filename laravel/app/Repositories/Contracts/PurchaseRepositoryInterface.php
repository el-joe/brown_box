<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface PurchaseRepositoryInterface extends BaseRepositoryInterface
{
    public function createWithItems(array $purchaseData, array $items): Model;

    public function syncItems(int $purchaseId, array $items): Model;

    public function nextInvoiceNumber(): string;
}
