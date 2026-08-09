<?php

namespace App\Services;

use App\Models\AccountingEntry;
use App\Models\Affiliate;
use App\Models\OpeningBalance;
use Illuminate\Support\Facades\DB;

class OpeningBalanceService
{
    public function set(array $data, int $adminId): OpeningBalance
    {
        return DB::transaction(function () use ($data, $adminId) {
            $entityType = $data['entity_type'];
            $entityId = $entityType === 'affiliate' ? (int) $data['affiliate_id'] : 0;

            $openingBalance = OpeningBalance::query()->updateOrCreate(
                ['entity_type' => $entityType, 'entity_id' => $entityId],
                [
                    'amount' => $data['amount'],
                    'date' => $data['date'],
                    'notes' => $data['notes'] ?? null,
                ]
            );

            if ($entityType === 'affiliate') {
                Affiliate::query()->whereKey($entityId)->update(['balance' => $data['amount']]);
            }

            AccountingEntry::query()->updateOrCreate(
                [
                    'reference_type' => OpeningBalance::class,
                    'reference_id' => $openingBalance->id,
                ],
                [
                    'type' => 'credit',
                    'category' => 'owner',
                    'amount' => $data['amount'],
                    'description' => __('Opening balance').' - '.ucfirst($entityType),
                    'date' => $data['date'],
                    'admin_id' => $adminId,
                ]
            );

            return $openingBalance;
        });
    }
}
