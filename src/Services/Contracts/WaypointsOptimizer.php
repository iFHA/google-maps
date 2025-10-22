<?php

namespace BeeDelivery\GoogleMaps\Services\Contracts;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\WaypointDTO;
use BeeDelivery\GoogleMaps\Enums\RouteTravelModeEnum;

interface WaypointsOptimizer
{
    /**
     * Optimize the waypoints
     * @param WaypointDTO $origin
     * @param WaypointDTO $destination
     * @param array<WaypointDTO> $waypoints
     * @param RouteTravelModeEnum $mode
     * @return OptimizedWaypointsDTO
     * @throws Exception
     */
    public function optimize(WaypointDTO $origin, WaypointDTO $destination, array $waypoints = [], RouteTravelModeEnum $mode = RouteTravelModeEnum::DRIVE): OptimizedWaypointsDTO;
}