<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    public function forModel(string $modelType, int $modelId): Collection
    {
        return $this->model->newQuery()
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->latest()
            ->get();
    }
}
