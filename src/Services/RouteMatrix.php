<?php
namespace BeeDelivery\GoogleMaps\Services;

use BeeDelivery\GoogleMaps\DTOs\RouteMatrixResponseDTO;
use BeeDelivery\GoogleMaps\DTOs\WaypointDTO;
use BeeDelivery\GoogleMaps\Enums\RouteTravelModeEnum;
use BeeDelivery\GoogleMaps\Exceptions\RouteMatrixException;
use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersRouteMatrix;
class RouteMatrix
{
    use HelpersRouteMatrix;
    
    private Connection $http;
    
    public function __construct()
    {
        $this->http = new Connection();
    }
    
    /**
     * Get the route matrix from the Google Maps API
     * @param array<WaypointDTO> $origins
     * @param array<WaypointDTO> $destinations
     * @param RouteTravelModeEnum $mode
     * @return array<RouteMatrixResponseDTO>
     * @throws RouteMatrixException when the response is null or the response has an error
     */
    public function getRouteMatrix(array $origins, array $destinations, RouteTravelModeEnum $mode = RouteTravelModeEnum::DRIVE): array
    {
        $response = $this->http->post(
            $this->url(),
            $this->formatRequest($origins, $destinations, $mode),
            $this->formatFieldMask()
        );
        if(is_null($response) || ($response['error'] ?? false)) {
            throw new RouteMatrixException($response['response'] ?? 'Error on getting route matrix');
        }
        return array_map(fn (array $response) => RouteMatrixResponseDTO::fromResponse($response), $response);
    }
}
