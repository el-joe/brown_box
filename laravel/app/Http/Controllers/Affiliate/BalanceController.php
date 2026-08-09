<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Affiliate\Concerns\InteractsWithAffiliate;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Transaction;
use Illuminate\View\View;

class BalanceController extends Controller
{
    use InteractsWithAffiliate;

    public function index(): View
    {
        $affiliate = $this->affiliate();

        $transactions = Transaction::query()
            ->where('model_type', Affiliate::class)
            ->where('model_id', $affiliate->id)
            ->latest()
            ->paginate(20);

        return view('affiliate.balance', [
            'affiliate' => $affiliate,
            'transactions' => $transactions,
            'minPayoutAmount' => (float) setting('affiliate.min_payout_amount', 0),
        ]);
    }
}
