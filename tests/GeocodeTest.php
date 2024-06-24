<?php

namespace Tests;

use BeeDelivery\GoogleMaps\Services\Geocoding;

class GeocodeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $geoCode = new Geocoding();

        dd($geoCode->geocode('Rua 13 de Maio, 47 - Centro, Rio de Janeiro - RJ, 20031-007', 'address', 'Rio de Janeiro', 'city'));
    }
}