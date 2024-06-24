<?php

namespace BeeDelivery\GoogleMaps\Services;

use BeeDelivery\GoogleMaps\Utils\Connection;
use BeeDelivery\GoogleMaps\Utils\HelpersGeoCoding;

class Geocoding
{
    use HelpersGeoCoding;

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
    public function geocode($search, $type = 'place_id', $restrictions = null, $restrictionType = null)
    {
        try {
            return $this->formatResponse($this->http->get($this->formatRequest($search, $type, $restrictions, $restrictionType)));
        } catch (\Exception $e) {
            return [
                'code' => $e->getCode(),
                'response' => $e->getMessage()
            ];
        }
    }
}
