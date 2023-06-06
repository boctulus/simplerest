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

function consume_api(string $url, string $http_verb = 'GET', $body = null, $headers = null, $options = null, $decode = true, $encode_body = true){
    $cli = ApiClient::instance($url)
    ->withoutStrictSSL();

    $cli->setMethod($http_verb);
    $cli->setBody($body, $encode_body);
    $cli->setHeaders($headers ?? []);
    $cli->setOptions($options ?? []);
    $cli->setDecode($decode);
    
    $cli->send();

    return $cli->data();
}