<?php

namespace App\Services;

use App\Events\PaymentVerified;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaymentService
{
    /**
     * Keys concatenated (in this exact order) to build the Paymob HMAC
     * string, per Paymob's transaction processed callback documentation.
     */
    private const HMAC_FIELDS = [
        'amount_cents',
        'created_at',
        'currency',
        'error_occured',
        'has_parent_transaction',
        'id',
        'integration_id',
        'is_3d_secure',
        'is_auth',
        'is_capture',
        'is_refunded',
        'is_standalone_payment',
        'is_voided',
        'order.id',
        'owner',
        'pending',
        'source_data.pan',
        'source_data.sub_type',
        'source_data.type',
        'success',
    ];

    public function __construct(private readonly OrderRepositoryInterface $orders)
    {
    }

    /**
     * Run the standard Paymob Accept API 3-step flow (auth token, order
     * registration, payment key) and return the iframe URL the customer
     * should be redirected to in order to pay.
     */
    public function paymobInitiatePayment(Order $order): string
    {
        $config = $this->paymobConfig();

        $authToken = $this->paymobAuthToken($config['api_key']);
        $paymobOrderId = $this->paymobRegisterOrder($authToken, $order);
        $paymentKey = $this->paymobPaymentKey($authToken, $order, $paymobOrderId, $config['integration_id']);

        $order->update(['payment_reference' => (string) $paymobOrderId]);

        return sprintf(
            'https://accept.paymob.com/api/acceptance/iframes/%s?payment_token=%s',
            $config['iframe_id'],
            $paymentKey,
        );
    }

    /**
     * Verify a Paymob callback's HMAC signature. $transaction must be the
     * flat "obj" transaction payload Paymob sends (query params on GET
     * redirects, or obj on POST webhooks).
     */
    public function verifyPaymobHmac(array $transaction, string $receivedHmac): bool
    {
        $secret = $this->paymobConfig()['hmac_secret'];

        if (empty($secret) || empty($receivedHmac)) {
            return false;
        }

        $concatenated = '';

        foreach (self::HMAC_FIELDS as $field) {
            $concatenated .= $this->extractHmacValue($transaction, $field);
        }

        $expected = hash_hmac('sha512', $concatenated, $secret);

        return hash_equals(strtolower($expected), strtolower($receivedHmac));
    }

    private function extractHmacValue(array $data, string $dottedKey): string
    {
        $value = data_get($data, $dottedKey);

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) ($value ?? '');
    }

    /**
     * Paymob credentials are managed by admins via the gateways table, with
     * config/env as a fallback for environments without a seeded row.
     */
    private function paymobConfig(): array
    {
        $gatewayConfig = gateway('paymob');

        $config = [
            'api_key' => $gatewayConfig['api_key'] ?? config('services.paymob.api_key'),
            'integration_id' => $gatewayConfig['integration_id'] ?? config('services.paymob.integration_id'),
            'iframe_id' => $gatewayConfig['iframe_id'] ?? config('services.paymob.iframe_id'),
            'hmac_secret' => $gatewayConfig['hmac_secret'] ?? config('services.paymob.hmac_secret'),
        ];

        if (empty($config['api_key']) || empty($config['integration_id']) || empty($config['iframe_id'])) {
            throw new RuntimeException('Paymob is not fully configured.');
        }

        return $config;
    }

    private function paymobAuthToken(string $apiKey): string
    {
        $response = Http::post('https://accept.paymob.com/api/auth/tokens', [
            'api_key' => $apiKey,
        ]);

        if ($response->failed() || empty($response->json('token'))) {
            Log::error('Paymob auth token request failed.', ['response' => $response->body()]);

            throw new RuntimeException('Unable to authenticate with Paymob.');
        }

        return $response->json('token');
    }

    private function paymobRegisterOrder(string $authToken, Order $order): int
    {
        $response = Http::post('https://accept.paymob.com/api/ecommerce/orders', [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            'amount_cents' => (int) round(((float) $order->total_amount) * 100),
            'currency' => 'EGP',
            'merchant_order_id' => $order->order_number,
            'items' => [],
        ]);

        if ($response->failed() || empty($response->json('id'))) {
            Log::error('Paymob order registration failed.', ['response' => $response->body()]);

            throw new RuntimeException('Unable to register order with Paymob.');
        }

        return (int) $response->json('id');
    }

    private function paymobPaymentKey(string $authToken, Order $order, int $paymobOrderId, string $integrationId): string
    {
        $address = $order->customer_address ?? [];
        $customer = $order->customer;

        $nameParts = preg_split('/\s+/', trim((string) ($address['name'] ?? $customer?->name ?? '')), 2);

        $response = Http::post('https://accept.paymob.com/api/acceptance/payment_keys', [
            'auth_token' => $authToken,
            'amount_cents' => (int) round(((float) $order->total_amount) * 100),
            'expiration' => 3600,
            'order_id' => $paymobOrderId,
            'billing_data' => [
                'first_name' => $nameParts[0] ?? 'NA',
                'last_name' => $nameParts[1] ?? 'NA',
                'email' => $customer?->email ?? 'NA@NA.com',
                'phone_number' => $address['phone'] ?? $customer?->phone ?? 'NA',
                'apartment' => 'NA',
                'floor' => 'NA',
                'street' => $address['address_line'] ?? 'NA',
                'building' => 'NA',
                'shipping_method' => 'NA',
                'postal_code' => 'NA',
                'city' => 'NA',
                'country' => 'EG',
                'state' => 'NA',
            ],
            'currency' => 'EGP',
            'integration_id' => (int) $integrationId,
        ]);

        if ($response->failed() || empty($response->json('token'))) {
            Log::error('Paymob payment key request failed.', ['response' => $response->body()]);

            throw new RuntimeException('Unable to obtain Paymob payment key.');
        }

        return $response->json('token');
    }

    /**
     * Handle a payment gateway callback: locate the order by its payment
     * reference and mark it paid or failed depending on the gateway status.
     */
    public function handleGatewayCallback(string $paymentReference, string $gatewayStatus, array $payload = []): Model
    {
        return DB::transaction(function () use ($paymentReference, $gatewayStatus, $payload) {
            $order = $this->orders->all(['payment_reference' => $paymentReference])->first();

            if (! $order) {
                throw new RuntimeException("No order found for payment reference {$paymentReference}.");
            }

            $paymentStatus = match ($gatewayStatus) {
                'success', 'paid', 'completed' => 'paid',
                'failed', 'declined' => 'failed',
                default => 'pending',
            };

            $order->update([
                'payment_status' => $paymentStatus,
                'payment_reference' => $payload['reference'] ?? $order->payment_reference,
            ]);

            if ($paymentStatus === 'paid') {
                $this->orders->addStatusHistory($order->id, $order->status->value, 'Payment confirmed via gateway.');
                event(new PaymentVerified($order));
            }

            return $order->fresh();
        });
    }

    /**
     * Manual verification by an admin (e.g. bank transfer proof review).
     */
    public function verifyManually(int $orderId, ?int $adminId, bool $approved, ?string $notes = null): Model
    {
        return DB::transaction(function () use ($orderId, $adminId, $approved, $notes) {
            $order = $this->orders->findOrFail($orderId);

            $order->update([
                'payment_status' => $approved ? 'paid' : 'failed',
            ]);

            $this->orders->addStatusHistory(
                $orderId,
                $order->status->value,
                $notes ?? ($approved ? 'Payment verified manually.' : 'Payment rejected manually.'),
                $adminId,
            );

            if ($approved) {
                event(new PaymentVerified($order));
            }

            return $order->fresh();
        });
    }
}
