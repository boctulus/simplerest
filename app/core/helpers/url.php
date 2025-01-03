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

// Alias de base_url()
if (!function_exists('site_url')){
    function site_url(){
        return base_url();
    }    
}

function consume_api(string $url, string $http_verb = 'GET', $body = null, $headers = null, $options = null, $decode = true, $encode_body = true, $cache_time = null){
    if (substr($url, 0, 1) == '/'){
        $url = base_url() . $url;
    }
    
    $cli = (new ApiClient($url))
    ->withoutStrictSSL();

    $cli->setMethod($http_verb);
    $cli->setBody($body, $encode_body);
    $cli->setHeaders($headers ?? []);
    
    if (!empty($options)){
        $cli->setOptions($options);
    }

    if ($cache_time !== null){
        if (is_string($cache_time)){
            // si es una fecha-hora
            $cli->cacheUntil($cache_time);
        } else if (is_int($cache_time)) {
            // si es un numero entero
            $cli->cache($cache_time);
        } else {
            throw new \InvalidArgumentException("Invalid format for cache_time");
        }
    }

    $cli->send();

    $res = $cli->data();

    if ($decode && Strings::isJSON($res)){
        $res = json_decode($res, true);
        $res['status'] = $cli->getStatus();

        $err = $cli->getError();

        if (!empty($err)){
            $res['error'] = $err;
        }
    }

    return $res;
}