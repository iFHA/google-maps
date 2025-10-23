<?php

namespace BeeDelivery\GoogleMaps\Services\Impl;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\OptimizeWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\RouteMatrixResponseDTO;
use BeeDelivery\GoogleMaps\DTOs\WaypointDTO;
use BeeDelivery\GoogleMaps\Enums\RouteTravelModeEnum;
use BeeDelivery\GoogleMaps\Services\Contracts\WaypointsOptimizer;
use BeeDelivery\GoogleMaps\Services\RouteMatrix;
use Exception;

class WaypointsOptimizerByRouteMatrixDistance implements WaypointsOptimizer
{
    private RouteMatrix $routeMatrix;
    private RouteTravelModeEnum $mode;
    public function __construct()
    {
        $this->routeMatrix = new RouteMatrix();
        $this->mode = RouteTravelModeEnum::DRIVE;
    }

    /**
     * Summary of optimizeWaypointsByDistance
     * @param WaypointDTO $origin
     * @param WaypointDTO $destination
     * @param array<WaypointDTO> $waypoints
     * @param RouteTravelModeEnum $mode
     * @return array
     */
    public function optimize(OptimizeWaypointsDTO $optimizeWaypointsDTO): OptimizedWaypointsDTO
    {
        $origins = $destinations = [$optimizeWaypointsDTO->origin, ...$optimizeWaypointsDTO->intermediateWaypoints, $optimizeWaypointsDTO->destination];
        $waypointsCount = count($origins);
        $destinationWaypointIndex = $waypointsCount - 1;
        if ($waypointsCount < 4) {
            throw new Exception('You must provide at least two intermediate waypoints');
        }
        if ($waypointsCount > 25) {
            throw new Exception("You must provide less than 26 intermediate waypoints including origin and destination, {$waypointsCount} were provided");
        }
        $routeMatrix = $this->routeMatrix->getRouteMatrix($origins, $destinations, $this->mode);
        $intermediateWaypointsCount = count($optimizeWaypointsDTO->intermediateWaypoints);
        return $this->getOptimizedWaypointsByDistanceFromRouteMatrix($routeMatrix, $destinationWaypointIndex, $intermediateWaypointsCount);
    }

    /**
     * Get the optimized waypoints by distance from the route matrix using traveling salesman problem algorithm ordered by distance
     * @param array<RouteMatrixResponseDTO> $routeMatrix
     * @param int $destinationWaypointIndex
     * @return OptimizedWaypointsDTO
     * @throws Exception when the route matrix response is null or the response code is not 200
     */
    private function getOptimizedWaypointsByDistanceFromRouteMatrix(array $routeMatrix, int $destinationWaypointIndex, int $intermediateWaypointsCount): OptimizedWaypointsDTO
    {
        $optimizedWaypointsOrdered = [];
        $index = $distanceInMeters = $durationInSeconds = 0;
        $visitedIndexes = [];
        while (count($optimizedWaypointsOrdered) < $intermediateWaypointsCount) {
            $closestWaypoint = $this->getClosestWaypointTo($index, $routeMatrix, $destinationWaypointIndex, $visitedIndexes);
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
    
    private function getClosestWaypointTo(int $originWaypointIndex, array $routeMatrix, int $destinationWaypointIndex, array $visitedIndexes): RouteMatrixResponseDTO
    {
        $closest = array_reduce($routeMatrix, function ($carry, RouteMatrixResponseDTO $item) use (
            $originWaypointIndex, $visitedIndexes, $destinationWaypointIndex) {
            $isFirstElement = $carry === null;
            $isOriginWaypoint = $item->originIndex === $originWaypointIndex;
            $wasNotVisitedYet = !in_array($item->destinationIndex, $visitedIndexes);
            $isNotSameAsSource = $item->destinationIndex !== $originWaypointIndex;
            $isCloser = $carry !== null && $item->distanceMeters < $carry->distanceMeters;
            $isNotDestinationWaypoint = $item->originIndex !== $destinationWaypointIndex;
            if (
                $isFirstElement ||
                (
                    $isOriginWaypoint  &&
                    $isNotSameAsSource &&
                    $wasNotVisitedYet &&
                    $isNotDestinationWaypoint &&
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
