<?php
namespace BeeDelivery\GoogleMaps\Exceptions;

use RuntimeException;

class RoutesException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
