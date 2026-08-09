<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackOrderController extends Controller
{
    public function index(): View
    {
        return view('website.track-order.index', [
            'order' => null,
        ]);
    }

    public function track(Request $request): View
    {
        $data = $request->validate([
            'order_number' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ]);

        $order = Order::query()
            ->with('items', 'statusHistories')
            ->where('order_number', $data['order_number'])
            ->whereJsonContains('customer_address->phone', $data['phone'])
            ->first();

        return view('website.track-order.index', [
            'order' => $order,
            'notFound' => ! $order,
        ]);
    }
}
