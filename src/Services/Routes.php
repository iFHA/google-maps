<?php

namespace BeeDelivery\GoogleMaps\Services;

use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersRoutes;
    
class Routes
{
    use HelpersRoutes;

    protected $http;

    /*
     * Create a new Connection instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->http = new Connection();
    }

    /*     
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function routes(array $waypoints, bool $optimize =  true, string $mode = 'DRIVE'): array
    {
        try {
            return $this->formatResponse(
                $this->http->post(
                    $this->url(),
                    $this->formatRequest($waypoints, $optimize, $mode),
                    $this->formatFieldMask($optimize)
                )
            );
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'response' => $e->getMessage()
            ];
        }
    }
}
