<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'country',
        'logo',
        'website',
        'gstin',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (!$organization->slug) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    // Relationships
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_organizations')
            ->withPivot(['role_id', 'is_default', 'is_active'])
            ->withTimestamps();
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function addUser(User $user, $roleId, $isDefault = false)
    {
        return $this->users()->attach($user->id, [
            'role_id' => $roleId,
            'is_default' => $isDefault,
            'is_active' => true,
        ]);
    }

    public function removeUser(User $user)
    {
        return $this->users()->detach($user->id);
    }

    public function updateUserRole(User $user, $roleId)
    {
        return $this->users()->updateExistingPivot($user->id, [
            'role_id' => $roleId,
        ]);
    }
    public function secrets()
    {
        return $this->hasMany(OrganizationSecret::class);
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
}