<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Shiprocket Configuration
    |--------------------------------------------------------------------------
    */
    'shiprocket' => [
        'api_url' => env('SHIPROCKET_API_URL', 'https://apiv2.shiprocket.in/v1/external'),
        'email' => env('SHIPROCKET_EMAIL'),
        'password' => env('SHIPROCKET_PASSWORD'),
        'pickup_location' => env('SHIPROCKET_PICKUP_LOCATION', 'work'),
        'channel_id' => env('SHIPROCKET_CHANNEL_ID'),
        'default_courier_id' => env('SHIPROCKET_DEFAULT_COURIER_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipmozo Configuration
    |--------------------------------------------------------------------------
    */
    'shipmozo' => [
        'api_url' => env('SHIPMOZO_API_URL', 'https://shipping-api.com/api/v1'),
        'api_key' => env('SHIPMOZO_API_KEY'),
        'pickup_name' => env('SHIPMOZO_PICKUP_NAME'),
        'pickup_phone' => env('SHIPMOZO_PICKUP_PHONE'),
        'pickup_address' => env('SHIPMOZO_PICKUP_ADDRESS'),
        'pickup_city' => env('SHIPMOZO_PICKUP_CITY'),
        'pickup_state' => env('SHIPMOZO_PICKUP_STATE'),
        'pickup_pincode' => env('SHIPMOZO_PICKUP_PINCODE'),
    ],

];