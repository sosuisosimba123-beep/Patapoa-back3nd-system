<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TomTomService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.tomtom.com';

    public function __construct()
    {
        $this->apiKey = config('services.tomtom.key');
    }

    /**
     * Calculate road distance and time between two points
     */
    public function getRoute(float $startLat, float $startLon, float $endLat, float $endLon)
    {
        if (!$this->apiKey) {
            Log::warning('TomTom API Key not configured.');
            return null;
        }

        $url = "{$this->baseUrl}/routing/1/calculateRoute/{$startLat},{$startLon}:{$endLat},{$endLon}/json";

        try {
            $response = Http::get($url, [
                'key' => $this->apiKey,
                'routeType' => 'fastest',
                'travelMode' => 'car',
                'traffic' => 'true'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['routes'])) {
                    $summary = $data['routes'][0]['summary'];
                    return [
                        'distance_km' => $summary['lengthInMeters'] / 1000,
                        'travel_time_seconds' => $summary['travelTimeInSeconds'],
                        'points' => $data['routes'][0]['legs'][0]['points'] ?? []
                    ];
                }
            } else {
                Log::error('TomTom API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('TomTom Service Exception', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Reverse geocode coordinates to address
     */
    public function reverseGeocode(float $lat, float $lon)
    {
        if (!$this->apiKey) return null;

        $url = "{$this->baseUrl}/search/2/reverseGeocode/{$lat},{$lon}.json";

        try {
            $response = Http::get($url, ['key' => $this->apiKey]);
            if ($response->successful()) {
                $data = $response->json();
                return $data['addresses'][0]['address']['freeformAddress'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('TomTom Geocode Error', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
