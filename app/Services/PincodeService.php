<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PincodeService
{
    public function getLocationByPincode($pincode)
    {
        if (strlen($pincode) != 6) {
            return [
                'city' => '',
                'state' => ''
            ];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept' => 'application/json',
                ])
                ->retry(4, 200)
                ->get("https://api.postalpincode.in/pincode/" . $pincode);

            if ($response->successful()) {
                $data = $response->json();

                if (
                    isset($data[0]['Status']) &&
                    $data[0]['Status'] === 'Success' &&
                    !empty($data[0]['PostOffice'])
                ) {
                    $postOffice = $data[0]['PostOffice'][0];

                    return [
                        'city' => $postOffice['District'] ?? '',
                        'state' => $postOffice['State'] ?? ''
                    ];
                }
            }

        } catch (\Exception $e) {
            Log::error("Pincode API Error: " . $e->getMessage());
        }

        return [
            'city' => '',
            'state' => ''
        ];
    }
}