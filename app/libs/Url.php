<?php

namespace simplerest\libs;

class Url {
    
    static function has_ssl( $domain ) {
        $ssl_check = @fsockopen( 'ssl://' . $domain, 443, $errno, $errstr, 30 );
        $res = !! $ssl_check;
        if ( $ssl_check ) { fclose( $ssl_check ); }
        return $res;
    }

    static function http_protocol(){
        $config = config();
       
        if (isset($config['HTTPS']) && $config['HTTPS'] != null){
            if ($config['HTTPS'] == 1 || strtolower($config['HTTPS']) == 'on'){
                $protocol = 'https';
            } else {
                $protocol = 'http';
            }
        } else {
            $protocol = self::has_ssl($_SERVER['HTTP_HOST']) ? 'https' : 'http';
        } 
        
        return $protocol;
    }

    /**
     * url_check - complement for parse_url
     *
     * @param  string $url
     *
     * @return bool
     */
    static function url_check(string $url){
        $sym = null;
    
        $len = strlen($url);
        for ($i=0; $i<$len; $i++){
            if ($url[$i] == '?'){
                if ($sym == '?' || $sym == '&')
                    return false;
    
                $sym = '?';
            }elseif ($url[$i] == '&'){
                if ($sym === null)
                    return false;
    
                $sym = '&';
            } 
        }
        return true;
    }

    static function is_postman(){
        if (!isset($_SERVER['HTTP_USER_AGENT'])){
            return false;
        }

		return (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'PostmanRuntime') !== false);	
	}

    static function is_insomnia(){
        if (!isset($_SERVER['HTTP_USER_AGENT'])){
            return false;
        }
        
        return Strings::startsWith('insomnia', $_SERVER['HTTP_USER_AGENT']);
    }

    static function getQueryParam(string $url, string $param){
        $query = parse_url($url, PHP_URL_QUERY);

        $x = null;
        if ($query != null){
            $q = explode('&', $query); 
            foreach($q as $p){
                if (Strings::startsWith($param . '=', $p)){
                    $_x = explode('=', $p);
                    $x  = $_x[count($_x)-1];                    
                }
            }
        }

        return $x;
    }

    static function currentUrl(){
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $actual_link;
    }

    static function getBaseUrl($url)
    {
        $url_info = parse_url($url);
        return  $url_info['scheme'] . '://' . $url_info['host'];
    }

    /*
        @author  Pablo Bozzolo  <boctulus@gmail.com>
    */
    static function consume_api(string $url, string $http_verb, $body = null, ?Array $headers = null, ?Array $options = null, $decode = true)
    {  
        if ($headers === null){
            $headers = [];
        }

        if ($options === null){
            $options = [];
        }

        $keys = array_keys($headers);

        $content_type_found = false;
        foreach ($keys as $key){
            if (strtolower($key) == 'content-type'){
                $content_type_found = $key;
                break;
            }
        }

        $accept_found = false;
        foreach ($keys as $key){
            if (strtolower($key) == 'accept'){
                $accept_found = $key;
                break;
            }
        }

        if (!$content_type_found){
            $headers = array_merge(
                [
                    'Content-Type' => 'application/json'
                ], 
                ($headers ?? [])
            );
        } 
        
        
        if ($accept_found) { 
            if (Strings::startsWith('text/plain', $headers[$accept_found]) || 
                Strings::startsWith('text/html', $headers[$accept_found])){
                $decode = false;
            }
        }
   
        if (/* $headers[$content_type_found] == 'application/json' || */ is_array($body)){
            $data = json_encode($body);
        } else {
            $data = $body;
        }

        $curl = curl_init();

        $http_verb = strtoupper($http_verb); 
    
        if ($http_verb != 'GET' && !empty($data)){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $headers['Content-Length']   = strlen($data);
        }
    
        $h = [];
        foreach ($headers as $key => $header){
            $h[] = "$key: $header";
        }

        $options = [
            CURLOPT_HTTPHEADER => $h
        ] + ($options ?? []);

        curl_setopt_array($curl, $options);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '' );
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0 );
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_verb);
   
        // https://stackoverflow.com/a/6364044/980631
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_HTTP200ALIASES, (array)400);    


        $response  = curl_exec($curl);
        $err_msg   = curl_error($curl);	
        $http_code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
        curl_close($curl);
    

        $data = $decode ? json_decode($response, true) : $response;
    
        $ret = [
            'data'      => $data,
            'http_code' => $http_code,
            'error'     => $err_msg
        ];
    
        return $ret;
    }    
    
}

