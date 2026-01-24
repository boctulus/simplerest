<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Libs\Strings;

class WooCommerceApiClient extends ApiClient
{
    protected $username;
    protected $password;

    function __construct($consumer_key , $consumer_secret)
    {
        $this->username = $consumer_key;
        $this->password = $consumer_secret;

        $this
        ->withoutStrictSSL()        
        ->contentType('application/json')
        ->userAgent('PostmanRuntime/7.34.0')
        ->decode();

        parent::__construct();
    }
    
    /*
        De uso obligado para peticiones HTTP
    */
    public function setOAuth()
    {
        if ($this->url === null){
            throw new \Exception("Undefined url");
        }

        if ($this->verb === null){
            throw new \Exception("Undefined HTTP verb");
        }
    
        $this->authorization(
            $this->getOAuth($this->url, $this->verb)
        );

        return $this;        
    }

    protected function getOAuth($url, $method) { 
        $request = array(
            'method' => $method,
            'url'    => $url
        );

        $wc_rest_api = array(
            'key'    => $this->username,
            'secret' => $this->password
        );
     
        $consumerKey = $wc_rest_api['key'];
        $signatureMethod = 'HMAC-SHA1';
        $timestamp = time();
        $nonce = md5(mt_rand());
        $version = '1.0';
     
        $oauthSignature = $this->generateSignature($request, $timestamp, $nonce, $signatureMethod, $version) ;
     
        return "OAuth oauth_consumer_key=\"{$consumerKey}\",oauth_signature_method=\"{$signatureMethod}\",oauth_timestamp=\"{$timestamp}\",oauth_nonce=\"{$nonce}\",oauth_version=\"1.0\",oauth_signature=\"{$oauthSignature}\"";
    }
     
    protected function generateSignature($request, $timestamp, $nonce, $signatureMethod, $version) {
        $wc_rest_api = array(
            'key'    => $this->username,
            'secret' => $this->password
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

}

