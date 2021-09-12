<?php

use simplerest\core\Route;
use simplerest\core\Response;
use simplerest\libs\Factory;


function route(string $name){
    return Route::getRouteByName($name);
}

function http_protocol(){
    $config = Factory::config();

    if ($config['HTTPS'] == 1 || strtolower($config['HTTPS']) == 'on'){
        $protocol = 'https';
    } else {
        $protocol = 'http';
    }

    return $protocol;
}

function redirect(string $url){
    return Response::redirect($url);
}

// @author limalopex.eisfux.de
if (!function_exists('apache_request_headers')){
    function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach($_SERVER as $key => $val) {
            if( preg_match($rx_http, $key) ) {
            $arh_key = preg_replace($rx_http, '', $key);
            $rx_matches = array();
            // do some nasty string manipulations to restore the original letter case
            // this should work in most cases
            $rx_matches = explode('_', $arh_key);
            if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                $arh_key = implode('-', $rx_matches);
            }
            $arh[$arh_key] = $val;
            }
        }
        return( $arh );
    }
}