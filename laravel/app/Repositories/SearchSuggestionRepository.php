<?php

namespace App\Repositories;

use App\Models\SearchSuggestion;
use App\Repositories\Contracts\SearchSuggestionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SearchSuggestionRepository extends BaseRepository implements SearchSuggestionRepositoryInterface
{
    public function __construct(SearchSuggestion $model)
    {
        parent::__construct($model);
    }

    public function popular(int $limit = 10): Collection
    {
        return $this->model->newQuery()
            ->active()
            ->orderByDesc('hits')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }

    public function matching(string $query, int $limit = 8): Collection
    {
        return $this->model->newQuery()
            ->active()
            ->whereRaw('LOWER(keyword) LIKE ?', ['%'.mb_strtolower($query).'%'])
            ->orderByDesc('hits')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }

    public function findByKeyword(string $keyword): ?SearchSuggestion
    {
        return $this->model->newQuery()->where('keyword', $keyword)->first();
    }

    public function incrementHits(string $keyword): void
    {
        $suggestion = $this->model->newQuery()->where('keyword', $keyword)->first();

        if ($suggestion) {
            $suggestion->increment('hits');

            return;
        }

        $this->model->newQuery()->create([
            'keyword' => $keyword,
            'hits' => 1,
            'is_active' => false,
            'sort_order' => 0,
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (! empty($filters['keyword'])) {
            $keyword = mb_strtolower($filters['keyword']);
            $query->whereRaw('LOWER(keyword) LIKE ?', ["%{$keyword}%"]);
            unset($filters['keyword']);
        }

        return parent::applyFilters($query, $filters);
    }
}
