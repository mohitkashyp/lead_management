<?php

namespace App\Services;

use App\Models\OrganizationSecret;

class SecretManager
{
    public static function set($orgId,$providerId,$key,$value)
    {
        OrganizationSecret::updateOrCreate(
            [
                'organization_id'=>$orgId,
                'shipping_provider_id'=>$providerId,
                'key'=>$key
            ],
            [
                'value'=>$value
            ]
        );
    }

    public static function get($orgId,$providerId,$key)
    {
        $secret = OrganizationSecret::where([
            'organization_id'=>$orgId,
            'shipping_provider_id'=>$providerId,
            'key'=>$key
        ])->first();

        return $secret?->value;
    }
}