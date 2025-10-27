<?php

namespace BeeDelivery\GoogleMaps\Services\Impl;

use BeeDelivery\GoogleMaps\DTOs\OptimizedWaypointsDTO;
use BeeDelivery\GoogleMaps\DTOs\OptimizeWaypointsDTO;
use BeeDelivery\GoogleMaps\Enums\RouteOptimizationTypeEnum;
use BeeDelivery\GoogleMaps\Exceptions\RouteOptimizationApiAuthException;
use BeeDelivery\GoogleMaps\Services\Contracts\WaypointsOptimizer;
use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersRouteOptimization;
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
        $this->initializeApiClient();
    }
    
    public function optimize(OptimizeWaypointsDTO $optimizeWaypointsDTO): OptimizedWaypointsDTO
    {
        $token = $this->apiClient
        ->fetchAccessTokenWithAssertion()['access_token'] ?? '';
        
        if (empty($token)) {
            throw new RouteOptimizationApiAuthException();
        }
        return $this->formatResponse(
            response: $this->http->post(
                url: $this->url(),
                data: $this->formatRequest($optimizeWaypointsDTO),
                headers: ['Authorization' => "Bearer $token"]
            ),
        );
    }

    private function initializeApiClient(): void
    {
        $this->apiClient = new Client();
        $authConfig = config('googlemaps.route_optimization_api.service_account_credentials');
        $scope = config('googlemaps.route_optimization_api.scope');
        if (empty($authConfig)) {
            throw new RouteOptimizationApiAuthException('Service account credentials for route optimization API not found');
        }
        if (empty($scope)) {
            throw new RouteOptimizationApiAuthException('Scope for route optimization API not found');
        }
        $this->apiClient->setAuthConfig($authConfig);
        $this->apiClient->addScope($scope);
    }

}
