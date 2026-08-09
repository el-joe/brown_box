<?php

namespace App\Repositories;

use App\Models\AccountingEntry;
use App\Repositories\Contracts\AccountingEntryRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class AccountingEntryRepository extends BaseRepository implements AccountingEntryRepositoryInterface
{
    public function __construct(AccountingEntry $model)
    {
        parent::__construct($model);
    }

    public function totalByTypeBetween(string $type, ?string $from = null, ?string $to = null): float
    {
        $query = $this->model->newQuery()->where('type', $type);

        if ($from) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('date', '<=', $to);
        }

        return (float) $query->sum('amount');
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['from'])) {
            $query->whereDate('date', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('date', '<=', $filters['to']);
        }

        return $query;
    }
}
