<?php
namespace BeeDelivery\GoogleMaps\DTOs;

class RouteMatrixResponseDTO
{
    public function __construct(
        public readonly int $originIndex,
        public readonly int $destinationIndex,
        public readonly int $distanceMeters,
        public readonly int $durationInSeconds,
    ) {
    }
    
    public static function fromResponse(array $response): self
    {
        return new self(
            $response['originIndex'],
            $response['destinationIndex'],
            $response['distanceMeters'] ?? 0,
            (int) $response['duration'],
        );
    }
}
