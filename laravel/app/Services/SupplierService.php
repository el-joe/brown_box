<?php

namespace App\Services;

use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SupplierService
{
    public function __construct(private readonly SupplierRepositoryInterface $suppliers)
    {
    }

    public function all(array $filters = []): Collection
    {
        return $this->suppliers->all($filters);
    }

    public function find(int $id): ?Model
    {
        return $this->suppliers->find($id);
    }

    public function create(array $data): Model
    {
        return $this->suppliers->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->suppliers->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->suppliers->delete($id);
    }
}
