<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Affiliate\Concerns\InteractsWithAffiliate;
use App\Http\Controllers\Controller;
use App\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class PayoutController extends Controller
{
    use InteractsWithAffiliate;

    public function __construct(private readonly AffiliateService $affiliates)
    {
    }

    public function index(): View
    {
        $affiliate = $this->affiliate();

        $payouts = $affiliate->payoutRequests()
            ->latest()
            ->paginate(20);

        return view('affiliate.payouts.index', [
            'affiliate' => $affiliate,
            'payouts' => $payouts,
            'minPayoutAmount' => (float) setting('affiliate.min_payout_amount', 0),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $affiliate = $this->affiliate();

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.(float) $affiliate->balance],
            'payment_method' => ['required', 'in:bank,vodafone,instapay'],
            'payment_details' => ['required', 'array'],
        ]);

        try {
            $this->affiliates->requestPayout(
                $affiliate->id,
                (float) $data['amount'],
                $data['payment_method'],
                $data['payment_details'],
            );
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('affiliate.payouts.index')
            ->with('success', __('Payout request submitted successfully.'));
    }
}
