<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class WooCommerceRestAPI
{
    protected $consumer_key;
    protected $consumer_secret;

    function __construct($consumer_key, $consumer_secret)
    {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
    }

    public function setOAuth( $request ) { 
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
     
    protected function generateSignature($request, $timestamp, $nonce, $signatureMethod, $version) {
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


}

