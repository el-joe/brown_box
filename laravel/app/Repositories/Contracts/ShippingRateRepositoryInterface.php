<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ShippingRateRepositoryInterface extends BaseRepositoryInterface
{
    public function findForLocation(int $shippingCompanyId, ?int $governorateId, ?int $cityId): ?Model;
}
