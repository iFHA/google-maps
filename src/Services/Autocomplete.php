<?php

namespace BeeDelivery\GoogleMaps\Services;

use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersAutoComplete;

class Autocomplete
{
    use HelpersAutoComplete;

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
    public function query($searchText, $originLat = '', $originLng = '', $radius = 10000)
    {
        try {
            return $this->formatResponse($this->http->get($this->formatRequest($searchText, $originLat, $originLng, $radius)));
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'response' => $e->getMessage()
            ];
        }
    }
}
