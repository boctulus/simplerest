<?php

use simplerest\core\Route;
use simplerest\core\Response;
use simplerest\core\libs\Url;

/**
 * Configura las cabeceras CORS según los parámetros proporcionados.
 *
 * @param string $crossOrigin 
 * @param bool  $allowCredentials - Indica si se permiten credenciales (cookies).
 * @param array $allowedHeaders - Lista de cabeceras permitidas.
 * @param array $allowedMethods - Lista de métodos HTTP permitidos.
 */
function cors(
    string $crossOrigin = '*',
    bool   $allowCredentials = true,
    array  $allowedHeaders = [
        'Origin',
        'Content-Type',
        'X-Auth-Token',
        'AccountKey',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Client-Security-Token',
        'Host',
        'Date',
        'Cookie',
        'Cookie2',
    ],
    array $allowedMethods = ['POST', 'OPTIONS']
) {
    
    header("Access-Control-Allow-Origin: $crossOrigin");
    header('Access-Control-Allow-Credentials: ' . ($allowCredentials ? 'True' : 'False'));
    header('Access-Control-Allow-Headers: ' . implode(', ', $allowedHeaders));
    header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
}

function route(string $name){
    return Route::getRouteByName($name);
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

/*
    Deberia procesarme con mas precaucion lo que provenga de Ajax ? 
*/

function is_ajax(){
    return ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest");
}