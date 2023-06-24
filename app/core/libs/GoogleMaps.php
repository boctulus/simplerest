<?php

namespace simplerest\core\libs;

class GoogleMaps
{
    protected $api_key;

    public function __construct(?string $api_key = null)
    {
        if ($api_key === null){
            $api_key = config()['google_maps_api_key'];
        } 

        $this->api_key = $api_key;
    }

    function getCoordinates(string $address)
    {   
        $allow_url_open = Files::isAllowUrlFopenEnabled();
        $curl_available = Files::isCurlAvailable();

        if (!$allow_url_open && !$curl_available){
            throw new \Exception("No way to get url contents");
        }

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address) . '&sensor=false&key=' . $this->api_key;

        if ($allow_url_open){
            $geo = file_get_contents($url);
            $geo = json_decode($geo, true); // JSON to an array
        } else {
            $client = new ApiClient($url);

            $geo_res = $client
            ->disableSSL()
            ->get();
    
            if ($geo_res->getStatus() != 200){
                return null;
            }
    
            $geo = $geo_res->getResponse()['data'];    
        }

        if (isset($geo['status']) && ($geo['status'] == 'OK')) {
            $lat = $geo['results'][0]['geometry']['location']['lat']; // Latitude
            $lon = $geo['results'][0]['geometry']['location']['lng']; // Longitude

            return [
                'lat' => $lat,
                'lon' => $lon
            ];
        }
    }


}

