<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SearchSuggestionRepositoryInterface extends BaseRepositoryInterface
{
    public function popular(int $limit = 10): Collection;

    public function incrementHits(string $keyword): void;

    public function matching(string $query, int $limit = 8): Collection;

    public function findByKeyword(string $keyword): ?\App\Models\SearchSuggestion;
}
