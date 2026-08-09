<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OpeningBalanceRequest;
use App\Models\Affiliate;
use App\Models\OpeningBalance;
use App\Services\OpeningBalanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OpeningBalanceController extends Controller
{
    public function __construct(private readonly OpeningBalanceService $openingBalances)
    {
    }

    public function index(): View
    {
        $cash = OpeningBalance::query()->where('entity_type', 'cash')->where('entity_id', 0)->first();
        $bank = OpeningBalance::query()->where('entity_type', 'bank')->where('entity_id', 0)->first();

        $affiliates = Affiliate::query()->with('customer')->get();
        $affiliateBalances = OpeningBalance::query()->where('entity_type', 'affiliate')->get()->keyBy('entity_id');

        return view('admin.opening-balances.index', [
            'cash' => $cash,
            'bank' => $bank,
            'affiliates' => $affiliates,
            'affiliateBalances' => $affiliateBalances,
        ]);
    }

    public function store(OpeningBalanceRequest $request): RedirectResponse
    {
        $this->openingBalances->set($request->validated(), auth('admin')->id());

        return redirect()->route('admin.opening-balances.index')->with('success', __('Opening balance saved successfully.'));
    }
}
