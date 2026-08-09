<?php

namespace App\Repositories;

use App\Models\StaticPage;
use App\Repositories\Contracts\StaticPageRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class StaticPageRepository extends BaseRepository implements StaticPageRepositoryInterface
{
    public function __construct(StaticPage $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?StaticPage
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $search = mb_strtolower($filters['search']);
            $query->where(function (Builder $q) use ($search) {
                $q->whereRaw('LOWER(slug) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(title) LIKE ?', ["%{$search}%"]);
            });
            unset($filters['search']);
        }

        return parent::applyFilters($query, $filters);
    }
}
