<?php
namespace BeeDelivery\GoogleMaps\DTOs;

class WaypointDTO
{
    public function __construct(
        public readonly string $latitude,
        public readonly string $longitude,
    ) {
    }
}
