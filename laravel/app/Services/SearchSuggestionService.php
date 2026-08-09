<?php

namespace App\Services;

use App\Models\SearchSuggestion;
use App\Repositories\Contracts\SearchSuggestionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchSuggestionService
{
    public function __construct(private readonly SearchSuggestionRepositoryInterface $suggestions)
    {
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->suggestions->paginate($filters, $perPage);
    }

    public function find(int $id): ?SearchSuggestion
    {
        return $this->suggestions->find($id);
    }

    public function create(array $data): SearchSuggestion
    {
        return $this->suggestions->create($data);
    }

    public function update(int $id, array $data): SearchSuggestion
    {
        return $this->suggestions->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->suggestions->delete($id);
    }

    public function bulkSetActive(array $ids, bool $active): int
    {
        return SearchSuggestion::query()->whereIn('id', $ids)->update(['is_active' => $active]);
    }

    public function importFromCsv(string $path): int
    {
        $handle = fopen($path, 'r');
        $imported = 0;

        if (! $handle) {
            return 0;
        }

        $header = fgetcsv($handle);
        $keywordIndex = 0;

        if ($header && ($idx = array_search('keyword', array_map('strtolower', $header))) !== false) {
            $keywordIndex = $idx;
        } else {
            rewind($handle);
        }

        while (($row = fgetcsv($handle)) !== false) {
            $keyword = trim((string) ($row[$keywordIndex] ?? ''));

            if ($keyword === '') {
                continue;
            }

            SearchSuggestion::query()->firstOrCreate(
                ['keyword' => $keyword],
                ['hits' => 0, 'is_active' => true, 'sort_order' => 0]
            );

            $imported++;
        }

        fclose($handle);

        return $imported;
    }
}
