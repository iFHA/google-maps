<?php

namespace BeeDelivery\GoogleMaps\Facades;

use Illuminate\Support\Facades\Facade;

class GoogleMaps extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'googlemaps';
    }
}