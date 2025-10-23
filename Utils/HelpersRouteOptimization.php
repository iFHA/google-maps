<?php
namespace BeeDelivery\GoogleMaps\Utils;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\OptimizeWaypointsDTO;
use BeeDelivery\GoogleMaps\Enums\RouteOptimizationTypeEnum;
use Exception;

trait HelpersRouteOptimization
{
    public function url()
    {
        $url = config('googlemaps.route_optimization_api.url');
        $projectId = config('googlemaps.route_optimization_api.service_account_credentials.project_id');
        
        if(empty($url)) {
            throw new Exception('Route optimization API URL is not set', 500);
        }
        
        if(empty($projectId)) {
            throw new Exception('Route optimization API project ID is not set', 500);
        }

        return str_replace(':projectId', $projectId, $url);
    }

    public function formatRequest(OptimizeWaypointsDTO $optimizeWaypointsDTO): array
    {
        if (count($optimizeWaypointsDTO->intermediateWaypoints) < 2) {
            throw new Exception('You must provide at least two waypoints');
        }

        $dataSearch = [
            "model" => [
                "vehicles" => [
                    [
                        "startLocation" => [
                            'latitude' => $optimizeWaypointsDTO->origin->latitude,
                            "longitude" => $optimizeWaypointsDTO->origin->longitude
                        ],
                        "endLocation" =>   [
                            'latitude' => $optimizeWaypointsDTO->destination->latitude,
                            "longitude" => $optimizeWaypointsDTO->destination->longitude
                        ],
                        "costPerKilometer" => 1
                    ]
                ],
                'shipments' => []
            ],
            "searchMode" => "SEARCH_MODE_UNSPECIFIED",
            "considerRoadTraffic" => false
        ];

        if ($this->type === RouteOptimizationTypeEnum::MIN_TRAVEL_TIME) {
            unset($dataSearch['model']['vehicles'][0]['costPerKilometer']);
            $dataSearch['model']['vehicles'][0]['costPerHour'] = 1;
        }

        foreach ($optimizeWaypointsDTO->intermediateWaypoints as $waypoint) {
            $dataSearch['model']['shipments'][] = [
                'deliveries' => [
                    'arrivalLocation' => [
                        'latitude' => $waypoint->latitude,
                        'longitude' => $waypoint->longitude
                    ]
                ]
            ];
        }

        return $dataSearch;
    }

    /**
     * Format the response from the API to an OptimizedWaypointsDTO object
     * @param array $response the response from the API
     * @throws \Exception
     * @return OptimizedWaypointsDTO
     */
    public function formatResponse(array $response): OptimizedWaypointsDTO
    {
        if ($response['error'] ?? false) {
            throw new Exception($response['response'], $response['code']);
        }

        $aggregatedRouteMetrics = $response['metrics']['aggregatedRouteMetrics'] ?? [];
        $distanceInMeters = (int) $aggregatedRouteMetrics['travelDistanceMeters'] ?? 0;
        $durationInSeconds = (int) $aggregatedRouteMetrics['travelDuration'] ?? 0;

        if ($distanceInMeters == 0) {
            throw new Exception('Distance can not be zero', 404);
        }

        $distanceInKilometers = round(($distanceInMeters / 1000), 2);
        $durationInMinutes = round(($durationInSeconds / 60), 2);

        return new OptimizedWaypointsDTO(
            distanceInMeters: $distanceInMeters,
            distanceInKilometers: $distanceInKilometers,
            durationInSeconds: $durationInSeconds,
            durationInMinutes: $durationInMinutes,
            optimizationType: $this->type,
            intermediateWaypointsOrder: $this->extractIntermediateWaypointOrderFromResponse($response),
        );
    }

    private function extractIntermediateWaypointOrderFromResponse(array $response): array
    {
        return array_map(fn($visit) => $visit['shipmentIndex'] ?? 0, $response['routes'][0]['visits']);
    }

}
