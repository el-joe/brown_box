<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Model;

    public function findBySku(string $sku): ?Model;

    public function search(string $keyword, int $limit = 20): Collection;

    public function featured(int $limit = 12): Collection;
}
