<?php

namespace simplerest\libs;

use simplerest\core\libs\ApiClient;

class WPAmelia extends ApiClient
{
    protected $base_url;
    protected $api_key;

    function __construct($base_url = null, $api_key = null){
        $this->base_url = $base_url ?? env('AMELIA_API_URL');
        $this->api_key  = $api_key  ?? env('AMELIA_API_KEY');

        $this
        ->withoutStrictSSL()
        ->followLocations()
        ->setHeaders([
            "Content-type"  => "application/json",
            "Amelia" => $this->api_key
        ])
        ->decode();
    }

    /*
        Endpoints
    */
    function setCouponEndpoint(){
        $this->setUrl("{$this->base_url}/coupons");
        return $this;
    }
}

