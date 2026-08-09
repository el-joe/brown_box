<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateCommissionTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'affiliate_category_commission_id',
        'min_amount',
        'max_amount',
        'rate',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function categoryCommission(): BelongsTo
    {
        return $this->belongsTo(AffiliateCategoryCommission::class, 'affiliate_category_commission_id');
    }
}
