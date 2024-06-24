<?php

namespace BeeDelivery\GoogleMaps;

use BeeDelivery\GoogleMaps\Services\Geocoding;

class GoogleMaps
{
    public function geocode()
    {
        return new Geocoding();
    }

    public function places()
    {
        return new Places();
    }

    public function routes()
    {
        return new Routes();
    }
}
