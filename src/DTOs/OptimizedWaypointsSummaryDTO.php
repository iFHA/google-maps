<?php
namespace BeeDelivery\GoogleMaps\DTOs;

class OptimizedWaypointsSummaryDTO
{
    public function __construct(
        public readonly int $distanceInMeters,
        public readonly float $distanceInKilometers,
        public readonly int $durationInSeconds,
        public readonly float $durationInMinutes,
    ) {
    }
}
