<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Shipment;
use App\Models\Order;

class ShiprocketService
{
    protected $baseUrl;
    protected $email;
    protected $password;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.shiprocket.api_url', 'https://apiv2.shiprocket.in/v1/external');
        $this->email = config('services.shiprocket.email');
        $this->password = config('services.shiprocket.password');
    }

    /**
     * Authenticate and get token
     */
    public function authenticate()
    {
        $response = Http::post($this->baseUrl . '/auth/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($response->successful()) {
            $this->token = $response->json()['token'];
            return $this->token;
        }

        throw new \Exception('Shiprocket authentication failed');
    }

    /**
     * Create order in Shiprocket
     */
    public function createOrder(Order $order)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $orderData = [
            'order_id' => $order->order_number,
            'order_date' => $order->created_at->format('Y-m-d H:i'),
            'pickup_location' => config('services.shiprocket.pickup_location', 'Primary'),
            'channel_id' => config('services.shiprocket.channel_id', ''),
            'billing_customer_name' => $order->customer->name,
            'billing_last_name' => '',
            'billing_address' => $order->shipping_address,
            'billing_city' => $order->shipping_city,
            'billing_pincode' => $order->shipping_pincode,
            'billing_state' => $order->shipping_state,
            'billing_country' => $order->shipping_country,
            'billing_email' => $order->customer->email ?? '',
            'billing_phone' => $order->customer->phone,
            'shipping_is_billing' => true,
            'order_items' => $this->formatOrderItems($order),
            'payment_method' => $order->payment_method ?? 'Prepaid',
            'sub_total' => $order->subtotal,
            'length' => 10, // Default dimensions
            'breadth' => 10,
            'height' => 10,
            'weight' => 0.5, // Default weight in kg
        ];

        $response = Http::withToken($this->token)
            ->post($this->baseUrl . '/orders/create/adhoc', $orderData);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to create Shiprocket order: ' . $response->body());
    }

    /**
     * Create shipment
     */
    public function createShipment(Order $order, $shiprocketOrderId)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $shipmentData = [
            'order_id' => $shiprocketOrderId,
            'courier_id' => config('services.shiprocket.default_courier_id'),
        ];

        $response = Http::withToken($this->token)
            ->post($this->baseUrl . '/shipments/create/adhoc', $shipmentData);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to create shipment: ' . $response->body());
    }

    /**
     * Get available couriers for an order
     */
    public function getAvailableCouriers($shiprocketOrderId)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/courier/serviceability', [
                'order_id' => $shiprocketOrderId,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
    }

    /**
     * Track shipment
     */
    public function trackShipment($awbNumber)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/courier/track/awb/' . $awbNumber);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Generate AWB for shipment
     */
    public function generateAwb($shipmentId, $courierId)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $response = Http::withToken($this->token)
            ->post($this->baseUrl . '/courier/assign/awb', [
                'shipment_id' => $shipmentId,
                'courier_id' => $courierId,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to generate AWB: ' . $response->body());
    }

    /**
     * Generate shipping label
     */
    public function generateLabel($shipmentIds)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $response = Http::withToken($this->token)
            ->post($this->baseUrl . '/shipments/print', [
                'shipment_id' => $shipmentIds,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to generate label: ' . $response->body());
    }

    /**
     * Format order items for Shiprocket
     */
    protected function formatOrderItems(Order $order)
    {
        return $order->items->map(function ($item) {
            return [
                'name' => $item->product_name,
                'sku' => $item->product_sku,
                'units' => $item->quantity,
                'selling_price' => $item->price,
                'discount' => $item->discount ?? 0,
            ];
        })->toArray();
    }

    /**
     * Cancel shipment
     */
    public function cancelShipment($awbNumbers)
    {
        if (!$this->token) {
            $this->authenticate();
        }

        $response = Http::withToken($this->token)
            ->post($this->baseUrl . '/orders/cancel/shipment/awbs', [
                'awbs' => is_array($awbNumbers) ? $awbNumbers : [$awbNumbers],
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to cancel shipment: ' . $response->body());
    }
}