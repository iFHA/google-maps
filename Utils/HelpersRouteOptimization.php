<?php
namespace BeeDelivery\GoogleMaps\Utils;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\OptimizeWaypointsDTO;
use BeeDelivery\GoogleMaps\Enums\RouteOptimizationTypeEnum;
use BeeDelivery\GoogleMaps\Exceptions\RouteOptimizationException;
use Illuminate\Support\Arr;

trait HelpersRouteOptimization
{
    
    /**
     * Get the URL for the route optimization API
     * @return string
     * @throws RouteOptimizationException when the URL or project ID is not set
     */
    public function url(): string
    {
        $url = config('googlemaps.route_optimization_api.url');
        $projectId = config('googlemaps.route_optimization_api.service_account_credentials.project_id');
        
        if(empty($url)) {
            throw new RouteOptimizationException('Route optimization API URL is not set');
        }
        
        if(empty($projectId)) {
            throw new RouteOptimizationException('Route optimization API project ID is not set');
        }

        return str_replace(':projectId', $projectId, $url);
    }

    public function formatRequest(OptimizeWaypointsDTO $optimizeWaypointsDTO): array
    {
        if (count($optimizeWaypointsDTO->intermediateWaypoints) < 1) {
            throw new RouteOptimizationException('You must provide at least one intermediate waypoint(3 including origin and destination)');
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
        
        if (!isset($this->type)) {
            throw new RouteOptimizationException('Route optimization type must be defined before calling formatRequest()');
        }

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
     * @throws RouteOptimizationException when the response has an error
     * @return OptimizedWaypointsDTO
     */
    public function formatResponse(array $response): OptimizedWaypointsDTO
    {
        if ($response['error'] ?? false) {
            throw new RouteOptimizationException($response['response']);
        }
        
        $aggregatedRouteMetrics = Arr::get($response, 'metrics.aggregatedRouteMetrics', []);
        $distanceInMeters = (int) $aggregatedRouteMetrics['travelDistanceMeters'] ?? 0;
        $durationInSeconds = (int) $aggregatedRouteMetrics['travelDuration'] ?? 0;

        if ($distanceInMeters == 0) {
            throw new RouteOptimizationException('Distance can not be zero');
        }

        $distanceInKilometers = round(($distanceInMeters / 1000), 2);
        $durationInMinutes = round(($durationInSeconds / 60), 2);

        return new OptimizedWaypointsDTO(
            distanceInMeters: $distanceInMeters,
            distanceInKilometers: $distanceInKilometers,
            durationInSeconds: $durationInSeconds,
            durationInMinutes: $durationInMinutes,
            optimizationType: $this->type->toOptimizerWaypointsType(),
            intermediateWaypointsOrder: $this->extractIntermediateWaypointOrderFromResponse($response),
        );
    }

    private function extractIntermediateWaypointOrderFromResponse(array $response): array
    {
        return array_map(fn($visit) => $visit['shipmentIndex'] ?? 0, $response['routes'][0]['visits']);
    }

}
