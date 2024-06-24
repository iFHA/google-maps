<?php

namespace BeeDelivery\GoogleMaps\Utils;

use Exception;

trait HelpersDirections
{
    public function formatRequest($waypoints, $optimize, $mode)
    {
        $optimize = $optimize ? 'true' : 'false';

        if (count($waypoints) < 2) {
            throw new \Exception('You must provide at least two waypoints');
        } elseif (count($waypoints) == 2) {
            $searchUrl = $this->formatForTwoWaypoints($waypoints, $mode);
        } else {
            $searchUrl = $this->formatForMoreThatTwoWaypointsOptimize($waypoints, $optimize, $mode);
        }
        
        return config('googlemaps.directions_url') . $searchUrl;
    }

    public function formatResponse($response)
    {
        $distance = 0;
        $duration = 0;

        if ($response['status'] == 'OK' and isset($response['routes'][0]['legs'])) {
            foreach ($response['routes'][0]['legs'] as $key => $item) {
                $distance += ($item['distance']['value']);
                $duration += ($item['duration']['value']);
            }

            return [
                'distance' => $distance,
                'distance_in_quilometers' => number_format(($distance / 1000), 2),
                'duration_in_seconds' => $duration,
                'duration_in_minutes' => number_format(($duration / 60), 2),
                'waypoint_order' => $response['routes'][0]['waypoint_order'] ?? '',
            ];
        } elseif ($response['status'] == 'ZERO_RESULTS') {
            throw new Exception('Distance can not be zero', 404);
        }
    }

    private function formatForTwoWaypoints($waypoints, $mode)
    {
        $origin = $waypoints[0];
        $destination = $waypoints[count($waypoints) - 1];

        // Construir a string de consulta
        return http_build_query([
            'mode' => $mode,
            'origin' => $origin['lat'] . ',' . $origin['lng'],
            'destination' => $destination['lat'] . ',' . $destination['lng'],
        ]);
    }

    private function formatForMoreThatTwoWaypointsOptimize($waypoints, $optimize, $mode)
    {
        $search = $this->formatForTwoWaypoints($waypoints, $mode);
        // Adicionar pontos intermediários, se houver mais de dois
        $intermediateWaypoints = array_slice($waypoints, 1, -1);
        $intermediatePoints = [];
        foreach ($intermediateWaypoints as $waypoint) {
            $intermediatePoints[] = $waypoint['lat'] . ',' . $waypoint['lng'];
        }
        $search .= '&waypoints=optimize:' .  $optimize . '|' . implode('|', $intermediatePoints);
        return $search;
    }
}
