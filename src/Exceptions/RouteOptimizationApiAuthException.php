<?php

namespace BeeDelivery\GoogleMaps\Exceptions;

use RuntimeException;

class RouteOptimizationApiAuthException extends RuntimeException
{
    public function __construct(string $message = 'Failed to get access token for route optimization')
    {
        parent::__construct($message);
    }
}
