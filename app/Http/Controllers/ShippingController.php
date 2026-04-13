<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Order;
use App\Models\ShippingProvider;
use App\Models\User;
use App\Services\ShipmozoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ShippingController extends Controller
{
    function test2(){
        $response = Http::timeout(15)
        ->withOptions([
            'verify' => false,  // Disable SSL peer verification
        ])        
        ->retry(4, 200)
                ->get("http://api.postalpincode.in/pincode/151001");
                dd($response);
    }
    function test()
    {

        $order= Order::with('items')->latest()->first();
       
        $provider = ShippingProvider::find(2);
        $organization = Auth::user()->currentOrganization;
        $settings = $organization->settings ?? [];
        $providerKey = strtolower(str_replace(' ', '_', $provider->name));
        $providerConfig = $settings['shipping_config'][$providerKey] ?? null;
        $shippingService = new ShipmozoService();
                           
                            // Configure if needed (override config values)
                            if (!empty($providerConfig['api_key'])) {
                                    $shippingService->setPublicKey($providerConfig['api_key']);
                                }
                                if (!empty($providerConfig['api_secret'])) {
                                    $shippingService->setPrivateKey($providerConfig['api_secret']);
                                }
                                if (!empty($providerConfig['api_endpoint'])) {
                                    $shippingService->setBaseUrl($providerConfig['api_endpoint']);
                                }
                                
                                // First, check if API is working
                                $status = $shippingService->checkApiStatus();
                              
                                // Get warehouse ID (you might need to fetch or create one)
                                $warehouses = $shippingService->getWarehouses();
                                
                                $warehouseId = null;
                                
                                if (!empty($warehouses)) {
                                    // Use first active warehouse or default
                                    foreach ($warehouses as $warehouse) {
                                        if ($warehouse['status'] === 'ACTIVE') {
                                            $warehouseId = $warehouse['id'];
                                            break;
                                        }
                                    }
                                }
                                
                                if (!$warehouseId) {
                                    // Create a warehouse if none exists
                                    $warehouseId = $shippingService->createWarehouse([
                                        'address_title' => 'Default Warehouse',
                                        'name' => $order->customer->name ?? 'Default',
                                        'phone' => $order->customer->phone ?? '9999999999',
                                        'email' => $order->customer->email ?? 'default@example.com',
                                        'address_line_one' => $order->shipping_address_line_one ?? 'Default Address',
                                        'pin_code' => $order->shipping_pincode ?? '110001',
                                    ]);
                                }
                                
                                // Push order to Shipmozo
                                $response = $shippingService->pushOrder($order, $warehouseId);
                                dd($response);
    }

}