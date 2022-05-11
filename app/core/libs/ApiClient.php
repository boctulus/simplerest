<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Url;

/*
    Wrapper para Url::consume_api()
*/
class ApiClient
{
    protected $headers;
    protected $options;
    protected $body;
    protected $auto_decode;
    protected $status;
    protected $errors;
    protected $response;

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

    function getStatus(){
        return $this->status;
    }

    function getErrors(){
        return $this->errors;
    }

    function getResponse(bool $decode = true, bool $as_array = true){
        if ($decode){
            return json_decode($this->response, $as_array);
        }

        return $this->response;
    }
    
    function request(string $url, string $http_verb, $body = null, ?Array $headers = null, ?Array $options = null){
        $body    = $body    ?? $this->body    ?? null;
        $headers = $headers ?? $this->headers ?? null;
        $options = $options ?? $this->options ?? null;
        $decode  = $decode  ?? $this->auto_decode;

        $res = Url::consume_api($url, $http_verb, $body, $headers, $options, false);
        $this->status   = $res['http_code'];
        $this->errors   = $res['error'];
        $this->response = $res['data'];

        return $res;
    }

}

