<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;

class OrderShow extends Component
{
    public $order;
    public $shipment;

    public function mount($id)
    {
        $this->order = Order::with([
            'customer',
            'items',
            'items.product',
            'shipment.shippingProvider'
        ])->findOrFail($id);

        $this->shipment = $this->order->shipment;
    }

    // 🔁 Fetch latest tracking from provider
    public function refreshTracking()
    {
        if (!$this->shipment) {
            session()->flash('error', 'No shipment found');
            return;
        }

        try {
            $provider = $this->shipment->shippingProvider;

            if (strtolower($provider->name) === 'shiprocket') {

                $response = Http::withToken(config('services.shiprocket.token'))
                    ->get("https://apiv2.shiprocket.in/v1/external/courier/track", [
                        'order_id' => $this->shipment->tracking_number
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    $this->shipment->update([
                        'status' => $data['tracking_data']['shipment_status'] ?? 'unknown',
                        'tracking_history' => json_encode($data),
                    ]);

                    session()->flash('success', 'Tracking updated');
                }
            }

            // 👉 Add Shipmozo logic similarly if needed

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.order-show')->layout('layouts.app');
    }
}