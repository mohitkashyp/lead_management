<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use  HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'current_organization_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function currentOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'user_organizations')
            ->withPivot(['role_id', 'is_default', 'is_active'])
            ->withTimestamps();
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function createdOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function leadActivities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    // Helper methods for multi-organization
    public function switchOrganization($organizationId)
    {
        // Check if user belongs to this organization
        if (!$this->organizations()->where('organizations.id', $organizationId)->exists()) {
            throw new \Exception('User does not belong to this organization');
        }

        $this->current_organization_id = $organizationId;
        $this->save();

        return $this;
    }

    public function getDefaultOrganization()
    {
        return $this->organizations()
            ->wherePivot('is_default', true)
            ->first();
    }

    public function getRoleInOrganization($organizationId = null)
    {
        $orgId = $organizationId ?? $this->current_organization_id;
        
        $pivot = $this->organizations()
            ->where('organizations.id', $orgId)
            ->first()
            ?->pivot;

        if ($pivot) {
            return Role::find($pivot->role_id);
        }

        return $this->role; // Fallback to global role
    }

    public function hasAccessToOrganization($organizationId): bool
    {
        return $this->organizations()
            ->where('organizations.id', $organizationId)
            ->wherePivot('is_active', true)
            ->exists();
    }

    // Role checks (considering current organization)
    public function isAdmin(): bool
    {
        $role = $this->getRoleInOrganization();
        return $role && $role->name === 'admin';
    }

    public function isManager(): bool
    {
        $role = $this->getRoleInOrganization();
        return $role && $role->name === 'manager';
    }

    public function isAgent(): bool
    {
        $role = $this->getRoleInOrganization();
        return $role && $role->name === 'agent';
    }

    public function canManageAllLeads(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    // Scoped queries for current organization
    public function scopeForOrganization($query, $organizationId = null)
    {
        $orgId = $organizationId ?? auth()->user()?->current_organization_id;
        
        return $query->whereHas('organizations', function ($q) use ($orgId) {
            $q->where('organizations.id', $orgId);
        });
    }
}