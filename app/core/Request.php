<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Interfaces\Arrayable;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Url;

/*
    Correcciones necesarias !!

    https://chatgpt.com/c/66e8fb29-9d10-800d-9a61-5d8284942006
*/

class Request  implements \ArrayAccess, Arrayable
{
    protected $query_arr;
    protected $raw;
    protected $body;
    protected $params;
    protected $headers;
    protected $accept_encoding;
    protected $content_type;
    protected static $instance;

    protected $as_object = true;

    protected function __construct() {
        $this->initializeRequest();
    }

    private function initializeRequest(): void
    {
        if (php_sapi_name() != 'cli'){
            if (isset($_SERVER['QUERY_STRING'])){
                $this->query_arr = url::queryString();
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

            $headers = $this->getHeadersStatic();

            $accept_encoding_header = $headers['Accept-Encoding'] ?? null;

            if (!empty($accept_encoding_header)){
                $this->accept_encoding = $accept_encoding_header;
            } else {
                if (!empty($this->query_arr["accept_encoding"])){
                    $this->accept_encoding = Arrays::shift($this->accept_encoding, 'accept_encoding');
                }
            }

            /*
                Content-Type

                Para form-data sería multipart/form-data o un derivado como
                'multipart/form-data; boundary=--------------------------240766805501822956475464'
            */

            $content_type_header = $headers['Content-Type'] ?? null;

            if (!empty($content_type_header)){
                $this->content_type = $content_type_header;

            } else {
                if (!empty($this->query_arr["content_type"])){
                    $this->content_type = Arrays::shift($this->accept_encoding, 'content_type');
                }
            }

            // Content-Type
            $is_form_data = (bool) Strings::startsWith('multipart/form-data', $this->content_type);
            $is_json      = ($this->content_type == 'application/json');

            $this->raw  = file_get_contents("php://input");

            // Si el el Content-Type es para json,.... decode

            $this->body = ($is_json && !empty($this->raw)) ? Url::bodyDecode($this->raw) : $this->raw;

            $this->headers = $this->getHeadersStatic();

            $tmp = [];
            foreach ($this->headers as $key => $val){
                $tmp[strtolower($key)] = $val;
            }
            $this->headers = $tmp;

        } else {
            // CLI mode: parse options from $argv
            $this->query_arr = [];
            $this->headers = [];
            $this->body = [];

            global $argv;
            if (isset($argv)) {
                $this->query_arr = $this->parseCliOptions($argv);
            }
        }
    }

    function as_array(){
        $this->as_object = false;
        return $this;
    }

    // Note: getHeaders() is now an instance method (see line ~408)
    // Keeping this static method for backward compatibility
    static function getHeadersStatic() {
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
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * For testing purposes only - allows setting a mock Request instance
     *
     * @param Request|null $instance
     */
    public static function setInstance(?Request $instance): void
    {
        static::$instance = $instance;
    }

    /**
     * Parse CLI options from $argv array
     * Supports formats: --key=value, --key:value, --key
     *
     * @param array $argv
     * @return array
     */
    protected static function parseCliOptions(array $argv): array
    {
        $options = [];

        foreach ($argv as $arg) {
            // Match --key=value or --key:value
            if (preg_match('/^--([^=:]+)[=:](.+)$/', $arg, $matches)) {
                $key = str_replace('-', '_', $matches[1]);
                $value = trim($matches[2], '"\'');
                $options[$key] = $value;
            }
            // Match --key (boolean flag)
            elseif (preg_match('/^--(.+)$/', $arg, $matches)) {
                $key = str_replace('-', '_', $matches[1]);
                $options[$key] = true;
            }
        }

        return $options;
    }

    function getRaw(){
        return $this->raw;
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
        $this->params = $params;
        return $this;
    }

    function getPaginatorParams(){
        $param_names    = Config::get()['paginator']['params'];
        $page_name      = $param_names['page'];
        $page_size_name = $param_names['pageSize'];

        return [
            'page'     => $this->shiftQuery($page_name),
            'pageSize' => $this->shiftQuery($page_size_name),
        ];
    }

    function headers(){
        return $this->headers;
    }

    function header(string $key){
        return $this->headers[strtolower($key)] ?? NULL;
    }

    // alias
    function getHeader(string $key){
        return $this->header($key);
    }

    /**
     * @deprecated Use withHeader() / withoutHeader() for immutable operations
     */
    function shiftHeader(string $key){
        $key = strtolower($key);

        $out = $this->headers[$key] ?? null;
        unset($this->headers[$key]);

        return $out;
    }

    function isAjax(): bool
    {
        return $this->header('X-Requested-With') == 'XMLHttpRequest';
    }

    function getAuth(){
        $token = $this->shiftQuery('token');

        if (!empty($token)){
            return "Bearer $token";
        }

        return $this->headers['authorization'] ?? null;
    }

    /*
        Se refiere solo a si tiene el campo Autorization en headers o ... via url pero es de tipo JWT
    */
    function hasAuth(){
        return $this->getAuth() != NULL;
    }

    function getApiKey(){
        return  $this->headers['x-api-key'] ??
                $this->shiftQuery('api_key') ??
                NULL;
    }

    function hasApiKey(){
        return $this->getApiKey() != NULL;
    }

    function getTenantId(){
        return
            $this->shiftQuery('tenantid') ??
            $this->headers['x-tenant-id'] ??
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
    function acceptEncoding() {
        if ($this->accept_encoding){
            return $this->accept_encoding;
        }

        // @deprecated Use withHeader() / withoutHeader() for immutable operations
        return $this->shiftHeader('Accept-Encoding');
    }

    function gzip(){
        return in_array('gzip', explode(',', $this->acceptEncoding() ?? ''));
    }

    function deflate(){
        return in_array('deflate', explode(',', $this->acceptEncoding() ?? ''));
    }

    function getQuery($key = null)
    {
        if ($key == null)
            return $this->query_arr;
        else
             return $this->query_arr[$key] ?? null;
    }

    // getter destructivo sobre $query_arr
    /**
     * @deprecated Use withQueryParam() / withoutQueryParam() for immutable operations
     */
    function shiftQuery($key, $default_value = NULL, $fn = null)
    {
        static $arr = [];

        if (isset($arr[$key])){
            return $arr[$key];
        }

        if (isset($this->query_arr[$key])){
            $out = $this->query_arr[$key];
            unset($this->query_arr[$key]);
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
        return is_array($this->query_arr) && array_key_exists($key, $this->query_arr);
    }

    function get($key, $default_value = null){
        return $this->query_arr[$key] ?? $default_value;
    }

    function getParam($index){
        return $this->params[$index];
    }

    function getParams(){
        return $this->params;
    }

    /**
     * Instance method wrapper for static getHeaders()
     * Allows mocking in unit tests
     */
    function getHeaders() {
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

    function getBody(?bool $as_obj = null)
    {
        if ($as_obj === null){
            $as_obj = $this->as_object;
        }

        $body = $this->body;

        // Si el cuerpo es un string (no decodificado), decodificarlo como JSON
        if (is_string($body)) {
            $decoded = json_decode($body, $as_obj); // Decodificar como array si $as_obj es false/null, como objeto si $as_obj es true
            if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
                $body = $decoded;
            } else {
                // Si no es JSON válido, devolver como string
                return $body;
            }
        }

        // Si $as_obj es true y body es array, convertir a objeto
        if ($as_obj === true && is_array($body)) {
            return (object) $body;
        }
        // Si $as_obj es false/null y body es objeto, convertir a array
        else if (($as_obj === false || $as_obj === null) && is_object($body)) {
            return (array) $body;
        }

        // Devolver el cuerpo tal cual está si ya tiene el formato correcto
        return $body;
    }

    function getBodyDecoded(){
        $content_type = $this->getHeader('Content-Type');
        $data         = $this->raw;

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
        return $this->body[$key] ?? NULL;
    }

    // getter destructivo sobre el body
    /**
     * @deprecated Use withBody() for immutable operations
     */
    function shiftBodyParam($key){
        if (!isset($this->body[$key])){
            return NULL;
        }

        $ret = $this->body[$key];

        unset($this->body[$key]);
        return $ret;
    }
    
    /**
     * Devuelve una opción (query, body, param o header) por su nombre.
     *
     * Orden de búsqueda:
     * 1. Query string (?key=)
     * 2. Cuerpo (body json o form-data)
     * 3. Parámetros de ruta
     * 4. Headers
     *
     * Ejemplo:
     *   $slug = $request->getOption('slug') ?? $request->getOption('s');
     *
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    function getOption(string $key, $default = null)
    {
        static $cache = [];

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $value = null;

        // 1. Query string
        if ($this->has($key)) {
            $value = $this->get($key);
        }

        // 2. Body (si no se encontró antes)
        if ($value === null && is_array($this->body) && array_key_exists($key, $this->body)) {
            $value = $this->body[$key];
        }

        // 3. Parámetros de ruta
        if ($value === null && isset($this->params[$key])) {
            $value = $this->params[$key];
        }

        // 4. Headers (en minúsculas)
        if ($value === null && isset($this->headers[strtolower($key)])) {
            $value = $this->headers[strtolower($key)];
        }

        $cache[$key] = $value ?? $default;

        return $cache[$key];
    }

    function input($key, $default = null){
        return $this->getOption($key, $default);
    }

    function json(){
        return $this->getBodyDecoded();
    }

    function getCode(){
        return http_response_code();
    }

    /*  ArrayAccess       */

    #[\ReturnTypeWillChange]
    function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->params[] = $value;
        } else {
            $this->params[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    function offsetExists($offset) {
        return isset($this->params[$offset]);
    }

    #[\ReturnTypeWillChange]
    function offsetUnset($offset) {
        unset($this->params[$offset]);
    }

    #[\ReturnTypeWillChange]
    function offsetGet($offset) {
        return isset($this->params[$offset]) ? $this->params[$offset] : null;
    }

    // Antes method()
    function method(){
        $config = Config::get();

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
        return $this->params;
    }

    // ==================== PHASE 2: Immutable Methods (PSR-7 inspired) ====================

    /**
     * Return a new instance with the specified query parameter
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the instance, and MUST return a new instance with
     * the modified query parameter.
     *
     * @param string $key The query parameter key
     * @param mixed $value The query parameter value
     * @return self A new instance with the specified query parameter
     */
    public function withQueryParam(string $key, $value): self
    {
        $new = clone $this;

        if ($new->query_arr === null) {
            $new->query_arr = [];
        }

        $new->query_arr[$key] = $value;
        return $new;
    }

    /**
     * Return a new instance without the specified query parameter
     *
     * @param string $key The query parameter key to remove
     * @return self A new instance without the specified query parameter
     */
    public function withoutQueryParam(string $key): self
    {
        $new = clone $this;

        if (isset($new->query_arr[$key])) {
            unset($new->query_arr[$key]);
        }

        return $new;
    }

    /**
     * Return a new instance with the specified header
     *
     * @param string $name Header name
     * @param string|string[] $value Header value(s)
     * @return self A new instance with the specified header
     */
    public function withHeader(string $name, $value): self
    {
        $new = clone $this;

        if ($new->headers === null) {
            $new->headers = [];
        }

        $lowerName = strtolower($name);
        $new->headers[$lowerName] = is_array($value) ? implode(', ', $value) : $value;

        return $new;
    }

    /**
     * Return a new instance with the specified added header value
     *
     * @param string $name Header name
     * @param string|string[] $value Header value(s) to add
     * @return self A new instance with the added header value
     */
    public function withAddedHeader(string $name, $value): self
    {
        $new = clone $this;

        if ($new->headers === null) {
            $new->headers = [];
        }

        $lowerName = strtolower($name);

        if (isset($new->headers[$lowerName])) {
            $existing = $new->headers[$lowerName];
            $newValue = is_array($value) ? implode(', ', $value) : $value;
            $new->headers[$lowerName] = $existing . ', ' . $newValue;
        } else {
            $new->headers[$lowerName] = is_array($value) ? implode(', ', $value) : $value;
        }

        return $new;
    }

    /**
     * Return a new instance without the specified header
     *
     * @param string $name Header name to remove
     * @return self A new instance without the specified header
     */
    public function withoutHeader(string $name): self
    {
        $new = clone $this;

        $lowerName = strtolower($name);

        if (isset($new->headers[$lowerName])) {
            unset($new->headers[$lowerName]);
        }

        return $new;
    }

    /**
     * Return a new instance with the specified body
     *
     * @param mixed $body The new body (array, object, or string)
     * @return self A new instance with the specified body
     */
    public function withBody($body): self
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

}