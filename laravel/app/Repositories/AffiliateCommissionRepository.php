<?php

namespace App\Repositories;

use App\Models\AffiliateCommission;
use App\Repositories\Contracts\AffiliateCommissionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AffiliateCommissionRepository extends BaseRepository implements AffiliateCommissionRepositoryInterface
{
    public function __construct(AffiliateCommission $model)
    {
        parent::__construct($model);
    }

    public function forAffiliate(int $affiliateId, array $filters = []): Collection
    {
        return $this->applyFilters(
            $this->model->newQuery()->where('affiliate_id', $affiliateId),
            $filters
        )->get();
    }

    public function availableForAffiliate(int $affiliateId): Collection
    {
        return $this->model->newQuery()
            ->where('affiliate_id', $affiliateId)
            ->available()
            ->get();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }
}
