<?php

namespace App\Repositories\Contracts;

use App\Models\StaticPage;

interface StaticPageRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?StaticPage;
}
