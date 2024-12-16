<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleMapsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('app.google_api_key');
    }

    public function getCoordinates(string $address): ?array
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json";

        $response = Http::get($url, [
            'address' => $address,
            'key' => $this->apiKey,
        ]);

        if ($response->successful() && isset($response->json()['results'][0])) {
            $location = $response->json()['results'][0]['geometry']['location'];

            return [
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
            ];
        }

        return null;
    }
}
