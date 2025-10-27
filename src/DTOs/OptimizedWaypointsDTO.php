<?php
namespace BeeDelivery\GoogleMaps\DTOs;

use BeeDelivery\GoogleMaps\Enums\WaypointsOptimizerType;

class OptimizedWaypointsDTO
{
    public function __construct(
        public readonly int $distanceInMeters,
        public readonly float $distanceInKilometers,
        public readonly int $durationInSeconds,
        public readonly float $durationInMinutes,
        public readonly WaypointsOptimizerType $optimizationType,
        public readonly array $intermediateWaypointsOrder,
    ) {
    }
}
