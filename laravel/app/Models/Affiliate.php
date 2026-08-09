<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'code',
        'commission_type',
        'fixed_commission_rate',
        'balance',
        'total_earned',
        'is_active',
        'approved_at',
    ];

    protected $casts = [
        'fixed_commission_rate' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    public function categoryCommissions(): HasMany
    {
        return $this->hasMany(AffiliateCategoryCommission::class);
    }

    public function commissionTiers(): HasMany
    {
        return $this->hasMany(AffiliateCommissionTier::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvailableBalanceAttribute(): float
    {
        return (float) $this->balance;
    }
}
