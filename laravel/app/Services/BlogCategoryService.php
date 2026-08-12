<?php

namespace App\Services;

use App\Repositories\Contracts\BlogCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BlogCategoryService
{
    public function __construct(private readonly BlogCategoryRepositoryInterface $blogCategories)
    {
    }

    public function all(array $filters = []): Collection
    {
        return $this->blogCategories->all($filters);
    }

    public function find(int $id): ?Model
    {
        return $this->blogCategories->find($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->blogCategories->findBySlug($slug);
    }

    public function create(array $data): Model
    {
        return $this->blogCategories->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->blogCategories->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->blogCategories->delete($id);
    }
}
