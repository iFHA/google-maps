<?php
namespace BeeDelivery\GoogleMaps\Enums;

enum RouteOptimizationTypeEnum: string
{
    case MIN_DISTANCE = 'MIN_DISTANCE';
    case MIN_TRAVEL_TIME = 'MIN_TRAVEL_TIME';
    
    public function toOptimizerWaypointsType(): WaypointsOptimizerType
    {
        return match ($this) {
            self::MIN_DISTANCE => WaypointsOptimizerType::MIN_DISTANCE,
            self::MIN_TRAVEL_TIME => WaypointsOptimizerType::MIN_TRAVEL_TIME,
        };
    }
}
