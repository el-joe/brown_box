<?php

namespace App\Services;

use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BlogPostService
{
    public function __construct(private readonly BlogPostRepositoryInterface $blogPosts)
    {
    }

    public function all(array $filters = []): Collection
    {
        return $this->blogPosts->all($filters);
    }

    public function find(int $id): ?Model
    {
        return $this->blogPosts->find($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->blogPosts->findBySlug($slug);
    }

    public function create(array $data): Model
    {
        return $this->blogPosts->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->blogPosts->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->blogPosts->delete($id);
    }
}
