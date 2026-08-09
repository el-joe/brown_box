<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $method = 'filter'.str_replace('_', '', ucwords($key, '_'));

            if (method_exists($this, $method)) {
                $this->{$method}($query, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query;
    }
}
