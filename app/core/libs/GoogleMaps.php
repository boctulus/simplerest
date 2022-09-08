<?php

namespace simplerest\core\libs;

use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

class GoogleMaps
{
    protected $api_key;

    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;
    }

    function getCoordiantes(string $address) {
        // Get JSON results from this request
        $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address) . '&sensor=false&key=' . $this->api_key);

        $geo = json_decode($geo, true); // Convert the JSON to an array

        if (isset($geo['status']) && ($geo['status'] == 'OK')) {
            $lat = $geo['results'][0]['geometry']['location']['lat']; // Latitude
            $lon = $geo['results'][0]['geometry']['location']['lng']; // Longitude

            return ([
                'lat' => $lat,
                'lon' => $lon
            ]);
        } else {
            return null;
        }
     }


}

