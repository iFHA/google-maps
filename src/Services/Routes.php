<?php

namespace BeeDelivery\GoogleMaps\Services;

use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersPlaces;

class Routes
{
    use HelpersPlaces;

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
    public function routes($data)
    {
        //TODO: in progress
    }
}
