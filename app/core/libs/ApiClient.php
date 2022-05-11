<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Url;

/*
    Wrapper para Url::consume_api()
*/
class ApiClient
{
    protected $url;
    protected $verb;
    protected $headers;
    protected $options;
    protected $body;
    protected $auto_decode;
    protected $status;
    protected $errors;
    protected $response;
    protected $expiration;

    function setHeaders(Array $headers){
        $this->headers = $headers;
        return $this;
    }

    function setOptions(Array $options){
        $this->options = $options;
        return $this;
    }

    function setBody($body){
        $this->body = $body;
        return $this;
    }

    function setDecode(bool $auto){
        $this->auto_decode = $auto;
        return $this;
    }

    /*
        @param $expiration_time int seconds 
    */
    function setCache($expiration_time = 60){
        $this->expiration = $expiration_time;
        return $this;
    }

    function getStatus(){
        return $this->status;
    }

    function getErrors(){
        return $this->errors;
    }

    function getResponse(bool $decode = true, bool $as_array = true){
        if ($decode){
            $res = json_decode($this->response, $as_array);
        }    

        return $res;
    }
    
    function request(string $url, string $http_verb, $body = null, ?Array $headers = null, ?Array $options = null){
        $this->url  = $url;
        $this->verb = strtoupper($http_verb);

        $body    = $body    ?? $this->body    ?? null;
        $headers = $headers ?? $this->headers ?? null;
        $options = $options ?? $this->options ?? null;
        $decode  = $decode  ?? $this->auto_decode;

        if ($this->expiration){
            $res = $this->getCache();

            if ($res !== null){
                $deco = json_decode($res, true);    

                $this->status   = $deco['http_code'];
                $this->errors   = $deco['error'];
                $this->response = $deco['data'];
                return;
            }
        }

        $res = Url::consume_api($url, $http_verb, $body, $headers, $options, false);
        $this->status   = $res['http_code'];
        $this->errors   = $res['error'];
        $this->response = $res['data'];

        if ($this->expiration){
            $this->saveResponse($res);
        }
    }

    protected function getCachePath(){
        static $path;

        if (isset($path[$this->url])){
            return $path[$this->url];
        }

        $filename = str_replace(['%'], ['p'], urlencode(Url::normalize($this->url))) . '.html';
        $filename = str_replace('/', '', $filename);

        $path[$this->url] = sys_get_temp_dir() . '/' . $filename;
        return $path[$this->url];
    }
 
	protected function saveResponse(Array $response){
        if ($this->verb != 'GET'){
            return;
        }

        $path = $this->getCachePath();

        file_put_contents($path, var_export($response, true));
    }

    protected function getCache(){
        $path = $this->getCachePath();

        if (file_exists($path)){
            return file_get_contents($path);
        }

        return null;
    }

}

