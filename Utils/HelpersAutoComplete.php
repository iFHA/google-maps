<?php

namespace BeeDelivery\GoogleMaps\Utils;

use Exception;

trait HelpersAutoComplete
{
    public function formatRequest($searchText, $originLat, $originLng, $radius)
    {
        if ($originLat != '' && $originLng != '') {
            $searchUrl = http_build_query([
                'strictbounds' => 'true',
                'location' => $originLat . ',' . $originLng,
                'radius' => $radius,
                'language' => 'pt',
                'input' => $searchText
            ]);
        } else {
            $searchUrl = http_build_query([
                'input' => $searchText,
                'language' => 'pt',
            ]);
        }
       
        return config('googlemaps.autocomplete_url') . $searchUrl;
    }

    public function formatResponse($response)
    {
        foreach ($response['predictions'] as $item) {
            if (isset($item) && isset($item['place_id']) && isset($item['description'])) {
                $predictions['predictions'][] = [
                    'description' => $item['description'],
                    'placeId' => $item['place_id'],
                    'is_establishment' => in_array('establishment', $item['types']),
                ];
            }
        }
        return $predictions ?? [];
    }
}
