<?php

namespace App\Repositories;

use App\Models\SeoPage;
use App\Repositories\Contracts\SeoPageRepositoryInterface;

class SeoPageRepository extends BaseRepository implements SeoPageRepositoryInterface
{
    public function __construct(SeoPage $model)
    {
        parent::__construct($model);
    }

    public function findByPageKey(string $pageKey): ?SeoPage
    {
        return $this->model->newQuery()->where('page_key', $pageKey)->first();
    }

    public function findForModel(string $modelType, int $modelId): ?SeoPage
    {
        return $this->model->newQuery()
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->first();
    }
}
