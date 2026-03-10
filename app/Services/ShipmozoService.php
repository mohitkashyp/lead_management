<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Shipment;
use App\Models\Order;

class ShipmozoService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.shipmozo.api_url', 'https://api.shipmozo.com/api/v1');
        $this->apiKey = config('services.shipmozo.api_key');
    }

    /**
     * Create order in Shipmozo
     */
    public function createOrder(Order $order)
    {
        $orderData = [
            'order_number' => $order->order_number,
            'order_date' => $order->created_at->format('Y-m-d'),
            'payment_mode' => $order->payment_method === 'cod' ? 'COD' : 'Prepaid',
            'total_amount' => $order->total,
            'customer' => [
                'name' => $order->customer->name,
                'email' => $order->customer->email ?? '',
                'phone' => $order->customer->phone,
                'address' => $order->shipping_address,
                'city' => $order->shipping_city,
                'state' => $order->shipping_state,
                'pincode' => $order->shipping_pincode,
                'country' => $order->shipping_country,
            ],
            'products' => $this->formatOrderItems($order),
            'pickup_address' => [
                'name' => config('services.shipmozo.pickup_name'),
                'phone' => config('services.shipmozo.pickup_phone'),
                'address' => config('services.shipmozo.pickup_address'),
                'city' => config('services.shipmozo.pickup_city'),
                'state' => config('services.shipmozo.pickup_state'),
                'pincode' => config('services.shipmozo.pickup_pincode'),
            ],
            'weight' => $this->calculateWeight($order),
            'dimensions' => [
                'length' => 10,
                'width' => 10,
                'height' => 10,
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/orders/create', $orderData);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to create Shipmozo order: ' . $response->body());
    }

    /**
     * Create shipment
     */
    public function createShipment(Order $order, $shipmozoOrderId, $courierId = null)
    {
        $shipmentData = [
            'order_id' => $shipmozoOrderId,
        ];

        if ($courierId) {
            $shipmentData['courier_id'] = $courierId;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/shipments/create', $shipmentData);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to create shipment: ' . $response->body());
    }

    /**
     * Get available couriers
     */
    public function getAvailableCouriers($pincode, $weight, $codAmount = 0)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/couriers/serviceability', [
            'pickup_pincode' => config('services.shipmozo.pickup_pincode'),
            'delivery_pincode' => $pincode,
            'weight' => $weight,
            'cod_amount' => $codAmount,
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
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/track/' . $awbNumber);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Generate shipping label
     */
    public function generateLabel($shipmentId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/shipments/' . $shipmentId . '/label');

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to generate label: ' . $response->body());
    }

    /**
     * Cancel shipment
     */
    public function cancelShipment($awbNumber)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/shipments/cancel', [
            'awb_number' => $awbNumber,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to cancel shipment: ' . $response->body());
    }

    /**
     * Schedule pickup
     */
    public function schedulePickup($shipmentId, $pickupDate)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/pickups/schedule', [
            'shipment_id' => $shipmentId,
            'pickup_date' => $pickupDate,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Failed to schedule pickup: ' . $response->body());
    }

    /**
     * Format order items for Shipmozo
     */
    protected function formatOrderItems(Order $order)
    {
        return $order->items->map(function ($item) {
            return [
                'name' => $item->product_name,
                'sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'tax' => $item->tax ?? 0,
            ];
        })->toArray();
    }

    /**
     * Calculate total weight
     */
    protected function calculateWeight(Order $order)
    {
        // Default weight if product weight not available
        $totalWeight = 0;
        
        foreach ($order->items as $item) {
            $productWeight = $item->product->weight ?? 0.5; // Default 0.5 kg
            $totalWeight += $productWeight * $item->quantity;
        }

        return max($totalWeight, 0.5); // Minimum 0.5 kg
    }
}