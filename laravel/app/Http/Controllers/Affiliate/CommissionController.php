<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Affiliate\Concerns\InteractsWithAffiliate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    use InteractsWithAffiliate;

    public function index(Request $request): View
    {
        $affiliate = $this->affiliate();

        $commissions = $affiliate->commissions()
            ->with('order')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $base = $affiliate->commissions();

        $summary = [
            'total_earned' => (clone $base)->whereIn('status', ['approved', 'paid'])->sum('amount'),
            'pending' => (clone $base)->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere(function ($query) {
                        $query->where('status', 'approved')->where('available_at', '>', now());
                    });
            })->sum('amount'),
            'available' => (clone $base)->where('status', 'approved')->where('available_at', '<=', now())->sum('amount'),
            'paid' => (clone $base)->where('status', 'paid')->sum('amount'),
        ];

        return view('affiliate.commissions.index', [
            'commissions' => $commissions,
            'summary' => $summary,
        ]);
    }
}
