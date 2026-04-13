<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Order;
use Exception;

class ShipmozoService
{
    protected $baseUrl;
    protected $publicKey;
    protected $privateKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.shipmozo.api_url', 'https://shipping-api.com/app/api/v1'), '/');
        $this->publicKey = config('services.shipmozo.public_key');
        $this->privateKey = config('services.shipmozo.private_key');
    }

    /**
     * Get headers with authentication keys
     */
    protected function getHeaders()
    {
        return [
            'public-key' => $this->publicKey,
            'private-key' => $this->privateKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Check if API is operational
     */
    public function checkApiStatus()
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/info');

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception('API status check failed: ' . $response->body());
    }

    /**
     * Authenticate user and get keys
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * Set private key
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * Set base URL
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Create and push order (complete workflow)
     */
    public function createAndPushOrder(Order $order, $warehouseId = null)
    {
        // If warehouse_id not provided, get default warehouse
        if (!$warehouseId) {
            $warehouses = $this->getWarehouses();
            if (!empty($warehouses)) {
                foreach ($warehouses as $warehouse) {
                    if ($warehouse['default'] === 'YES') {
                        $warehouseId = $warehouse['id'];
                        break;
                    }
                }
                // If no default, use first active warehouse
                if (!$warehouseId && !empty($warehouses)) {
                    $warehouseId = $warehouses[0]['id'];
                }
            }
        }

        if (!$warehouseId) {
            throw new Exception('No warehouse ID provided or found');
        }

        // Push the order
        return $this->pushOrder($order, $warehouseId);
    }

    /**
     * Push order to Shipmozo
     */
    public function pushOrder(Order $order, $warehouseId)
    {
        $orderData = [
            'order_id' => $order->order_number,
            'order_date' => $order->created_at->format('Y-m-d'),
            'consignee_name' => $order->customer->name,
            'consignee_phone' => $order->customer->phone,
            'consignee_alternate_phone' => $order->customer->alternate_phone ?? '',
            'consignee_email' => $order->customer->email ?? '',
            'consignee_address_line_one' => $order->shipping_address,
            'consignee_address_line_two' => $order->shipping_address_line_two ?? '',
            'consignee_pin_code' => $order->shipping_pincode,
            'consignee_city' => $order->shipping_city,
            'consignee_state' => $order->shipping_state,
            'product_detail' => $this->formatOrderItems($order),
            'payment_type' => $order->payment_method === 'cod' ? 'COD' : 'PREPAID',
            'cod_amount' => $order->payment_method === 'cod' ? (string)$order->total : '',
            'shipping_charges' => $order->payment_method === 'cod' ? (string)$order->shipping_cost : '',
            'weight' => $this->calculateWeight($order) * 1000, // Convert to grams as per API
            'length' => $order->length ?? 10,
            'width' => $order->width ?? 10,
            'height' => $order->height ?? 10,
            'warehouse_id' => $warehouseId,
            'gst_ewaybill_number' => $order->gst_ewaybill_number ?? '',
            'gstin_number' => $order->gstin_number ?? '',
        ];

        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/push-order', $orderData);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result;
            }
            dd($result['message']);
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to push order: ' . $response->body());
    }

    /**
     * Push return order
     */
    public function pushReturnOrder(array $returnData)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/push-return-order', $returnData);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result;
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to push return order: ' . $response->body());
    }

    /**
     * Assign courier to order
     */
    public function assignCourier($orderId, $courierId)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/assign-courier', [
                'order_id' => $orderId,
                'courier_id' => $courierId,
            ]);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result;
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to assign courier: ' . $response->body());
    }

    /**
     * Auto assign courier (requires setup in panel)
     */
    public function autoAssignCourier($orderId)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/auto-assign-order', [
                'order_id' => $orderId,
            ]);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result;
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to auto assign courier: ' . $response->body());
    }

    /**
     * Schedule pickup for order
     */
    public function schedulePickup($orderId)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/schedule-pickup', [
                'order_id' => $orderId,
            ]);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result;
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to schedule pickup: ' . $response->body());
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId, $awbNumber)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/cancel-order', [
                'order_id' => $orderId,
                'awb_number' => $awbNumber,
            ]);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result;
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to cancel order: ' . $response->body());
    }

    /**
     * Get order details
     */
    public function getOrderDetail($orderId)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/get-order-detail/' . $orderId);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception('Failed to get order details: ' . $response->body());
    }

    /**
     * Get order label by AWB number
     */
    public function getOrderLabel($awbNumber)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/get-order-label/' . $awbNumber);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result;
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to get order label: ' . $response->body());
    }

    /**
     * Track order by AWB number
     */
    public function trackOrder($awbNumber)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/track-order', [
                'awb_number' => $awbNumber,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception('Failed to track order: ' . $response->body());
    }

    /**
     * Check pincode serviceability
     */
    public function checkServiceability($pickupPincode, $deliveryPincode)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/pincode-serviceability', [
                'pickup_pincode' => $pickupPincode,
                'delivery_pincode' => $deliveryPincode,
            ]);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result['data']['serviceable'] ?? false;
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to check serviceability: ' . $response->body());
    }

    /**
     * Get return reasons
     */
    public function getReturnReasons()
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/get-return-reason');

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result['data'];
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to get return reasons: ' . $response->body());
    }

    /**
     * Create warehouse
     */
    public function createWarehouse(array $warehouseData)
    {
        $requiredFields = ['address_title', 'address_line_one', 'pin_code'];
        foreach ($requiredFields as $field) {
            if (empty($warehouseData[$field])) {
                throw new Exception("{$field} is required for creating warehouse");
            }
        }

        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/create-warehouse', $warehouseData);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result['data']['warehouse_id'];
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to create warehouse: ' . $response->body());
    }

    /**
     * Get all warehouses
     */
    public function getWarehouses()
    {
       
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/get-warehouses');

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result['data'];
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to get warehouses: ' . $response->body());
    }

    /**
     * Update order warehouse
     */
    public function updateOrderWarehouse($orderId, $warehouseId)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/order/update-warehouse', [
                'order_id' => $orderId,
                'warehouse_id' => $warehouseId,
            ]);

        if ($response->successful()) {
            $result = $response->json();
            if ($result['result'] === '1') {
                return $result;
            }
            throw new Exception($result['message']);
        }

        throw new Exception('Failed to update order warehouse: ' . $response->body());
    }

    /**
     * Format order items for Shipmozo API
     */
    protected function formatOrderItems(Order $order)
    {
        return $order->items->map(function ($item) {
            return [
                'name' => $item->product_name,
                'sku_number' => $item->product_sku ?? '',
                'quantity' => (int)$item->quantity,
                'discount' => (string)($item->discount ?? ''),
                'hsn' => $item->hsn_code ?? '',
                'unit_price' => (float)$item->price,
                'product_category' => $item->product_category ?? 'Other',
            ];
        })->toArray();
    }

    /**
     * Calculate total weight in kg
     */
    protected function calculateWeight(Order $order)
    {
        $totalWeight = 0;

        foreach ($order->items as $item) {
            $productWeight = $item->product->weight ?? 0.5; // Default 0.5 kg
            $totalWeight += $productWeight * $item->quantity;
        }

        return max($totalWeight, 0.5); // Minimum 0.5 kg
    }
}
