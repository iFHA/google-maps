<?php
namespace BeeDelivery\GoogleMaps\Factories;

use BeeDelivery\GoogleMaps\Enums\WaypointsOptimizerType;
use BeeDelivery\GoogleMaps\Services\Contracts\WaypointsOptimizer;
use BeeDelivery\GoogleMaps\Services\Impl\WaypointsOptimizerByRouteMatrixDistance;
use BeeDelivery\GoogleMaps\Services\Impl\WaypointsOptimizerByRouteOptimizationApi;
use BeeDelivery\GoogleMaps\Services\Impl\WaypointsOptimizerByTravelTime;
use Exception;

class WaypointsOptimizerFactory
{
    public static function make(WaypointsOptimizerType $type = WaypointsOptimizerType::MIN_TRAVEL_TIME): WaypointsOptimizer
    {
        return match ($type) {
            WaypointsOptimizerType::MIN_TRAVEL_TIME => new WaypointsOptimizerByTravelTime(),
            WaypointsOptimizerType::MIN_DISTANCE =>
                config('googlemaps.waypoints_optimizer_by_distance_preferred_api') === 'route_matrix' ?
                new WaypointsOptimizerByRouteMatrixDistance():
                new WaypointsOptimizerByRouteOptimizationApi(),
            default => throw new Exception('Invalid waypoints optimizer type, accepted values are: MIN_TRAVEL_TIME, MIN_DISTANCE'),
        };
    }
}
