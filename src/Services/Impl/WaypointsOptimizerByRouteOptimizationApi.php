<?php

namespace BeeDelivery\GoogleMaps\Services\Impl;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\OptimizeWaypointsDTO;
use BeeDelivery\GoogleMaps\Enums\RouteOptimizationTypeEnum;
use BeeDelivery\GoogleMaps\Services\Contracts\WaypointsOptimizer;
use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersRouteOptimization;
use Exception;
use Google\Client;
    

class WaypointsOptimizerByRouteOptimizationApi implements WaypointsOptimizer
{
    use HelpersRouteOptimization;
    private Connection $http;
    private Client $apiClient;
    public function __construct(
        public readonly RouteOptimizationTypeEnum $type = RouteOptimizationTypeEnum::MIN_DISTANCE,
    )
    {
        $this->http = new Connection();
        $this->apiClient = new Client();
        $this->apiClient->setAuthConfig('googlemaps.route_optimization_api.service_account_credentials');
        $this->apiClient->addScope(config('googlemaps.route_optimization_api.scope'));
    }
    
    public function optimize(OptimizeWaypointsDTO $optimizeWaypointsDTO): OptimizedWaypointsDTO
    {
        $token = $this->apiClient
        ->fetchAccessTokenWithAssertion()['access_token'] ?? '';
        
        if (empty($token)) {
            throw new Exception('Failed to get access token for route optimization');
        }
        return $this->formatResponse(
            response: $this->http->post(
                url: $this->url(),
                data: $this->formatRequest($optimizeWaypointsDTO),
                headers: ['Authorization' => "Bearer $token"]
            ),
        );
    }

}
