<?php

namespace BeeDelivery\GoogleMaps\Utils;

trait HelpersPlaces
{
    public function url()
    {
        return config('googlemaps.places_url');
    }

    public function formatFieldMask(bool $displayName): string
    {
        return 'places.formattedAddress,places.priceLevel,places.location' . ($displayName ? ',places.displayName' : '');
    }

    public function formatRequest(string $textQuery, string $regionCode = 'br'): array
    {
        return [
            'textQuery' => $textQuery,
            'regionCode' => $regionCode,
        ];
    }

    public function formatResponse(array $response): array
    {
        if (isset($response['error'])) {
            return [
                'code' => $response['error']['code'],
                'message' => $response['error']['message']
            ];
        }


        $addresses = ['addresses' => []];

        foreach ($response['places'] as $result) {
            $addresses['addresses'][] = $this->mapAddressComponents($result);
        }

        return $addresses;
    }

    private function mapAddressComponents(array $result): array
    {
        $componentsMap = [
            'formattedAddress' => $result['formattedAddress'],
            'lat' => $result['location']['latitude'],
            'lng' => $result['location']['longitude'],
        ];

        if (isset($result['displayName'])) {
            $componentsMap['displayName'] = $result['displayName']['text'];
        }

        return $componentsMap;
    }
}
