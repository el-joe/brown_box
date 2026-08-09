<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AffiliateRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $code): ?Model;

    public function findByCustomerId(int $customerId): ?Model;

    public function incrementBalance(int $affiliateId, float $amount): Model;

    public function decrementBalance(int $affiliateId, float $amount): Model;
}
