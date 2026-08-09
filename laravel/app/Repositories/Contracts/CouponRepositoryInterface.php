<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CouponRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $code): ?Model;

    public function incrementUsage(int $couponId): Model;
}
