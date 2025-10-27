<?php

namespace BeeDelivery\GoogleMaps\Utils;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsSummaryDTO;
use BeeDelivery\GoogleMaps\DTOs\RouteMatrixResponseDTO;
use BeeDelivery\GoogleMaps\Enums\WaypointsOptimizerType;
use BeeDelivery\GoogleMaps\Exceptions\RouteMatrixException;

class RouteMatrixDistanceOptimizerByTSP
{
    /**
     * The maximum number of waypoints allowed by the route matrix API
     * @var int
     */
    const MAX_WAYPOINTS_COUNT_ALLOWED = 25;
    /**
     * The map of the route matrix (origin => destinations sorted by distance)
     * @var array<int, array<RouteMatrixResponseDTO>>
     */
    private array $map = [];
    /**
     * The visited indexes by the algorithm
     * @var array<int>
     */
    private array $visitedIndexes = [];
    /**
     * The route matrix
     * @var array<RouteMatrixResponseDTO>
     */
    private array $routeMatrix;
    /**
     * The index of the destination waypoint
     * @var int
     */
    private int $destinationWaypointIndex;
    /**
     * The optimal intermediate waypoints ordered by distance
     * @var array<RouteMatrixResponseDTO>
     */
    private array $optimalIntermediateWaypointsOrderByDistance = [];
    
    /**
     * Construct the RouteMatrixDistanceOptimizerByTSP object
     * @param array<RouteMatrixResponseDTO> $routeMatrix the route matrix
     * @throws RouteMatrixException when the route matrix is empty
     */
    public function __construct(array $routeMatrix)
    {
        $this->routeMatrix = $routeMatrix;
        $this->initializeMap();
        $this->destinationWaypointIndex = count($this->map) - 1;
    }

    /**
     * Initialize the map
     * @throws RouteMatrixException when the route matrix is empty
     * @return void
     */
    private function initializeMap(): void
    {
        if (empty($this->routeMatrix)) {
            throw new RouteMatrixException('The route matrix is empty');
        }
        for ($i = 0; $i < count($this->routeMatrix); $i++) {
            $this->add($this->routeMatrix[$i]);
        }
    }
    
    /**
     * Add a destination for a specific origin
     * @param RouteMatrixResponseDTO $routeMatrixRow
     * @return void
     */
    private function add(RouteMatrixResponseDTO $routeMatrixRow): void
    {
        if ($routeMatrixRow->distanceMeters <= 0) {
            return;
        }
        
        $originIndex = $routeMatrixRow->originIndex;
        $this->map[$originIndex][] = $routeMatrixRow;

        usort($this->map[$originIndex], fn(RouteMatrixResponseDTO $a, RouteMatrixResponseDTO $b) => $a->distanceMeters <=> $b->distanceMeters);
    }

    /**
     * Get all destinations for a specific origin
     * @param int $originIndex
     * @return array<RouteMatrixResponseDTO>
     */
    public function getDestinations(int $originIndex): array
    {
        return $this->map[$originIndex] ?? [];
    }
    
    /**
     * Get the destination data for a specific origin and destination index
     * @param int $originIndex
     * @param int $destinationIndex
     * @return RouteMatrixResponseDTO
     * @throws RouteMatrixException when the destination is not found
     */
    public function getDestination(int $originIndex, int $destinationIndex): RouteMatrixResponseDTO
    {
        $destination = array_filter(
            $this->getDestinations($originIndex),
            fn(RouteMatrixResponseDTO $destination) => $destination->destinationIndex === $destinationIndex);
        return array_values(array: $destination)[0] ??
            throw new RouteMatrixException('Destination not found for origin index: ' . $originIndex . ' and destination index: ' . $destinationIndex);
    }
    
    /**
     * Calculate the optimal intermediate waypoints ordered by distance
     * @throws RouteMatrixException when the maximum number of iterations is reached in case the algorithm gets stuck in a loop
     * @return void
     */
    private function calculateOptimalIntermediateWaypointsOrderByDistance(): void
    {
        $this->visitedIndexes = [];
        $index = 0;
        $intermediateWaypointsCount = $this->destinationWaypointIndex - 1;
        $maxIterations = self::MAX_WAYPOINTS_COUNT_ALLOWED;
        while (count($this->optimalIntermediateWaypointsOrderByDistance) < $intermediateWaypointsCount) {
            $closestWaypoint = $this->getClosestWaypointTo($index);
            $this->visitedIndexes[] = $index;
            $index = $closestWaypoint->destinationIndex;
            $this->optimalIntermediateWaypointsOrderByDistance[] = $closestWaypoint;
            $maxIterations--;
            if ($maxIterations < 0) {
                // just in case the algorithm gets stuck in a loop
                throw new RouteMatrixException('Max iterations reached while calculating optimal intermediate waypoints by distance');
            }
        }
    }
    
