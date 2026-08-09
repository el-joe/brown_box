<?php

namespace App\Services;

use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BrandService
{
    public function __construct(private readonly BrandRepositoryInterface $brands)
    {
    }

    public function all(array $filters = []): Collection
    {
        return $this->brands->all($filters);
    }

    public function find(int $id): ?Model
    {
        return $this->brands->find($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->brands->findBySlug($slug);
    }

    public function create(array $data): Model
    {
        return $this->brands->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->brands->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->brands->delete($id);
    }
}
