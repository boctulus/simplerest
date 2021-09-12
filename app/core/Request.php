<?php

namespace simplerest\core;

use simplerest\libs\Factory;
use simplerest\core\interfaces\Arrayable;

class Request  implements \ArrayAccess, Arrayable
{
    static protected $query_arr;
    static protected $raw;
    static protected $body;
    static protected $params;
    static protected $headers;
    static protected $instance = NULL;

    protected function __construct() { }

    static function getInstance(){
        if(static::$instance == NULL){
            if (php_sapi_name() != 'cli'){
                if (isset($_SERVER['QUERY_STRING']))
					parse_str($_SERVER['QUERY_STRING'], static::$query_arr);
				
                static::$raw  = file_get_contents("php://input");
                static::$body = json_decode(static::$raw, true);
                static::$headers = apache_request_headers();

                $tmp = [];
                foreach (static::$headers as $key => $val){
                    $tmp[strtolower($key)] = $val;
                }
                static::$headers = $tmp;
                
            }
            static::$instance = new static();
        }
        return static::$instance;
    }

    function setParams($params){
        static::$params = $params;
        return static::getInstance();
    }

    function headers(){
        return static::$headers;
    }

    function header(string $key){
        return static::$headers[strtolower($key)] ?? NULL;
    }

    function getAuth(){
        return static::$headers['authorization'] ?? NULL;
    }

    function hasAuth(){
        return $this->getAuth() != NULL; 
    }

    function getApiKey(){
        return  static::$headers['x-api-key'] ?? 
                $this->shiftQuery('api_key') ??                
                NULL;
    }

    function hasApiKey(){
        return $this->getApiKey() != NULL; 
    }

    function getTenantId(){
        return  
            Factory::auth()->check()['tenantid'] ??
            $this->shiftQuery('tenantid') ??
            static::$headers['x-tenant-id'] ??                 
            NULL;
    }

    function hasTenantId(){
        return $this->getTenantId() != NULL; 
    }

    function authMethod(){
        if ($this->hasApiKey()){
            return 'API_KEY';
        }elseif ($this->hasAuth()){
            return 'JWT';
        }
    }

    function gzip(){
        return in_array('gzip', explode(',', str_replace(' ', '',$this->header('Accept-Encoding'))));
    }

    function deflate(){
        return in_array('deflate', explode(',', str_replace(' ', '',$this->header('Accept-Encoding'))));
    }

    function getQuery(string $key = null)
    {
        if ($key == null)
            return static::$query_arr;
        else 
             return static::$query_arr[$key];   
    }    

    // getter destructivo sobre $query_arr
    function shiftQuery($key, $default_value = NULL)
    {
        static $arr = [];

        if (isset($arr[$key])){
            return $arr[$key];
        }

        if (isset(static::$query_arr[$key])){
            $out = static::$query_arr[$key];
            unset(static::$query_arr[$key]);
            $arr[$key] = $out;
        } else {
            $out = $default_value;
        }

        return $out;
    }

    function getParam($index){
        return static::$params[$index];
    } 

    function getBody($as_obj = true)
    {
        return $as_obj ? (object) static::$body : static::$body;
    }

    function getBodyParam($key){
        return static::$body[$key] ?? NULL;
    }

    // getter destructivo sobre el body
    function shiftBodyParam($key){
        if (!isset(static::$body[$key])){
            return NULL;
        }

        $ret = static::$body[$key];

        unset(static::$body[$key]);
        return $ret;
    }

    function getCode(){
        return http_response_code();
    }

    /*  ArrayAccess       */

    function offsetSet($offset, $value) {
        if (is_null($offset)) {
            static::$params[] = $value;
        } else {
            static::$params[$offset] = $value;
        }
    }

    function offsetExists($offset) {
        return isset(static::$params[$offset]);
    }

    function offsetUnset($offset) {
        unset(static::$params[$offset]);
    }

    function offsetGet($offset) {
        return isset(static::$params[$offset]) ? static::$params[$offset] : null;
    }

    function getRequestMethod(){
        return $_SERVER['REQUEST_METHOD'] ?? NULL;
    }

    static function ip(){
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    }

    /* Arrayable Interface */ 

    function toArray(){
        return static::$params;
    }

}