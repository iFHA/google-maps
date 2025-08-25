<?php

namespace Tests;

use BeeDelivery\GoogleMaps\Services\Routes;

class RoutesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $routes = new Routes();

        $origin = ['lat' => -5.5505189, 'lng' => -37.6333084];
        $destination = ['lat' => -5.5123139, 'lng' => -37.6312312104];

        $waypoints = [
            ['lat' => -5.5505199, 'lng' => -37.6333094],            
            ['lat' => -5.5123129, 'lng' => -37.6312312094],
            ['lat' => -5.6512349, 'lng' => -37.213123],
            ['lat' => -5.5503123, 'lng' => -37.633123123],
            ['lat' => -5.5213193, 'lng' => -37.142341],
            ['lat' => -5.6512349, 'lng' => -37.213123],
            ['lat' => -5.5503123, 'lng' => -37.633123123],
            ['lat' => -5.5213193, 'lng' => -37.142341],
        ];

        dd(
            $routes->routes($origin, $destination, $waypoints, false),
            $routes->routes($origin, $destination, $waypoints)
        );
    }
}