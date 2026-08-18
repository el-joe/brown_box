<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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

    public function orderDetail(Request $request): View
    {
        $orderId = (int) $request->route('order');

        $order = Order::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->with('items', 'statusHistories', 'refundRequests', 'shippingCompany')
            ->findOrFail($orderId);

        return view('website.account.order-detail', [
            'order' => $order,
        ]);
    }

    public function requestRefund(Request $request): RedirectResponse
    {
        $orderId = (int) $request->route('order');

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
        ]);

        $order = Order::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($orderId);

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

    public function storeAddress(Request $request): JsonResponse
    {
        try {
            $data = $this->validateAddress($request);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        $data['customer_id'] = Auth::guard('customer')->id();

        $address = CustomerAddress::query()->create($data);
        $address->load('governorate', 'city');

        return response()->json([
            'success' => true,
            'message' => __('website.address_added'),
            'address' => $this->formatAddress($address),
        ]);
    }

    public function updateAddress(Request $request): JsonResponse
    {
        $addressId = (int) $request->route('address');

        $address = CustomerAddress::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($addressId);

        try {
            $data = $this->validateAddress($request);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        $address->update($data);
        $address->load('governorate', 'city');

        return response()->json([
            'success' => true,
            'message' => __('website.address_updated'),
            'address' => $this->formatAddress($address),
        ]);
    }

    public function destroyAddress(Request $request): RedirectResponse
    {
        $addressId = (int) $request->route('address');

        CustomerAddress::query()
            ->where('customer_id', Auth::guard('customer')->id())
            ->findOrFail($addressId)
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

    private function formatAddress(CustomerAddress $address): array
    {
        return [
            'id' => $address->id,
            'label' => $address->label,
            'name' => $address->name,
            'phone' => $address->phone,
            'governorate_id' => $address->governorate_id,
            'city_id' => $address->city_id,
            'governorate_name' => $address->governorate?->name_en,
            'governorate_name_ar' => $address->governorate?->name_ar,
            'city_name' => $address->city?->name_en,
            'city_name_ar' => $address->city?->name_ar,
            'address_line' => $address->address_line,
            'is_default' => (bool) $address->is_default,
            'formatted' => trim(sprintf(
                '%s, %s, %s',
                $address->address_line,
                current_lang() === 'ar' ? $address->city?->name_ar : $address->city?->name_en,
                current_lang() === 'ar' ? $address->governorate?->name_ar : $address->governorate?->name_en,
            ), ', '),
        ];
    }
}
