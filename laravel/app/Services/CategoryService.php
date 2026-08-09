<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryService
{
    public function __construct(private readonly CategoryRepositoryInterface $categories)
    {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->categories->paginate($filters, $perPage);
    }

    public function tree(): Collection
    {
        return $this->categories->tree();
    }

    public function all(array $filters = []): Collection
    {
        return $this->categories->all($filters);
    }

    public function find(int $id): ?Model
    {
        return $this->categories->find($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->categories->findBySlug($slug);
    }

    public function create(array $data): Model
    {
        return $this->categories->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->categories->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->categories->delete($id);
    }
}
