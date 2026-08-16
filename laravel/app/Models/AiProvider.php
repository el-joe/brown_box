<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
        'is_active',
        'config',
        'available_models',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
        'available_models' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
