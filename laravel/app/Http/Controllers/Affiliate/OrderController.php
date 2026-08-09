<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Affiliate\Concerns\InteractsWithAffiliate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    use InteractsWithAffiliate;

    public function index(Request $request): View
    {
        $affiliate = $this->affiliate();

        $orders = $affiliate->orders()
            ->with('affiliateCommissions')
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->string('status');

                $query->whereHas('affiliateCommissions', function ($query) use ($status) {
                    if ($status === 'available') {
                        $query->where('status', 'approved')->where('available_at', '<=', now());
                    } else {
                        $query->where('status', $status);
                    }
                });
            })
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('date_to')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('affiliate.orders.index', [
            'orders' => $orders,
        ]);
    }
}
