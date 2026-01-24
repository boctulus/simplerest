<?php

namespace Boctulus\Simplerest\Core;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Url;

class Response implements \ArrayAccess 
{
    protected $data = [];
    protected $to_be_encoded;
    protected $headers = [];
    protected $http_code = NULL;
    protected $http_code_msg = '';
    protected $version = '2';
    protected $config;
    protected $pretty;
    protected $paginator_params;
    protected $as_object = false;
    protected $fake_status_codes = false; // send 200 instead
    protected $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    protected static $instance = NULL;    


    protected function __construct() {
        $this->config = Config::get();
        $this->pretty = $this->config['pretty'];
    }

    public function __destruct()
    {
        DB::closeAllConnections();
    }    

    static function getInstance(){
        // For backward compatibility we maintain the singleton instance
        if(static::$instance == NULL){
            static::$instance = new static();
        }
        return static::$instance;
    }

    // Method to create a new instance (non-singleton)
    static function create(){
        return new static();
    }

    /**
     * For testing purposes only - allows setting a mock Response instance
     *
     * @param Response|null $instance
     */
    public static function setInstance($instance): void
    {
        static::$instance = $instance;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    static protected function header(string $header, bool $replace = true, int $response_code = 0){
        if (php_sapi_name() != 'cli'){
            header($header, $replace, $response_code);
        }  
    }
    
    static function redirect(string $url, int $http_code = 307) {
        // Verificar si las cabeceras ya han sido enviadas
        if (!headers_sent()) {          
            // Verificar si se ha proporcionado un código HTTP válido
            switch ($http_code){
                case 301:
                    header('HTTP/1.1 301 Moved Permanently');
                    break;
                case 302:
                    header('HTTP/1.1 302 Found');
                    break;
                case 307:
                    header('HTTP/1.1 307 Temporary Redirect');
                    break;
                case 308:
                    header('HTTP/1.1 308 Permanent Redirect');
                    break;
                default:
                    throw new \InvalidArgumentException("Código HTTP no válido para redirección");
            }
            
            // Configurar la cabecera de ubicación para la redirección
            header("Location: $url");
            exit;
        } else {
            // Si las cabeceras ya han sido enviadas, lanzar una excepción
            throw new \Exception("Las cabeceras ya han sido enviadas");
        }
    }    

    function asObject(bool $val = true){
        $this->as_object = $val;
    }

    function addHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    function addHeader(string $header)
    {
        $this->headers[] = $header;
        return $this;
    }

    // Alias de addHeader()
    function setHeader(string $header){
        return $this->addHeader($header);
    }

    /**
     * sendHeaders
     *
     * @param  mixed $headers
     *
     * @return void
     */
    private function sendHeaders(array $headers = []) {
        if (php_sapi_name() == 'cli'){
            return;
        }

        foreach ($headers as $k => $val){
            if (empty($val))
                continue;
            
            header("$k:$val");
        }
    }

    function code(int $http_code, string $msg = '')
    {
        $this->http_code_msg = $msg;
        $this->http_code = $http_code;
        return $this;
    }

    function setPretty(bool $state){
        $this->pretty = $state;
        return $this;
    }

    protected function do_encode($data)
    {
        $this->header('Content-type:application/json;charset=utf-8');

        $options = $this->pretty ? $this->options | JSON_PRETTY_PRINT : $this->pretty;

        return json_encode($data, $options);
    }

    function encode(){
        $this->to_be_encoded = true;
        return $this;
    }

    function setPaginatorParams($row_count, $count, $current_page, $page_count, $page_size, $nextUrl){
        $formatter = Config::get()['paginator']['formatter'];

        $this->to_be_encoded    = true;

        $this->paginator_params = $formatter(
            $row_count, $count, $current_page, $page_count, $page_size, $nextUrl
        );

        return $this;
    }

    function send($data, $http_code = NULL){
        if ($http_code >= 400) {
            return $this->error($data, $http_code);
        }

        $http_code = $http_code != NULL ? $http_code : ($this->http_code !== null ? $this->http_code : 200);

        if (php_sapi_name() != 'cli' && !headers_sent()) {
            header(trim('HTTP/'.$this->version.' '.$http_code.' '.$this->http_code_msg));
        }

        if ($this->as_object || is_object($data) || is_array($data)) {
            $arr = [];

            if ($this->config['paginator']['position'] == 'TOP'){
                if ($this->paginator_params != NULL){
                    $arr = array_merge($arr, $this->paginator_params);
                }
            }

            /*
                Evita responder con data[data]
            */
            if (isset($data['data'])){
                $data = $data['data'];
            }

            $data = array_merge([
                    'data' => $data,
                    'status_code' => $http_code,
                    'error' => []
            ], $arr);

            $this->http_code = $http_code; //

            if ($this->config['paginator']['position'] == 'BOTTOM'){
                if ($this->paginator_params != NULL){
                    $data = array_merge($data, $this->paginator_params);
                }
            }
        }

        $this->set( $data );
        return $this;
    }

    private function zip($data){
        $data  = gzcompress($data, 9);

        ob_start("ob_gzhandler");
        echo $data; 
        ob_end_flush();
    } 

    function sendCode(int $http_code){
        $this->set( json_encode(['status_code' => $http_code]) );

        if (!$this->fake_status_codes){
            http_response_code($http_code);
        }

        $this->http_code = $http_code; //

        return $this;
    }
 

    function sendOK(){
        if (!headers_sent()) {
            http_response_code(200);
        }
        
        return static::$instance; 
    }

    // send as JSON
    function sendJson($data, $http_code = null, ?string $error_msg = null){
        $http_code = $http_code != null ? $http_code : ($this->http_code !== null ? $this->http_code : 200);

        $this->to_be_encoded = true;

        if (php_sapi_name() != 'cli' && !headers_sent()) {
            header(trim('HTTP/'.$this->version.' '.$http_code.' '.$this->http_code_msg));
        }

        /*
            Evita responder con data[data]
        */
        if (isset($data['data'])){
            $data = $data['data'];
        }

        $res = [
            'data' => $data,
            'status_code' => $http_code,
            'error' => $error_msg ?? ''
        ];

        $this->http_code = $http_code; //

        $this->set($res);

        return $this;
    }


   
    /**
     * error
     *
     *
     * @return void
     */
    function error($error = null, ?int $http_code = null, $detail = null, ?string $location = null)
    {
        if (is_string($error)){
            $message = $error;
        } elseif (is_array($error)){
            $type    = $error['type'] ?? null;
            $message = $error['text'] ?? null;
            $code    = $error['code'];
        }

        if (php_sapi_name() != 'cli'){
            if (!headers_sent()) {
                if ($http_code == NULL)
                    if ($this->http_code != NULL)
                        $http_code = $this->http_code;
                    else
                        $http_code = 500;

                if ($http_code != NULL && !$this->fake_status_codes)
                    header(trim('HTTP/'.$this->version.' '.$http_code.' '.$this->http_code_msg));
            }
        }

        $this->http_code = $http_code; //
        $res['status'] = $http_code;

        /*
            https://www.baeldung.com/rest-api-error-handling-best-practices
        */

        // Parche 14-Nov-2022 -modificado en 2023-

        if (Url::isPostman() || Url::isInsomnia()){
            if (is_string($detail)){
                $detail = trim($detail);
                $detail = Strings::isJSON($detail) ? json_decode($detail, true, 512, JSON_UNESCAPED_SLASHES) : $detail;
            }
        }

        $res['error'] = [
            'type'     => $type    ?? null,
            'code'     => $code    ?? null,
            'message'  => $message,
            'detail'   => $detail,
            'location' => $location  // <--- location deberia ser rellado automaticamente leyendo el stack
        ];

        $this->set($res);
        $this->flush();

        exit;
    }

    function set($data){
        $this->data = $data;
        return $this;
    }

    function get(){
        // Parche aplicado el 14-Nov-2022

        // If already marked to be encoded, return raw data (will be encoded in flush())
        if ($this->to_be_encoded) {
            return $this->data;
        }

        if (is_array($this->data)){
            $this->header('Content-type:application/json;charset=utf-8');
            return json_encode($this->data);
        }

        return $this->data;
    }

    function __toString()
    {
        return $this->get();
    }

    function isEmpty(){
        return $this->data == null;
    }

    /*
        Este método podría reducirse practicamente a generar distintos
        tipos de excepciones que serían capturadas por un Handler.php

        https://stackoverflow.com/a/30832286/980631
        https://tutsforweb.com/how-to-create-custom-404-page-laravel/
    */
    function flush(){
        // print_r(['Memory usage'=> System::getMemoryUsage()]);
        // print_r("<br>");

        // var_dump($this->data);

        if ($this->to_be_encoded){
            $this->data = $this->do_encode($this->data);
            $this->header('Content-type:application/json;charset=utf-8');
        } else{
            $accept = request()->header('Accept');

            if (Strings::startsWith('application/json', $accept)){
                $this->to_be_encoded = true;

                $this->data = $this->do_encode($this->data);
                $this->header('Content-type:application/json;charset=utf-8');
            }
        }

        $cli = (php_sapi_name() == 'cli');

        if (isset($this->data['error']) && !empty($this->data['error'])){
            // print_r('*'); // *

            $message  = $this->data['error']['message'] ?? '--';
            $type     = $this->data['error']['type'] ?? '--';
            $code     = $this->data['error']['code'] ?? '--';
            $detail   = $this->data['error']['detail'] ?? '--';
            $location = $this->data['error']['location'] ?? '--';

            if (is_array($detail)){
                $detail = json_encode($detail);
            }

            if (is_array($detail) || !$this->to_be_encoded){
                echo $this->do_encode($this->data);
            } else {
                echo "--| Error: \"$message\". -|Type: $type. -|Code: $code -| Location: $location -|Detail: $detail" .  PHP_EOL. PHP_EOL;
            }

        } else {
            if (is_array($this->data) && !$this->to_be_encoded){
                echo $this->do_encode($this->data);
            } else {
                echo $this->data;
            }
        }

        exit;
    }


    /*
        Ejecuta un callback cuano $cond es verdadero
    */
    function when($cond, $fn, ...$args){
        if ($cond){
            $fn($this, ...$args);
        }
        
        return $this;
    }

    /*
        Formatea la respuesta y solo envia el http status code

        $extra esta disponible para adiciones a la respuesta
        
        Ej:

        return Response::format($products, 200, '', [
            'paginator' => [
                'total' => $total_rows
            ]
        ]);

        Respuesta (ejemplo)

        {
           ...
            "status_code": 200,
            "error": "",
            "paginator": {
                "total": "218"
            }
        }
    */
    static function format($data, $http_code = 200, $error_msg = '', $extra = []){
        // Combina los arrays manteniendo las claves asociativas
        return array_merge([
            'data' => $data, 
            'status_code' => $http_code,
            'error' => $error_msg
        ], $extra);
    }

    function status(int $http_code)
    {
        if (php_sapi_name() != 'cli' && !headers_sent()) {
            $httpCodeMsg = $this->http_code_msg;
            header(trim('HTTP/' . $this->version . ' ' . $http_code . ' ' . $httpCodeMsg));
        }

        $this->http_code = $http_code;
        return $this;
    }

    function json($data)
    {
        $this->to_be_encoded = true;

        $response = [
            'data' => $data,
            'status_code' => $this->http_code ?? 200,
            'error' => []
        ];

        $this->set($response);
        return $this;
    }

    static function formatError($error_msg, $error_code = null){
        return [
            'message' => $error_msg,
            'code'    => $error_code
        ];
    }

    // ==================== PHASE 2: Immutable Methods (PSR-7 inspired) ====================

    /**
     * Return a new instance with the specified status code
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the instance, and MUST return a new instance with
     * the modified status code.
     *
     * @param int $code The 3-digit integer result code to set
     * @param string $reasonPhrase The reason phrase to use (optional)
     * @return self A new instance with the specified status code
     */
    public function withStatus(int $code, string $reasonPhrase = '')
    {
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException('Invalid HTTP status code');
        }

        $new = clone $this;
        $new->http_code = $code;
        $new->http_code_msg = $reasonPhrase;

        return $new;
    }

