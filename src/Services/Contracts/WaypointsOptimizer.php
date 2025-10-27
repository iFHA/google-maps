<?php

namespace BeeDelivery\GoogleMaps\Services\Contracts;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\OptimizeWaypointsDTO;
use Exception;

interface WaypointsOptimizer
{
    /**
     * Optimize the waypoints
     * @param OptimizeWaypointsDTO $optimizeWaypointsDTO waypoints to optimize
     * @return OptimizedWaypointsDTO optimized waypoints
     * @throws Exception
     */
    public function optimize(OptimizeWaypointsDTO $optimizeWaypointsDTO): OptimizedWaypointsDTO;
}