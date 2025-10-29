<?php

namespace BeeDelivery\GoogleMaps\Utils;

use Exception;

trait HelpersRoutes
{
    public function url()
    {
        return config('googlemaps.routes_url');
    }

    public function formatFieldMask(bool $optimize): string
    {
        $fieldMask = 'routes.distanceMeters,routes.duration';
        return $optimize ? $fieldMask . ',routes.optimizedIntermediateWaypointIndex' : $fieldMask;
    }

    public function formatRequest(array $origin, array $destination, array $waypoints, bool $optimize, string $mode): array
    {
        if (count($waypoints) < 2) {
            throw new \Exception('You must provide at least two waypoints');
        }

        $dataSearch = [];

        $dataSearch['origin'] = [
            'location' => [
                'latLng' => [
                    'latitude' => $origin['lat'],
                    'longitude' => $origin['lng']
                ]
            ]
        ];

        foreach ($waypoints as $waypoint) {
            $dataSearch['intermediates'][] = [
                'location' => [
                    'latLng' => [
                        'latitude' => $waypoint['lat'],
                        'longitude' => $waypoint['lng']
                    ]
                ]
            ];
        }

        $dataSearch['destination'] = [
            'location' => [
                'latLng' => [
                    'latitude' => $destination['lat'],
                    'longitude' => $destination['lng']
                ]
            ]
        ];

        $dataSearch['optimizeWaypointOrder'] = $optimize;
        $dataSearch['travelMode'] = $mode;

        return $dataSearch;
    }

    public function formatResponse(array $response): array
    {
        if (isset($response['error'])) {
            return [
                'code' => $response['error']['code'],
                'message' => $response['error']['message']
            ];
        }

        $distance = (int) $response['routes'][0]['distanceMeters'] ?? 0;
        $duration = (int) $response['routes'][0]['duration'] ?? 0;

        if ($distance == 0) {
            throw new Exception('Distance can not be zero', 404);
        }

        $response = [
            'distance' => $distance,
            'distance_in_quilometers' => number_format(($distance / 1000), 2),
            'duration_in_seconds' => $duration,
            'duration_in_minutes' => number_format(($duration / 60), 2),
            'waypoint_order' => $response['routes'][0]['optimizedIntermediateWaypointIndex'] ?? '',
        ];

        return $response;
    }

    public function formatCompleteResponse(array $response): array
    {
        if (isset($response['error'])) {
            return [
                'code' => $response['error']['code'],
                'message' => $response['error']['message']
            ];
        }

        return $response;
    }
}
