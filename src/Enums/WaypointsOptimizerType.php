<?php
namespace BeeDelivery\GoogleMaps\Enums;

enum WaypointsOptimizerType: string
{
    case MIN_TRAVEL_TIME = 'MIN_TRAVEL_TIME';
    case MIN_DISTANCE = 'MIN_DISTANCE';
}
