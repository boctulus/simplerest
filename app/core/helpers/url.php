<?php

use simplerest\core\libs\ApiClient;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;
use simplerest\core\Model;
use simplerest\core\libs\Url;

/*
    Returns BASE_URL to be used in the FrontEnd
*/
function base_url(){
    static $base_url;

    if ($base_url !== null){
        return $base_url;
    }

    $base_url = Url::getBaseUrl();

    return $base_url;
}

function consume_api(string $url, string $http_verb, $body = null, ?Array $headers = null, ?Array $options = null, $decode = true, $encode_body = true){
    return ApiClient::instance()->consumeAPI($url, $http_verb, $body, $headers, $options, $decode, $encode_body);
}