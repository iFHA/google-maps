<?php

namespace BeeDelivery\GoogleMaps;

use BeeDelivery\GoogleMaps\Services\Autocomplete;
use BeeDelivery\GoogleMaps\Services\Directions;
use BeeDelivery\GoogleMaps\Services\RouteMatrix;
use BeeDelivery\GoogleMaps\Services\Geocoding;
use BeeDelivery\GoogleMaps\Services\Places;
use BeeDelivery\GoogleMaps\Services\Routes;

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

    public function autocomplete()
    {
        return new Autocomplete();
    }

    public function directions()
    {
        return new Directions();
    }
    
    public function routeMatrix(): RouteMatrix
    {
        return new RouteMatrix();
    }
}
