<?php

use Boctulus\Simplerest\Core\Libs\CorsHandler;
use Boctulus\Simplerest\Core\Libs\Url;
use Boctulus\Simplerest\Core\Response;
use Boctulus\Simplerest\Core\WebRouter;

if (!function_exists('cors')){
    function cors(){
        $params = get_cfg('cors.php');
        
        $cors = new CorsHandler($params);
        $cors->loadConfig($params);
        $cors->handle();
    }    
}

function route(string $name){
    return WebRouter::getRouteByName($name);
}

function httpProtocol(){
    return Url::httpProtocol();
}

function redirect(string $url){
    return Response::redirect($url);
}

function get_api_key_from_request() 
{
    $headers = apache_request_headers();    
    return $headers['X-API-KEY'] ?? Url::getQueryParam(null, 'api_key');
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

function is_ajax(){
    return ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
}