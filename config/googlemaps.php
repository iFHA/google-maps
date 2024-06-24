<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'key' => env('GOOGLE_MAPS_KEY'),
    'geocoding_url' => 'https://maps.googleapis.com/maps/api/geocode/json?',
    'places_url' => 'https://maps.googleapis.com/maps/api/place/textsearch/json?',
    'autocomplete_url' => 'https://maps.googleapis.com/maps/api/place/autocomplete/json?',
    'directions_url' => 'https://maps.googleapis.com/maps/api/directions/json?',
    'routes_url' => 'https://maps.googleapis.com/maps/api/directions/json?'
];
