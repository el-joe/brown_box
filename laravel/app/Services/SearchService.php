<?php

namespace App\Services;

use App\Repositories\Contracts\SearchSuggestionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SearchService
{
    public function __construct(private readonly SearchSuggestionRepositoryInterface $suggestions)
    {
    }

    public function log(string $keyword): void
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return;
        }

        $this->suggestions->incrementHits($keyword);
    }

    public function autocomplete(string $query): Collection
    {
        $query = trim($query);

        if ($query === '') {
            return new Collection();
        }

        return $this->suggestions->matching($query, 8);
    }
}
