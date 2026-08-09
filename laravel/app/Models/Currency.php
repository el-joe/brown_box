<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'symbol',
        'name',
        'rate_to_egp',
        'is_default',
    ];

    protected $casts = [
        'rate_to_egp' => 'decimal:4',
        'is_default' => 'boolean',
    ];
}
