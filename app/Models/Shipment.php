<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipping_provider_id',
        'tracking_number',
        'awb_number',
        'shipment_id',
        'status',
        'shipping_cost',
        'courier_name',
        'tracking_url',
        'label_url',
        'tracking_history',
        'shipped_at',
        'delivered_at',
        'last_synced_at',
        'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'tracking_history' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function shippingProvider(): BelongsTo
    {
        return $this->belongsTo(ShippingProvider::class);
    }

    public function scopeInTransit($query)
    {
        return $query->whereIn('status', ['picked', 'in_transit', 'out_for_delivery']);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function updateTrackingHistory(array $newStatus): void
    {
        $history = $this->tracking_history ?? [];
        $history[] = array_merge($newStatus, ['timestamp' => now()]);
        $this->tracking_history = $history;
        $this->last_synced_at = now();
        $this->save();
    }
}