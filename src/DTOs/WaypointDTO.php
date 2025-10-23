<?php
namespace BeeDelivery\GoogleMaps\DTOs;

class WaypointDTO
{
    public function __construct(
        public readonly string $latitude,
        public readonly string $longitude,
    ) {
    }
    
    /**
     * Convert the WaypointDTO to an array compatible with the Routes API Service
     * @return array{lat: string, lng: string}
     */
    public function toRoutesApiArray(): array
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude,
        ];
    }
}
