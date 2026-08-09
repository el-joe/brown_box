<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    public function __construct(private readonly WarehouseRepositoryInterface $warehouses)
    {
    }

    public function all(array $filters = []): Collection
    {
        return $this->warehouses->all($filters);
    }

    public function find(int $id): ?Model
    {
        return $this->warehouses->find($id);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $warehouse = $this->warehouses->create($data);

            if ($warehouse->is_default) {
                $this->makeDefault($warehouse->id);
            }

            return $warehouse->refresh();
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $warehouse = $this->warehouses->update($id, $data);

            if ($warehouse->is_default) {
                $this->makeDefault($warehouse->id);
            }

            return $warehouse->refresh();
        });
    }

    public function delete(int $id): bool
    {
        return $this->warehouses->delete($id);
    }

    /**
     * Ensure only the given warehouse is flagged as default.
     */
    private function makeDefault(int $id): void
    {
        Warehouse::query()->where('id', '!=', $id)->update(['is_default' => false]);
    }
}
