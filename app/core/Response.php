<?php

namespace simplerest\core;

use simplerest\core\libs\Config;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Url;

class Response
{
    static protected $data;
    static protected $to_be_encoded;
    static protected $headers = []; 
    static protected $http_code = NULL;
    static protected $http_code_msg = '';
    static protected $instance = NULL;
    static protected $version = '2';
    static protected $config;
    static protected $pretty;
    static protected $paginator_params;
    static protected $as_object = false;
    static protected $fake_status_codes = false; // send 200 instead
    static protected $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;


    protected function __construct() { 
        static::$config = Config::get();
        static::$pretty = static::$config['pretty'];
    }

    public function __destruct()
    {
        DB::closeAllConnections();
    }    

    static function getInstance(){        
        if(static::$instance == NULL){
            static::$instance = new static();
        }
        return static::$instance;
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
        static::$as_object = $val;
    }

    function addHeaders(array $headers)
    {
        static::$headers = $headers;
        return static::getInstance();
    }
  
    function addHeader(string $header)
    {
        static::$headers[] = $header;
        return static::getInstance();
    }

    // Alias de addHeader()
    function setHeader(string $header){
        return static::addHeader($header);
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
        static::$http_code_msg = $msg;
        static::$http_code = $http_code;
        return static::getInstance();
    }

    function setPretty(bool $state){
        static::$pretty = $state;
        return static::getInstance();
    }

    protected function do_encode($data)
    {       
        $this->header('Content-type:application/json;charset=utf-8');

        $options = static::$pretty ? static::$options | JSON_PRETTY_PRINT : static::$pretty;
        
        return json_encode($data, $options);  
    }

    function encode(){
        static::$to_be_encoded = true;
        return static::getInstance();
    }

    function setPaginatorParams($row_count, $count, $current_page, $page_count, $page_size, $nextUrl){
        $formatter = Config::get()['paginator']['formatter'];
       
        static::$to_be_encoded    = true; 

        static::$paginator_params = $formatter(
            $row_count, $count, $current_page, $page_count, $page_size, $nextUrl
        );

        return static::getInstance();
    }

    function send($data, $http_code = NULL){
        if ($http_code >= 400) {
            return $this->error($data, $http_code);
        }

        $http_code = $http_code != NULL ? $http_code : (static::$http_code !== null ? static::$http_code : 200);

        if (php_sapi_name() != 'cli' && !headers_sent()) {
            header(trim('HTTP/'.static::$version.' '.$http_code.' '.static::$http_code_msg));
        }    

        if (static::$as_object || is_object($data) || is_array($data)) {
            $arr = [];

            if (static::$config['paginator']['position'] == 'TOP'){
                if (static::$paginator_params != NULL){
                    $arr = array_merge($arr, static::$paginator_params);
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

            static::$http_code = $http_code; //

            if (static::$config['paginator']['position'] == 'BOTTOM'){                
                if (static::$paginator_params != NULL){
                    $data = array_merge($data, static::$paginator_params);
                }
            }          
        }     

        static::$instance->set( $data );
        return static::$instance;   	
    }

    private function zip($data){
        $data  = gzcompress($data, 9);

        ob_start("ob_gzhandler");
        echo $data; 
        ob_end_flush();
    } 

    function sendCode(int $http_code){
        static::$instance->set( json_encode(['status_code' => $http_code]) );
          
        if (!static::$fake_status_codes){    
            http_response_code($http_code);
        }   

        static::$http_code = $http_code; //
        
        return static::$instance; 
    }
 

    function sendOK(){
        if (!headers_sent()) {
            http_response_code(200);
        }
        
        return static::$instance; 
    }

    // send as JSON
    function sendJson($data, $http_code = null, ?string $error_msg = null){
        $http_code = $http_code != null ? $http_code : (static::$http_code !== null ? static::$http_code : 200);
        
        self::$to_be_encoded = true; 

        if (php_sapi_name() != 'cli' && !headers_sent()) {
            header(trim('HTTP/'.static::$version.' '.$http_code.' '.static::$http_code_msg));
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

        static::$http_code = $http_code; //

        static::$instance->set($res);

        return static::$instance; 
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
                    if (static::$http_code != NULL)
                        $http_code = static::$http_code;
                    else
                        $http_code = 500;
        
                if ($http_code != NULL && !static::$fake_status_codes)
                    header(trim('HTTP/'.static::$version.' '.$http_code.' '.static::$http_code_msg));
            }   
        }
        
        static::$http_code = $http_code; //
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
       
        static::$instance->set($res);  
        static::$instance->flush();

        exit;
    }

    function set($data){        
        static::$data = $data;
        return static::$instance; 
    }

    function get(){ 
        // Parche aplicado el 14-Nov-2022
        
        if (is_array(static::$data)){
            $this->header('Content-type:application/json;charset=utf-8');
            return json_encode(static::$data);
        }
        
        return static::$data; 
    }

    function __toString()
    {
        return $this->get();
    }

    function isEmpty(){
        return static::$data == null;
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

        // var_dump(static::$data);

        if (self::$to_be_encoded){
            static::$data = $this->do_encode(static::$data);
            $this->header('Content-type:application/json;charset=utf-8');
        } else {
            $accept = request()->header('Accept');

            if (Strings::startsWith('application/json', $accept)){
                self::$to_be_encoded = true;

                static::$data = $this->do_encode(static::$data);
                $this->header('Content-type:application/json;charset=utf-8');
            }
        }

        $cli = (php_sapi_name() == 'cli');

        if (isset(static::$data['error']) && !empty(static::$data['error'])){
            // print_r('*'); // *

            $message  = static::$data['error']['message'] ?? '--';
            $type     = static::$data['error']['type'] ?? '--';
            $code     = static::$data['error']['code'] ?? '--';
            $detail   = static::$data['error']['detail'] ?? '--';
            $location = static::$data['error']['location'] ?? '--';

            if (is_array($detail)){
                $detail = json_encode($detail);
            }
            
            if (is_array($detail) || !self::$to_be_encoded){
                echo $this->do_encode(static::$data);
            } else {
                echo "--| Error: \"$message\". -|Type: $type. -|Code: $code -| Location: $location -|Detail: $detail" .  PHP_EOL. PHP_EOL;
            }
            
        } else {
            if (is_array(static::$data) && !self::$to_be_encoded){
                echo $this->do_encode(static::$data);
            } else {
                echo static::$data; 
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

    static function formatError($error_msg, $error_code = null){
        return [
            'message' => $error_msg,
            'code'    => $error_code
        ];
    }
}