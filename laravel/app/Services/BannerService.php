<?php

namespace App\Services;

use App\Repositories\Contracts\BannerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BannerService
{
    public function __construct(private readonly BannerRepositoryInterface $banners)
    {
    }

    public function all(array $filters = []): Collection
    {
        return $this->banners->all($filters);
    }

    public function find(int $id): ?Model
    {
        return $this->banners->find($id);
    }

    public function create(array $data): Model
    {
        return $this->banners->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->banners->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->banners->delete($id);
    }
}
