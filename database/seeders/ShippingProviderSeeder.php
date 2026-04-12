<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingProvider;

class ShippingProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'shiprocket',
                'display_name' => 'Shiprocket',
                'api_endpoint' => 'https://apiv2.shiprocket.in/v1/external',
                'is_active' => true,
            ],
            [
                'name' => 'shipmozo',
                'display_name' => 'Shipmozo',
                'api_endpoint' => 'https://shipping-api.com/api/v1',
                'is_active' => true,
            ],
            [
                'name' => 'delhivery',
                'display_name' => 'Delhivery',
                'api_endpoint' => 'https://track.delhivery.com/api',
                'is_active' => false,
            ],
        ];

        foreach ($providers as $provider) {
            ShippingProvider::updateOrCreate(
                ['name' => $provider['name']],
                $provider
            );
        }
    }
}