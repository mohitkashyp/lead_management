<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ShippingProvider;
use App\Services\ShipmozoService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderCreate extends Component
{
    public $lead_id;
    public $lead;
    public $customer_id;
    public $customer;
    
    // Order fields
    public $payment_method = 'cod';
    public $payment_status = 'pending';
    public $shipping_address;
    public $shipping_city;
    public $shipping_state;
    public $shipping_pincode;
    public $shipping_country = 'India';
    public $billing_address;
    public $notes;
    public $discount = 0;
    public $shipping_cost = 0;
    // Removed: public $tax_rate = 18; // Tax is now per-product
    
    // Shipping provider
    public $available_shipping_providers = [];
    public $selected_shipping_provider_id;
    public $create_shipment = false;
    public $organization = false;
    public $shipping_provider_selected = null;
    
    // Order items
    public $items = [];
    public $products;
    
    // Quick convert flag
    public $convertingLead = false;

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'payment_method' => 'required|in:cod,prepaid,online',
        'payment_status' => 'required|in:pending,paid,failed',
        'shipping_address' => 'required|string',
        'shipping_city' => 'required|string|max:100',
        'shipping_state' => 'required|string|max:100',
        'shipping_pincode' => 'required|string|max:10',
        'shipping_country' => 'required|string|max:100',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
    ];

    public function mount($lead = null)
    {
       // $this->organization=Auth::user()->current_organization_id;
        $this->products = Product::where('organization_id', Auth::user()->current_organization_id)
            ->where('is_active', true)
            ->get();

        // Load organization's enabled shipping providers
        $this->loadAvailableShippingProviders();

        if ($lead) {
            $this->lead_id = $lead;
            $this->lead = Lead::with('customer')->findOrFail($lead);
            
            // If lead already has customer, use it
            if ($this->lead->customer_id) {
                $this->customer_id = $this->lead->customer_id;
                $this->customer = $this->lead->customer;
                $this->populateFromCustomer();
            } else {
                // Prepare for quick conversion
                $this->convertingLead = true;
                $this->populateFromLead();
            }
        }

        // Add one empty item by default
        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function loadAvailableShippingProviders()
    {
        $organization = Auth::user()->currentOrganization;
        $settings = $organization->settings ?? [];
        
        // Get enabled shipping provider IDs
        $enabledProviderIds = $settings['shipping_providers'] ?? [];
        
        if (!empty($enabledProviderIds)) {
            $this->available_shipping_providers = ShippingProvider::whereIn('id', $enabledProviderIds)
                ->where('is_active', true)
                ->get();
        } else {
            $this->available_shipping_providers = collect([]);
        }
        
        // Auto-select first provider if only one available
        if ($this->available_shipping_providers && $this->available_shipping_providers->count() === 1) {
            $this->selected_shipping_provider_id = $this->available_shipping_providers->first()->id;
            $this->shipping_provider_selected = $this->available_shipping_providers->first();
        }
    }

    public function updatedSelectedShippingProviderId($value)
    {
        if ($value) {
            $this->shipping_provider_selected = ShippingProvider::find($value);
        } else {
            $this->shipping_provider_selected = null;
            $this->create_shipment = false;
        }
    }

    public function populateFromLead()
    {
        $this->shipping_address = $this->lead->address;
        $this->shipping_city = $this->lead->city;
        $this->shipping_state = $this->lead->state;
        $this->shipping_pincode = $this->lead->pincode;
        $this->billing_address = $this->lead->address;
    }

    public function populateFromCustomer()
    {
        $this->shipping_address = $this->customer->address;
        $this->shipping_city = $this->customer->city;
        $this->shipping_state = $this->customer->state;
        $this->shipping_pincode = $this->customer->pincode;
        $this->billing_address = $this->customer->address;
    }

    public function quickConvertLead()
    {
        if (!$this->lead) {
            return;
        }

        try {
            $this->customer = $this->lead->convertToCustomer();
            $this->customer_id = $this->customer->id;
            $this->convertingLead = false;
            
            session()->flash('success', 'Lead converted to customer successfully!');
            
            // Log activity
            $this->lead->activities()->create([
                'user_id' => Auth::id(),
                'activity_type' => 'status_change',
                'subject' => 'Lead Converted to Customer',
                'description' => 'Lead was converted to customer: ' . $this->customer->name,
                'activity_date' => now(),
            ]);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to convert lead: ' . $e->getMessage());
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
            'price' => 0,
            'discount' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        // When product is selected, auto-fill price
        if (strpos($key, 'product_id') !== false) {
            $index = explode('.', $key)[0];
            $productId = $this->items[$index]['product_id'];
            
            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $this->items[$index]['price'] = $product->price;
                }
            }
        }
    }

    public function getSubtotalProperty()
    {
        $subtotal = 0;
        foreach ($this->items as $item) {
            if (isset($item['quantity']) && isset($item['price'])) {
                $itemTotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);
                $subtotal += $itemTotal;
            }
        }
        return $subtotal;
    }

    public function getTaxProperty()
    {
        $totalTax = 0;
        foreach ($this->items as $item) {
            if (isset($item['product_id']) && isset($item['quantity']) && isset($item['price'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $itemTotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);
                    $taxRate = $product->getTaxRate();
                    $itemTax = ($itemTotal * $taxRate) / 100;
                    $totalTax += $itemTax;
                }
            }
        }
        return $totalTax;
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->tax + $this->shipping_cost - $this->discount;
    }

    public function save()
    {
        // If converting lead, do it first
        if ($this->convertingLead && $this->lead) {
            $this->quickConvertLead();
            
            if (!$this->customer_id) {
                return; // Conversion failed
            }
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'organization_id' => Auth::user()->current_organization_id,
                'customer_id' => $this->customer_id,
                'lead_id' => $this->lead_id,
                'created_by' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'shipping_cost' => $this->shipping_cost,
                'discount' => $this->discount,
                'total' => $this->total,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
                'shipping_address' => $this->shipping_address,
                'shipping_city' => $this->shipping_city,
                'shipping_state' => $this->shipping_state,
                'shipping_pincode' => $this->shipping_pincode,
                'shipping_country' => $this->shipping_country,
                'billing_address' => $this->billing_address ?? $this->shipping_address,
                'notes' => $this->notes,
            ]);

            // Create order items
            foreach ($this->items as $item) {
                $product = Product::find($item['product_id']);
                
                $itemSubtotal = ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0);
                $taxRate = $product->getTaxRate();
                $itemTax = ($itemSubtotal * $taxRate) / 100;
                $itemTotal = $itemSubtotal + $itemTax;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax_rate' => $taxRate,
                    'tax' => $itemTax,
                    'total' => $itemTotal,
                ]);

                // Decrease stock
                $product->decreaseStock($item['quantity']);
            }

            // If order created from lead, update lead
            if ($this->lead) {
                $this->lead->activities()->create([
                    'user_id' => Auth::id(),
                    'activity_type' => 'other',
                    'subject' => 'Order Created',
                    'description' => 'Order ' . $order->order_number . ' created for ₹' . number_format($order->total, 2),
                    'activity_date' => now(),
                ]);

                // Update lead status to converted if not already
                if (!$this->lead->converted_at) {
                    $convertedStatus = \App\Models\LeadStatus::where('name', 'converted')->first();
                    if ($convertedStatus) {
                        $this->lead->update([
                            'lead_status_id' => $convertedStatus->id,
                            'converted_at' => now(),
                        ]);
                    }
                }
            }

            // Create shipment if requested
             if ($this->create_shipment && $this->selected_shipping_provider_id) {
                try {
                    $provider = ShippingProvider::find($this->selected_shipping_provider_id);
                    
                    // Create shipment record
                    $shipment = \App\Models\Shipment::create([
                        'order_id' => $order->id,
                        'shipping_provider_id' => $provider->id,
                        'status' => 'pending',
                    ]);

                    // Get organization's provider config
                    $organization = Auth::user()->currentOrganization;
                    $settings = $organization->settings ?? [];
                    $providerKey = strtolower(str_replace(' ', '_', $provider->name));
                    $providerConfig = $settings['shipping_config'][$providerKey] ?? null;

                    if ($providerConfig) {
                        // Initialize appropriate shipping service based on provider
                        if (strtolower($provider->name) === 'shiprocket') {
                            $shippingService = new \App\Services\ShiprocketService(
                                $providerConfig['api_key'],
                                $providerConfig['api_secret'],
                                $providerConfig['api_endpoint'] ?? $provider->api_endpoint
                            );
                            
                            // Create order on Shiprocket
                            $response = $shippingService->createOrder($order);
                            
                            if ($response && isset($response['order_id'])) {
                                $shipment->update([
                                    'tracking_number' => $response['order_id'],
                                    'status' => 'created',
                                    'tracking_history' => json_encode([$response]),
                                ]);
                            }
                        } elseif (strtolower($provider->name) === 'shipmozo') {
                            try {
                                // Initialize the service
                                $shippingService = new ShipmozoService();
                                
                                // Configure if needed (override config values)
                                if (!empty($providerConfig['public_key'])) {
                                    $shippingService->setPublicKey($providerConfig['public_key']);
                                }
                                if (!empty($providerConfig['private_key'])) {
                                    $shippingService->setPrivateKey($providerConfig['private_key']);
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
                                // Check response and update shipment
                                if ($response && isset($response['result']) && $response['result'] === '1') {
                                    $orderData = $response['data'];
                                    
                                    $shipment->update([
                                        'tracking_number' => $orderData['order_id'] ?? $orderData['reference_id'],
                                        'reference_id' => $orderData['reference_id'] ?? null,
                                        'status' => 'created',
                                        'tracking_history' => json_encode([$response]),
                                        'shipmozo_response' => json_encode($response),
                                    ]);
                                    
                                   
                                    
                                } else {
                                    throw new Exception($response['message'] ?? 'Unknown error occurred');
                                }
                                
                            } catch (Exception $e) {
                                
                                $shipment->update([
                                    'status' => 'failed',
                                    'error_message' => $e->getMessage(),
                                ]);
                                
                                throw $e;
                            }
                        }

                        session()->flash('shipment_info', 'Shipment created successfully on ' . $provider->display_name);
                    } else {
                        session()->flash('shipment_warning', 'Shipping provider not configured for this organization.');
                    }
                } catch (\Exception $e) {
                    session()->flash('shipment_error', 'Failed to create shipment: ' . $e->getMessage());
                }
            }
 
            DB::commit();

            session()->flash('success', 'Order created successfully! Order #: ' . $order->order_number);
            
            return redirect()->route('orders.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.order-create', [
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,
        ])->layout('layouts.app');
    }
}