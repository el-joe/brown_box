<?php

namespace App\Repositories\Contracts;

use App\Models\SeoPage;

interface SeoPageRepositoryInterface extends BaseRepositoryInterface
{
    public function findByPageKey(string $pageKey): ?SeoPage;

    public function findForModel(string $modelType, int $modelId): ?SeoPage;
}
