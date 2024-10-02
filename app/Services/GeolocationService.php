<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeolocationService
{
    public function coordinates(?string $street, ?int $number, string $city, ?string $neighborhood, string $state, string $country = 'Brasil', ?string $cep = null): array
    {
        $url = 'https://nominatim.openstreetmap.org/search?';

        $response = Http::withQueryParameters([
            'street' => "$number $street",
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'postalcode' => $cep,
            'format' => 'jsonv2',
        ])
            ->withUserAgent('Geolocation Service Test/1.0')
            ->get($url);

        if (count($response->json()) > 0) {
            $data = $response->json()[0];

            return [
                'latitude' => $data['lat'],
                'longitude' => $data['lon'],
            ];
        }

        return [];
    }
}
