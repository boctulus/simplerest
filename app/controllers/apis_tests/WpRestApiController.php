<?php

namespace simplerest\controllers\apis_tests;

use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;
use simplerest\controllers\MyController;

use Automattic\WooCommerce\Client;

/*
    Creacion de credenciales para Basic Auth en la REST API de WooCommerce

    Ir a /wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys

    Notas:

    Authentication Methods

    For the newer APIs (not the "legacy" APIs):

    . For secure connections (HTTPS), you need to use "Basic Authentication".
    
    . For non-secure connections (HTTP), you need to use "oAuth1 Authentication".  <--------- !!!
*/
class WpRestApiController extends MyController
{    
    protected $consumer_key    = 'ck_185ddf7e2fa4f631b8a460f3963b1dc818bc5abf';
    protected $consumer_secret = 'cs_50688595c64b40a0cc1bb610b3852c9ab43be245';

    function via_package(){
        $base_url = 'http://woo1.lan';
        $endpoint = '/wp-json/wc/v3/products';
                       
        $consumer_key    = 'ck_185ddf7e2fa4f631b8a460f3963b1dc818bc5abf';
        $consumer_secret = 'cs_50688595c64b40a0cc1bb610b3852c9ab43be245';

        $woocommerce = new Client(
            $base_url,
            $consumer_key,
            $consumer_secret,
            [
                'debug'           => true,
                'return_as_array' => false,
                'validate_url'    => false,
                'timeout'         => 30,
                'ssl_verify'      => false,
            ]
        );

        $res = $woocommerce->get($endpoint);
        dd($res, 'RES');
    }


    function setOAuth( $request ) { 
        $wc_rest_api = array(
            'key'    => $this->consumer_key,
            'secret' => $this->consumer_secret
        );
     
        $consumerKey = $wc_rest_api['key'];
        $signatureMethod = 'HMAC-SHA1';
        $timestamp = time();
        $nonce = md5(mt_rand());
        $version = '1.0';
     
        $oauthSignature = $this->generateSignature( $request, $timestamp, $nonce, $signatureMethod, $version ) ;
     
        // echo "Signature: " . $oauthSignature . "<br>"; 
     
     
        return "OAuth oauth_consumer_key=\"{$consumerKey}\",oauth_signature_method=\"{$signatureMethod}\",oauth_timestamp=\"{$timestamp}\",oauth_nonce=\"{$nonce}\",oauth_version=\"1.0\",oauth_signature=\"{$oauthSignature}\"";
     
        // return "OAuth oauth_consumer_key={$consumerKey},oauth_signature_method={$signatureMethod},oauth_timestamp={$timestamp},oauth_nonce={$nonce},oauth_version=1.0,oauth_signature={$oauthSignature}";
     
    }
     
    function generateSignature($request, $timestamp, $nonce, $signatureMethod, $version) {
        $wc_rest_api = array(
            'key'    => $this->consumer_key,
            'secret' => $this->consumer_secret
        );
     
        $base = $request['method'] . "&"
            . rawurlencode(  $request['url'] ) . "&"
            . rawurlencode( "oauth_consumer_key=" . $wc_rest_api['key'] )
            . rawurlencode( "&oauth_nonce=" . $nonce )
            . rawurlencode( "&oauth_signature_method=" . $signatureMethod )
            . rawurlencode( "&oauth_timestamp=" . $timestamp )
            . rawurlencode( "&oauth_version=" . $version );
            // . rawurlencode( '&' . http_build_query($request['data']) );
     
        $key = rawurlencode($wc_rest_api['secret']) . '&';
        $signature = base64_encode( hash_hmac('sha1', $base, $key, true) );
     
        return $signature;
    }

    // apis_tests wp_rest_api
    function index()
    {
        $base_url = 'http://woo1.lan';
        $endpoint = '/wp-json/wc/v3/products';
        $method   = 'GET';
                       
        $url    = "{$base_url}{$endpoint}";
  
        $request = array(
            'method' => $method,
            'url'    => $url
        );

        $cli = (new ApiClient($url))
        ->withoutStrictSSL()
        ->setHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $this->setOAuth( $request),
            'User-Agent'   => 'PostmanRuntime/7.34.0',
        ])

        // ->queryParams([
        //     'consumer_key'    => $consumer_key,
        //     'consumer_secret' => $consumer_secret
        // ])       
        
        //->cache(600000)
        ;

        $cli->setMethod('GET');

        $cli->send();

        dd($cli->dump(), 'REQ');

        $res = $cli->data();

        // {"errors":[{"code":"woocommerce_api_authentication_error","message":"oauth_consumer_key parameter is missing"}]}
        dd($res, 'RES');
    }
}

