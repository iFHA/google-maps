<?php
namespace BeeDelivery\GoogleMaps\Exceptions;

use RuntimeException;

class RouteOptimizationException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}