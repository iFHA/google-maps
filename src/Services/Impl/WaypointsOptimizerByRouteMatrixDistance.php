<?php

namespace BeeDelivery\GoogleMaps\Services\Impl;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\RouteMatrixResponseDTO;
use BeeDelivery\GoogleMaps\DTOs\WaypointDTO;
use BeeDelivery\GoogleMaps\Enums\RouteTravelModeEnum;
use BeeDelivery\GoogleMaps\Services\Contracts\WaypointsOptimizer;
use BeeDelivery\GoogleMaps\Services\RouteMatrix;

class WaypointsOptimizerByRouteMatrixDistance implements WaypointsOptimizer
{
    private RouteMatrix $routeMatrix;
    public function __construct()
    {
        $this->routeMatrix = new RouteMatrix();
    }

    /**
     * Summary of optimizeWaypointsByDistance
     * @param WaypointDTO $origin
     * @param WaypointDTO $destination
     * @param array<WaypointDTO> $waypoints
     * @param RouteTravelModeEnum $mode
     * @return array
     */
    public function optimize(WaypointDTO $origin, WaypointDTO $destination, array $waypoints = [], RouteTravelModeEnum $mode = RouteTravelModeEnum::DRIVE): OptimizedWaypointsDTO
    {
        $origins = $destinations = [$origin, ...$waypoints, $destination];
        $routeMatrix = $this->routeMatrix->getRouteMatrix($origins, $destinations, $mode);
        return $this->getOptimizedWaypointsByDistanceFromRouteMatrix($routeMatrix);
    }

    /**
     * Get the optimized waypoints by distance from the route matrix using traveling salesman problem algorithm ordered by distance
     * @param array<RouteMatrixResponseDTO> $routeMatrix
     * @return OptimizedWaypointsDTO
     * @throws Exception when the route matrix response is null or the response code is not 200
     */
    private function getOptimizedWaypointsByDistanceFromRouteMatrix(array $routeMatrix): OptimizedWaypointsDTO
    {
        $optimizedWaypointsOrdered = [];
        $index = $distanceInMeters = $durationInSeconds = 0;
        $visitedIndexes = [];
        while (count($visitedIndexes) < count($routeMatrix)) {
            $closestWaypoint = $this->getClosestWaypointTo($index, $routeMatrix, $visitedIndexes);
            $distanceInMeters += $closestWaypoint->distanceMeters;
            $durationInSeconds += $closestWaypoint->durationInSeconds;
            $visitedIndexes[] = $index;
            $index = $closestWaypoint->destinationIndex;
            $optimizedWaypointsOrdered[] = $index;
        }
        
        $distanceInKilometers = round($distanceInMeters / 1000, 2);
        $durationInMinutes = round($durationInSeconds / 60, 2);
        
        return new OptimizedWaypointsDTO(
            $distanceInMeters,
            $distanceInKilometers,
            $durationInSeconds,
            $durationInMinutes,
            $optimizedWaypointsOrdered
        );
    }
    
    private function getClosestWaypointTo(int $originWaypointIndex, array $routeMatrix, array &$visitedIndexes): RouteMatrixResponseDTO
    {
        $closest = array_reduce($routeMatrix, function ($carry, $item) use ($originWaypointIndex, $visitedIndexes) {
            $isFirstElement = $carry === null;
            $isOriginWaypoint = $item['originIndex'] === $originWaypointIndex;
            $wasNotVisitedYet = !in_array($item['destinationIndex'], $visitedIndexes);
            $isNotSameAsSource = $item['destinationIndex'] !== $originWaypointIndex;
            $isCloser = $item['distanceMeters'] < $carry['distanceMeters'];
            if (
                $isFirstElement ||
                (
                    $isOriginWaypoint  &&
                    $isNotSameAsSource &&
                    $wasNotVisitedYet &&
                    $isCloser
                )
            ) {
                return $item;
            }
            return $carry;
        });
        return RouteMatrixResponseDTO::fromResponse($closest);
    }
    
}
