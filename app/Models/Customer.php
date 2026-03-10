<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'notes',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function repurchaseReminders(): HasMany
    {
        return $this->hasMany(RepurchaseReminder::class);
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->pincode,
            $this->country,
        ]));
    }

    public function getTotalOrdersAttribute(): int
    {
        return $this->orders()->count();
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->orders()->sum('total');
    }

    public function getLastOrderDateAttribute()
    {
        return $this->orders()->latest()->first()?->created_at;
    }
}