<?php

namespace Tests;

use BeeDelivery\GoogleMaps\Services\Places;

class PlacesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $places = new Places();
        $searchText = 'rua manoel franklin, 3848';
        dd(
            $places->places($searchText, true),
            $places->places($searchText)
        );
    }
}