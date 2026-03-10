<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Shipment;
use App\Services\ShiprocketService;
use App\Services\ShipmozoService;

class SyncShipmentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all active shipments (not delivered or cancelled)
        $shipments = Shipment::with(['shippingProvider', 'order'])
            ->whereNotIn('status', ['delivered', 'failed', 'returned'])
            ->whereNotNull('awb_number')
            ->get();

        foreach ($shipments as $shipment) {
            try {
                $this->syncShipment($shipment);
            } catch (\Exception $e) {
                // Log error but continue with other shipments
                \Log::error('Failed to sync shipment ' . $shipment->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Sync individual shipment
     */
    protected function syncShipment(Shipment $shipment): void
    {
        $provider = $shipment->shippingProvider;
        $trackingData = null;

        // Get tracking data based on provider
        switch ($provider->name) {
            case 'shiprocket':
                $service = app(ShiprocketService::class);
                $trackingData = $service->trackShipment($shipment->awb_number);
                break;
                
            case 'shipmozo':
                $service = app(ShipmozoService::class);
                $trackingData = $service->trackShipment($shipment->awb_number);
                break;
                
            default:
                return;
        }

        if (!$trackingData) {
            return;
        }

        // Update shipment status based on tracking data
        $this->updateShipmentFromTracking($shipment, $trackingData, $provider->name);
    }

    /**
     * Update shipment from tracking data
     */
    protected function updateShipmentFromTracking(Shipment $shipment, array $trackingData, string $providerName): void
    {
        // Parse tracking data based on provider
        if ($providerName === 'shiprocket') {
            $this->updateFromShiprocket($shipment, $trackingData);
        } elseif ($providerName === 'shipmozo') {
            $this->updateFromShipmozo($shipment, $trackingData);
        }
    }

    /**
     * Update from Shiprocket tracking data
     */
    protected function updateFromShiprocket(Shipment $shipment, array $data): void
    {
        if (!isset($data['tracking_data'])) {
            return;
        }

        $tracking = $data['tracking_data'];
        
        // Map Shiprocket status to our status
        $statusMap = [
            'Pickup Scheduled' => 'created',
            'Picked Up' => 'picked',
            'In Transit' => 'in_transit',
            'Out For Delivery' => 'out_for_delivery',
            'Delivered' => 'delivered',
            'RTO' => 'returned',
            'Cancelled' => 'failed',
        ];

        $currentStatus = $tracking['shipment_status'] ?? '';
        $newStatus = $statusMap[$currentStatus] ?? $shipment->status;

        // Update shipment
        $shipment->update([
            'status' => $newStatus,
            'courier_name' => $tracking['courier_name'] ?? $shipment->courier_name,
            'last_synced_at' => now(),
        ]);

        // Add to tracking history
        if (isset($tracking['shipment_track'])) {
            foreach ($tracking['shipment_track'] as $track) {
                $shipment->updateTrackingHistory([
                    'status' => $track['current_status'],
                    'location' => $track['location'] ?? '',
                    'date' => $track['date'] ?? now(),
                ]);
            }
        }

        // Update order status if delivered
        if ($newStatus === 'delivered' && !$shipment->delivered_at) {
            $shipment->update(['delivered_at' => now()]);
            $shipment->order->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }
    }

    /**
     * Update from Shipmozo tracking data
     */
    protected function updateFromShipmozo(Shipment $shipment, array $data): void
    {
        if (!isset($data['tracking'])) {
            return;
        }

        $tracking = $data['tracking'];
        
        // Map Shipmozo status to our status
        $statusMap = [
            'created' => 'created',
            'picked' => 'picked',
            'in_transit' => 'in_transit',
            'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'rto' => 'returned',
            'cancelled' => 'failed',
        ];

        $currentStatus = strtolower($tracking['status'] ?? '');
        $newStatus = $statusMap[$currentStatus] ?? $shipment->status;

        // Update shipment
        $shipment->update([
            'status' => $newStatus,
            'courier_name' => $tracking['courier_name'] ?? $shipment->courier_name,
            'last_synced_at' => now(),
        ]);

        // Add to tracking history
        if (isset($tracking['scan_details'])) {
            foreach ($tracking['scan_details'] as $scan) {
                $shipment->updateTrackingHistory([
                    'status' => $scan['status'],
                    'location' => $scan['location'] ?? '',
                    'date' => $scan['date'] ?? now(),
                    'remarks' => $scan['remarks'] ?? '',
                ]);
            }
        }

        // Update order status if delivered
        if ($newStatus === 'delivered' && !$shipment->delivered_at) {
            $shipment->update(['delivered_at' => now()]);
            $shipment->order->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
        }
    }
}