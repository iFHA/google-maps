<?php

namespace BeeDelivery\GoogleMaps\Services;

use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersDirections;

class Directions
{
    use HelpersDirections;

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
     */
    public function directions($waypoints, $optimize =  false, $mode = 'driving')
    {
        try {
            return $this->formatResponse($this->http->get($this->formatRequest($waypoints, $optimize, $mode)));
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'response' => $e->getMessage()
            ];
        }
    }
}
