<?php

namespace BeeDelivery\GoogleMaps;

use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersPlaces;

class Places
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
    public function places($data)
    {
        //TODO: in progress
    }
}
