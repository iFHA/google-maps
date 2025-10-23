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
    'waypoints_optimizer_by_distance_preferred_api' => env('WAYPOINTS_OPTIMIZER_BY_DISTANCE_PREFERRED_API', 'route_matrix'),
    'route_optimization_api' => [
        'url' => 'https://routeoptimization.googleapis.com/v1/projects/:projectId:optimizeTours',
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'service_account_credentials' => [
            'type' => 'service_account',
            'project_id' => env('GOOGLE_MAPS_PROJECT_ID'),
            'private_key_id' => env('GOOGLE_MAPS_ROUTE_OPTIMIZATION_PRIVATE_KEY_ID'),
            'private_key' => env('GOOGLE_MAPS_ROUTE_OPTIMIZATION_PRIVATE_KEY'),
            'client_email' => env('GOOGLE_MAPS_ROUTE_OPTIMIZATION_CLIENT_EMAIL'),
            'client_id' => env('GOOGLE_MAPS_ROUTE_OPTIMIZATION_CLIENT_ID'),
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => env('GOOGLE_MAPS_ROUTE_OPTIMIZATION_CLIENT_X509_CERT_URL'),
            'universe_domain' => 'googleapis.com'
        ]
    ]

];
