<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OrganizationSecret extends Model
{
    protected $fillable = [
        'organization_id',
        'shipping_provider_id',
        'key',
        'value'
    ];

    protected $hidden = ['value'];

    /*
    |--------------------------------------------------------------------------
    | Encrypt before saving
    |--------------------------------------------------------------------------
    */

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = Crypt::encryptString($value);
    }

    public function getValueAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function provider()
    {
        return $this->belongsTo(ShippingProvider::class,'shipping_provider_id');
    }
}