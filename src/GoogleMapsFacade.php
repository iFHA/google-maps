<?php

namespace BeeDelivery\GoogleMaps;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BeeDelivery\GoogleMaps\Skeleton\SkeletonClass
 */
class GoogleMapsFacade extends Facade
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