    /**
     * Get the closest waypoint to the index
     * @param int $index
     * @return RouteMatrixResponseDTO
     * @throws RouteMatrixException when no closest waypoint is found
     */
    private function getClosestWaypointTo(int $index): RouteMatrixResponseDTO
    {
        $destinations = $this->getDestinations($index);
        $destinations = array_filter($destinations, function (RouteMatrixResponseDTO $destination) {
            $wasNotVisitedYet = !in_array($destination->destinationIndex, $this->visitedIndexes);
            $isNotDestinationWaypoint = $destination->originIndex !== $this->destinationWaypointIndex;
            $doesNotPointToDestinationWaypoint = $destination->destinationIndex !== $this->destinationWaypointIndex;
            return $wasNotVisitedYet && $isNotDestinationWaypoint && $doesNotPointToDestinationWaypoint;
        });
        return array_values($destinations)[0] ??
            throw new RouteMatrixException('No closest waypoint found for index: ' . $index);
    }
    
    
    /**
     * Get the optimal intermediate waypoints ordered by distance (calculated if not already calculated)
     * @return array<RouteMatrixResponseDTO>
     */
    public function getOptimalIntermediateWaypointsOrderByDistance(): array
    {
        if(empty($this->optimalIntermediateWaypointsOrderByDistance)) {
            $this->calculateOptimalIntermediateWaypointsOrderByDistance();
        }
        return $this->optimalIntermediateWaypointsOrderByDistance;
    }
    
    /**
     * Get the optimized waypoints summary
     * @return OptimizedWaypointsSummaryDTO
     */
    public function getOptimizedWaypointsSummary(): OptimizedWaypointsSummaryDTO
    {
        $optimizedIntermediateWaypointsOrderByDistance = $this->getOptimalIntermediateWaypointsOrderByDistance();
        $distanceInMeters = 0;
        $durationInSeconds = 0;
        
        foreach ($optimizedIntermediateWaypointsOrderByDistance as $intermediateWaypoint) {
            $distanceInMeters += $intermediateWaypoint->distanceMeters;
            $durationInSeconds += $intermediateWaypoint->durationInSeconds;
        }
        
        $lastIntermediateWaypointIndex = $optimizedIntermediateWaypointsOrderByDistance[count($optimizedIntermediateWaypointsOrderByDistance) - 1]->destinationIndex;
        
        $dataFromBetweenLastIntermediateWaypointAndDestination = $this->getDestination($lastIntermediateWaypointIndex, $this->destinationWaypointIndex);
        
        $distanceInMeters += $dataFromBetweenLastIntermediateWaypointAndDestination->distanceMeters;
        $durationInSeconds += $dataFromBetweenLastIntermediateWaypointAndDestination->durationInSeconds;
        
        $distanceInKilometers = round($distanceInMeters / 1000, 2);
        $durationInMinutes = round($durationInSeconds / 60, 2);
        
        return new OptimizedWaypointsSummaryDTO(
            distanceInMeters: $distanceInMeters,
            distanceInKilometers: $distanceInKilometers,
            durationInSeconds: $durationInSeconds,
            durationInMinutes: $durationInMinutes,
        );
    }
    
    /**
     * Convert the optimized waypoints order to an indexed array (0-based index)
     * @return array<int>
     */
    private function optimizedWaypointsOrderToIndexedArray(): array
    {
        return array_map(function (RouteMatrixResponseDTO $waypoint) {
            $destinationIndex = $waypoint->destinationIndex;
            if($destinationIndex <= 0) {
                throw new RouteMatrixException('Destination index can not be less than or equal to 0');
            }
            return $destinationIndex - 1;
        }, $this->getOptimalIntermediateWaypointsOrderByDistance());
    }
    
    /**
     * Build the optimized waypoints DTO
     * @return OptimizedWaypointsDTO
     */
    public function buildOptimizedWaypointsDTO(): OptimizedWaypointsDTO
    {
        $summary = $this->getOptimizedWaypointsSummary();
        return new OptimizedWaypointsDTO(
            distanceInMeters: $summary->distanceInMeters,
            distanceInKilometers: $summary->distanceInKilometers,
            durationInSeconds: $summary->durationInSeconds,
            durationInMinutes: $summary->durationInMinutes,
            optimizationType: WaypointsOptimizerType::MIN_DISTANCE,
            intermediateWaypointsOrder: $this->optimizedWaypointsOrderToIndexedArray(),
        );
    }
}
