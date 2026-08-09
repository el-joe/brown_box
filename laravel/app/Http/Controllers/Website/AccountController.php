<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        return view('website.account.index', [
            'customer' => Auth::guard('customer')->user(),
        ]);
    }

    public function orders(): View
    {
        $orders = Order::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->latest()
            ->paginate(10);

        return view('website.account.orders', [
            'orders' => $orders,
        ]);
    }

    public function orderDetail(int $order): View
    {
        $order = Order::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->with('items', 'statusHistories', 'refundRequests', 'shippingCompany')
            ->findOrFail($order);

        return view('website.account.order-detail', [
            'order' => $order,
        ]);
    }

    public function requestRefund(Request $request, int $order): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
        ]);

        $order = Order::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($order);

        RefundRequest::query()->create([
            'order_id' => $order->id,
            'customer_id' => Auth::guard('customer')->id(),
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', __('website.refund_requested'));
    }

    public function wishlist(): View
    {
        return app(WishlistController::class)->index();
    }

    public function profile(): View
    {
        return view('website.account.profile', [
            'customer' => Auth::guard('customer')->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $customer->fill([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
        ]);

        if (! empty($data['password'])) {
            $customer->password = Hash::make($data['password']);
        }

        $customer->save();

        return back()->with('success', __('website.profile_updated'));
    }

    public function addresses(): View
    {
        return view('website.account.addresses', [
            'addresses' => Auth::guard('customer')->user()->addresses()->with('governorate', 'city')->latest()->get(),
            'governorates' => Governorate::query()->with('cities')->orderBy('name_en')->get(),
        ]);
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $data = $this->validateAddress($request);
        $data['customer_id'] = Auth::guard('customer')->id();

        CustomerAddress::query()->create($data);

        return back()->with('success', __('website.address_added'));
    }

    public function updateAddress(Request $request, int $address): RedirectResponse
    {
        $address = CustomerAddress::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($address);

        $address->update($this->validateAddress($request));

        return back()->with('success', __('website.address_updated'));
    }

    public function destroyAddress(int $address): RedirectResponse
    {
        CustomerAddress::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($address)
            ->delete();

        return back()->with('success', __('website.address_deleted'));
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'governorate_id' => ['required', 'integer', 'exists:governorates,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'address_line' => ['required', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}
