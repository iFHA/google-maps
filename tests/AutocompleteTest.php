<?php

namespace Tests;

use BeeDelivery\GoogleMaps\Services\Autocomplete;

class AutocompleteTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $autocomplete = new Autocomplete();
        $searchText = 'Bee Delivery';
        $searchText2 = "Rua da Aurora, Recife, PE, Brasil";
        $originLat = '-7.221944';
        $originLng = '-35.873056';
        $radius = 1000000;
        dd($autocomplete->query($searchText, $originLat, $originLng, $radius), $autocomplete->query($searchText2));
        // dd($geoCode->directions($waypoints, false, 'driving'));
    }
}