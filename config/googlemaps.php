<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'key' => env('GOOGLE_MAPS_KEY'),
    'geocoding_url' => 'https://maps.googleapis.com/maps/api/geocode/json?',
    'places_url' => 'https://places.googleapis.com/v1/places:searchText',
    'autocomplete_url' => 'https://places.googleapis.com/v1/places:autocomplete',
    'directions_url' => 'https://routes.googleapis.com/directions/v2:computeRoutes',
    'routes_url' => 'https://routes.googleapis.com/directions/v2:computeRoutes',
    'distance_matrix_url' => 'https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix',
];
