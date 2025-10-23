<?php
namespace BeeDelivery\GoogleMaps\Enums;

enum RouteOptimizationTypeEnum: string
{
    case MIN_DISTANCE = 'MIN_DISTANCE';
    case MIN_TRAVEL_TIME = 'MIN_TRAVEL_TIME';
}
