<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'api_endpoint',
        'api_key',
        'api_secret',
        'config',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
    public function secrets()
    {
        return $this->hasMany(OrganizationSecret::class);
    }
}