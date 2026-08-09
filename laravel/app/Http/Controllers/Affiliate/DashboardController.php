<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Affiliate\Concerns\InteractsWithAffiliate;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use InteractsWithAffiliate;

    public function index(): View
    {
        $affiliate = $this->affiliate();

        $recentCommissions = $affiliate->commissions()
            ->with('order')
            ->latest()
            ->take(10)
            ->get();

        $recentOrders = $affiliate->orders()
            ->latest()
            ->take(10)
            ->get();

        $referralLink = url('/').'?ref='.$affiliate->code;

        return view('affiliate.dashboard', [
            'affiliate' => $affiliate,
            'recentCommissions' => $recentCommissions,
            'recentOrders' => $recentOrders,
            'referralLink' => $referralLink,
        ]);
    }
}
