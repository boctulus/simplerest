<?php

namespace Boctulus\ApiClient\Helpers;

use Boctulus\ApiClient\Helpers\Strings;
use Boctulus\ApiClient\Interfaces\Arrayable;
use Boctulus\ApiClient\Helpers\Url;
use Boctulus\ApiClient\Helpers\Arrays;

/*
    Correcciones necesarias !!

    https://chatgpt.com/c/66e8fb29-9d10-800d-9a61-5d8284942006
*/

class Request  implements /*\ArrayAccess,*/ Arrayable
{
    protected static $query_arr;
    protected static $raw;
    protected static $body;
    protected static $params;
    protected static $headers;
    protected static $accept_encoding;
    protected static $content_type;
    protected static $instance = NULL;

    protected        $as_object = true;

    protected function __construct() { }

    function as_array(){
        $this->as_object = false;
        return $this;
    }

    static function getHeaders() {
        if (function_exists('apache_request_headers')) {
            return apache_request_headers();
        }
        // alternativa para obtener los encabezados en otros servidores
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    static function isBrowser(): bool 
    {
        if (!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        return (
            stripos($user_agent, 'Mozilla') !== false || 
            stripos($user_agent, 'Chrome') !== false ||
            stripos($user_agent, 'Safari') !== false ||
            stripos($user_agent, 'Edge') !== false ||
            stripos($user_agent, 'Firefox') !== false
        );
    }

    static function getInstance() : Request {
        if(static::$instance == NULL){
            if (php_sapi_name() != 'cli'){
                if (isset($_SERVER['QUERY_STRING'])){
                    static::$query_arr = url::queryString();
                }
                
                /*
                    Accept encoding

                    Accept-Encoding: gzip
                    Accept-Encoding: compress
                    Accept-Encoding: deflate
                    Accept-Encoding: br
                    Accept-Encoding: identity
                    Accept-Encoding: *

                    https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Encoding
                */

                $headers = static::getHeaders();

                $accept_encoding_header = $headers['Accept-Encoding'] ?? null;
                
                if (!empty($accept_encoding_header)){
                    static::$accept_encoding = $accept_encoding_header;
                } else {
                    if (!empty(static::$query_arr["accept_encoding"])){
                        static::$accept_encoding = Arrays::shift(static::$accept_encoding, 'accept_encoding');
                    }
                }                
                
                /*
                    Content-Type

                    Para form-data sería multipart/form-data o un derivado como
                    'multipart/form-data; boundary=--------------------------240766805501822956475464'
                */

                $content_type_header = $headers['Content-Type'] ?? null;

                if (!empty($content_type_header)){
                    static::$content_type = $content_type_header;
                    
                } else {
                    if (!empty(static::$query_arr["content_type"])){
                        static::$content_type = Arrays::shift(static::$accept_encoding, 'content_type');
                    }
                }   

                // Content-Type
                $is_form_data = (bool) Strings::startsWith('multipart/form-data', static::$content_type);
                $is_json      = (static::$content_type == 'application/json');

                static::$raw  = file_get_contents("php://input");

                // Si el Content-Type es para json,.... decode

                static::$body = ($is_json && !empty(static::$raw)) ? Url::bodyDecode(static::$raw) : static::$raw;
                
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

    function getRaw(){
        return static::$raw;
    }

    function getFormData(){
        return $_POST;
    }

    /*
        Intenta recuperar via $_POST un JSON enviado como body en modo "raw"

        $_POST solo funciona con

            Content-Type: application/x-www-form-urlencoded

        y

            Content-Type: multipart/form-data (usado principalmente para file uploads)

        Ver
        https://stackoverflow.com/a/8893792

    */
    function parseFormData(){
        $data = $_POST;

        if (static::getHeader('Content-type') == 'application/x-www-form-urlencoded'){
            $json = Arrays::arrayKeyFirst($data);
            $json = preg_replace('/_(?![^"]*"(?:(?:[^"]*"){2})*[^"]*$)/i', ' ', $json);
            $data = json_decode($json, true);

            if (empty($data)){
                return false;
            }

            foreach($data as $k => $v){
                if (is_string($v)){
                    $data[$k] = str_replace('_', ' ', $v);
                }
            }
        }

        return $data;
    }
    
    function setParams($params){
        static::$params = $params;
        return static::getInstance();
    }

    function getPaginatorParams(){
        $param_names    = config()['paginator']['params'];
        $page_name      = $param_names['page'];
        $page_size_name = $param_names['pageSize'];
        
        return [
            'page'     => static::shiftQuery($page_name),
            'pageSize' => static::shiftQuery($page_size_name),
        ];    
    }

    function headers(){
        return static::$headers;
    }

    function header(string $key){
        return static::$headers[strtolower($key)] ?? NULL;
    }

    // alias
    function getHeader(string $key){
        return $this->header($key);
    }

    function shiftHeader(string $key){
        $key = strtolower($key);

        $out = static::$headers[$key] ?? null;
        unset(static::$headers[$key]);

        return $out;
    }

    function getAuth(){
        $token = $this->shiftQuery('token'); 

        if (!empty($token)){
            return "Bearer $token";
        }

        return static::$headers['authorization'] ?? null;
    }
    
    /*
        Se refiere solo a si tiene el campo Autorization en headers o ... via url pero es de tipo JWT
    */
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
            $this->shiftQuery('tenantid') ??
            static::$headers['x-tenant-id'] ??             
            NULL;
    }

    function hasTenantId(){
        return $this->getTenantId() !== NULL; 
    }

    function authMethod(){
        static $method;

        if ($method !== null){
            return $method;
        }

        if ($this->hasApiKey()){
            $method = 'API_KEY';
        }elseif ($this->hasAuth()){
            $method = 'JWT';
        }

        return $method;
    }

    function isAuthenticated(){
        return !is_cli() && !empty($this->authMethod());
    }

    /*  
        https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Encoding
    */
    function acceptEncoding() : ?string {
        if (static::$accept_encoding){
            return static::$accept_encoding;
        }

        return static::shiftHeader('Accept-Encoding');
    }

    function gzip(){
        return in_array('gzip', explode(',', static::acceptEncoding() ?? ''));
    }

    function deflate(){
        return in_array('deflate', explode(',', static::acceptEncoding() ?? ''));
    }

    function getQuery(string $key = null)
    {
        if ($key == null)
            return static::$query_arr;
        else 
             return static::$query_arr[$key] ?? null;   
    }    

    // getter destructivo sobre $query_arr
    function shiftQuery($key, $default_value = NULL, callable $fn = null)
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

        if ($fn != null){
            return $fn($out, $key, $default_value);
        }

        return $out;
    }
    
    function has($key){
        return array_key_exists($key, static::$query_arr);
    }

    function get($key, $default_value = null){
        return static::$query_arr[$key] ?? $default_value;
    }

    function getParam($index){
        return static::$params[$index];
    } 

    function getParams(){
        return static::$params;
    } 

    function getBody(?bool $as_obj = null)
    {
        if ($as_obj === null){
            $as_obj = $this->as_object;
        }

        return $as_obj ? (object) static::$body : static::$body;
    }

    function getBodyDecoded(){
        $content_type = static::getHeader('Content-Type');
        $data         = static::$raw;
       
        if (!empty($content_type))
        {
            // Podría ser un switch-case aceptando otros MIMEs
            if (Strings::contains('application/x-www-form-urlencoded', $content_type)){
                $data = urldecode($data);
                $data = Url::parseStrQuery($data);

            } else {
                $data = json_decode($data, true);

                if ($data === null) {
                    throw new \Exception("JSON inválido");
                }
            }

        }

        return $data;
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

    // function offsetSet($offset, $value) {
    //     if (is_null($offset)) {
    //         static::$params[] = $value;
    //     } else {
    //         static::$params[$offset] = $value;
    //     }
    // }

    // function offsetExists($offset) {
    //     return isset(static::$params[$offset]);
    // }

    // function offsetUnset($offset) {
    //     unset(static::$params[$offset]);
    // }

    // function offsetGet($offset) {
    //     return isset(static::$params[$offset]) ? static::$params[$offset] : null;
    // }

    // Antes method()
    function method(){
        $config = config();

        $asked_method = null;
        if ($config['method_override']['by_url'] ?? null){
            $asked_method  =  $this->shiftQuery('_method');
        }

        if ($asked_method == null && ($config['method_override']['by_header'] ?? null)){
            $asked_method  =  $this->header('X-HTTP-Method-Override'); 
        }

        if ($asked_method == NULL){
            $asked_method = $_SERVER['REQUEST_METHOD'] ?? NULL;
        }
        
        return $asked_method;
    }

    static function ip(){
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    }

    static function user_agent(){
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }

    /* Arrayable Interface */ 

    function toArray(){
        return static::$params;
    }

}