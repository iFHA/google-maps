<?php
namespace BeeDelivery\GoogleMaps\Services\Impl;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\OptimizeWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\WaypointDTO;
use BeeDelivery\GoogleMaps\Enums\WaypointsOptimizerType;
use BeeDelivery\GoogleMaps\Services\Contracts\WaypointsOptimizer;
use BeeDelivery\GoogleMaps\Services\Routes;
use Exception;

class WaypointsOptimizerByTravelTime implements WaypointsOptimizer
{
    private Routes $routes;
    public function __construct()
    {
        $this->routes = new Routes();
    }
    /**
     * Optimize the waypoints by travel time
     * @param OptimizeWaypointsDTO $optimizeWaypointsDTO
     * @throws Exception
     * @return OptimizedWaypointsDTO
     */
    public function optimize(OptimizeWaypointsDTO $optimizeWaypointsDTO): OptimizedWaypointsDTO
    {
        $response = $this->routes->routes(
            $optimizeWaypointsDTO->origin->toRoutesApiArray(),
            $optimizeWaypointsDTO->destination->toRoutesApiArray(),
            array_map(fn (WaypointDTO $waypoint) => $waypoint->toRoutesApiArray(), $optimizeWaypointsDTO->intermediateWaypoints),
        );
        
        if (isset($response['code'])) {
            throw new Exception("Failed to optimize waypoints by travel time,
            code: {$response['code']}, message: {$response['response']}", $response['code']);
        }
        
        return new OptimizedWaypointsDTO(
            distanceInMeters: $response['distance'],
            distanceInKilometers: $response['distance_in_quilometers'],
            durationInSeconds: $response['duration_in_seconds'],
            durationInMinutes: $response['duration_in_minutes'],
            optimizationType: WaypointsOptimizerType::MIN_TRAVEL_TIME,
            intermediateWaypointsOrder: $response['waypoint_order'],
        );
    }
}
