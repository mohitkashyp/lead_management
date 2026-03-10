<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepurchaseReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'last_order_id',
        'product_id',
        'last_purchase_date',
        'next_reminder_date',
        'reminder_interval_days',
        'status',
        'assigned_to',
        'notes',
        'reminded_at',
        'converted_at',
    ];

    protected $casts = [
        'last_purchase_date' => 'date',
        'next_reminder_date' => 'date',
        'reminder_interval_days' => 'integer',
        'reminded_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lastOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'last_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDueToday($query)
    {
        return $query->where('next_reminder_date', '<=', today())
            ->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_reminder_date', '<', today())
            ->where('status', 'pending');
    }

    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->reminded_at = now();
        $this->save();
    }

    public function markAsConverted(): void
    {
        $this->status = 'converted';
        $this->converted_at = now();
        $this->save();
    }
}