<?php

namespace BeeDelivery\GoogleMaps\Services;

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
    public function places(string $textQuery, bool $displayName = false, string $regionCode = 'br'): array
    {
        try {
            if ($regionCode == '') {
                throw new \Exception('The region code is required');
            }

            return $this->formatResponse(
                $this->http->post(
                    $this->url(),
                    $this->formatRequest($textQuery, $regionCode),
                    $this->formatFieldMask($displayName)
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
