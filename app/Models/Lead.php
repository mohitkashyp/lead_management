<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'lead_number',
        'name',
        'email',
        'phone',
        'alternate_phone',
        'lead_source_id',
        'lead_status_id',
        'assigned_to',
        'customer_id',
        'address',
        'city',
        'state',
        'pincode',
        'product_interest',
        'estimated_value',
        'next_follow_up_date',
        'notes',
        'custom_fields',
        'converted_at',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'estimated_value' => 'decimal:2',
        'next_follow_up_date' => 'date',
        'converted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lead) {
            if (!$lead->lead_number) {
                $prefix = $lead->organization_id ? 'LD-' . $lead->organization_id . '-' : 'LD-';
                $lead->lead_number = $prefix . strtoupper(uniqid());
            }
            
            // Auto-set organization_id if not set
            if (!$lead->organization_id && auth()->check()) {
                $lead->organization_id = auth()->user()->current_organization_id;
            }
        });
    }

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeForOrganization($query, $organizationId = null)
    {
        $orgId = $organizationId ?? auth()->user()?->current_organization_id;
        return $query->where('organization_id', $orgId);
    }

    public function scopeAssignedToMe($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeBySource($query, $sourceId)
    {
        return $query->where('lead_source_id', $sourceId);
    }

    public function scopeByStatus($query, $statusId)
    {
        return $query->where('lead_status_id', $statusId);
    }

    public function scopeFollowUpToday($query)
    {
        return $query->whereDate('next_follow_up_date', today());
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_follow_up_date', '<', today())
            ->whereNull('converted_at');
    }

    // Helper methods
    public function isConverted(): bool
    {
        return !is_null($this->converted_at);
    }

    public function convertToCustomer(): Customer
    {
        if ($this->customer_id) {
            return $this->customer;
        }

        $customer = Customer::create([
            'organization_id' => $this->organization_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'alternate_phone' => $this->alternate_phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'notes' => $this->notes,
        ]);

        $this->update([
            'customer_id' => $customer->id,
            'converted_at' => now(),
        ]);

        return $customer;
    }
}