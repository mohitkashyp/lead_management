<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CommonController extends Controller
{
    public static function getInfoFromPin($pin_code)
    {
        if (strlen($pin_code) == 6) {

            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                        'Accept' => 'application/json',
                    ])
                    ->retry(4, 200)
                    ->get("https://api.postalpincode.in/pincode/" . $pin_code);

                if ($response->successful()) {

                    return $data = $response->json();

               }

            } catch (\Exception $e) {
                logger("Pincode API Error: " . $e->getMessage());
            }
        }
    }

}