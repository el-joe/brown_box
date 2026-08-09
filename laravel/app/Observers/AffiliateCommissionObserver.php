<?php

namespace App\Observers;

use App\Models\AffiliateCommission;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AffiliateCommissionObserver
{
    public function updated(AffiliateCommission $commission): void
    {
        if (! $commission->isDirty('status') || $commission->status !== 'approved') {
            return;
        }

        if ($commission->available_at && $commission->available_at->isFuture()) {
            return;
        }

        DB::transaction(function () use ($commission) {
            $affiliate = $commission->affiliate()->lockForUpdate()->first();

            if (! $affiliate) {
                return;
            }

            $balanceBefore = (float) $affiliate->balance;
            $balanceAfter = $balanceBefore + (float) $commission->amount;

            $affiliate->update([
                'balance' => $balanceAfter,
                'total_earned' => $affiliate->total_earned + $commission->amount,
            ]);

            Transaction::create([
                'type' => 'affiliate_commission',
                'model_type' => $affiliate::class,
                'model_id' => $affiliate->id,
                'amount' => $commission->amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => 'Commission approved for order #'.$commission->order_id,
                'reference' => 'affiliate_commission:'.$commission->id,
            ]);
        });
    }
}
