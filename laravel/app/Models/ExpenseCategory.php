<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ExpenseCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
        'parent_id',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public array $translatable = ['name'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class, 'parent_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullPathAttribute(): string
    {
        $names = collect([$this->getTranslation('name', app()->getLocale())]);
        $parent = $this->parent;

        while ($parent) {
            $names->prepend($parent->getTranslation('name', app()->getLocale()));
            $parent = $parent->parent;
        }

        return $names->implode(' / ');
    }
}
