<?php
namespace BeeDelivery\GoogleMaps\Enums;

enum RouteTravelModeEnum: string
{
    case DRIVE = 'DRIVE';
    case WALK = 'WALK';
    case BICYCLE = 'BICYCLE';
    case TRANSIT = 'TRANSIT';
    case TWO_WHEELER = 'TWO_WHEELER';
    case TRAVEL_MODE_UNSPECIFIED = 'TRAVEL_MODE_UNSPECIFIED';
}
