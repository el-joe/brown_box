<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface AffiliateCommissionRepositoryInterface extends BaseRepositoryInterface
{
    public function forAffiliate(int $affiliateId, array $filters = []): Collection;

    public function availableForAffiliate(int $affiliateId): Collection;
}