    /**
     * Return a new instance with the specified header
     *
     * @param string $name Header name
     * @param string|string[] $value Header value(s)
     * @return self A new instance with the specified header
     */
    public function withHeader(string $name, $value)
    {
        $new = clone $this;

        if ($new->headers === null) {
            $new->headers = [];
        }

        $headerString = is_array($value) ? implode(', ', $value) : $value;
        $new->headers[] = "$name: $headerString";

        return $new;
    }

    /**
     * Return a new instance with the specified added header value
     *
     * @param string $name Header name
     * @param string|string[] $value Header value(s) to add
     * @return self A new instance with the added header value
     */
    public function withAddedHeader(string $name, $value)
    {
        $new = clone $this;

        if ($new->headers === null) {
            $new->headers = [];
        }

        $headerString = is_array($value) ? implode(', ', $value) : $value;
        $new->headers[] = "$name: $headerString";

        return $new;
    }

    /**
     * Return a new instance without the specified header
     *
     * @param string $name Header name to remove
     * @return self A new instance without the specified header
     */
    public function withoutHeader(string $name)
    {
        $new = clone $this;

        if ($new->headers !== null) {
            $new->headers = array_filter($new->headers, function($header) use ($name) {
                return stripos($header, $name . ':') !== 0;
            });
            $new->headers = array_values($new->headers); // Re-index array
        }

        return $new;
    }

    /**
     * Return a new instance with the specified body
     *
     * @param mixed $body The new body data
     * @return self A new instance with the specified body
     */
    public function withBody($body)
    {
        $new = clone $this;
        $new->data = $body;
        return $new;
    }

    /**
     * Return a new instance with JSON body and appropriate headers
     *
     * Convenience method for creating JSON responses
     *
     * @param mixed $data The data to encode as JSON
     * @param int $status HTTP status code
     * @return self A new instance with JSON body
     */
    public function withJson($data, int $status = 200)
    {
        $new = clone $this;
        $new->http_code = $status;
        $new->to_be_encoded = true;
        $new->data = $data;

        // Add Content-Type header
        if ($new->headers === null) {
            $new->headers = [];
        }
        $new->headers[] = 'Content-Type: application/json';

        return $new;
    }
}