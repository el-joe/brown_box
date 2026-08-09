<?php

namespace App\Repositories\Contracts;

interface TransactionRepositoryInterface extends BaseRepositoryInterface
{
    public function forModel(string $modelType, int $modelId): \Illuminate\Database\Eloquent\Collection;
}
