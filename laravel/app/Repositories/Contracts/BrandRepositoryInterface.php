<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BrandRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Model;
}
