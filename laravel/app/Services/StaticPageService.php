<?php

namespace App\Services;

use App\Repositories\Contracts\StaticPageRepositoryInterface;
use App\Models\StaticPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StaticPageService
{
    public function __construct(private readonly StaticPageRepositoryInterface $pages)
    {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->pages->paginate($filters, $perPage);
    }

    public function find(int $id): ?StaticPage
    {
        return $this->pages->find($id);
    }

    public function create(array $data): StaticPage
    {
        return $this->pages->create($data);
    }

    public function update(int $id, array $data): StaticPage
    {
        return $this->pages->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->pages->delete($id);
    }
}
