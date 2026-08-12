<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BlogPostRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySlug(string $slug): ?Model;
}
