<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Url;

/*
    Wrapper para Url::consume_api()

    @author Pablo Bozzolo
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
    protected $max_retries = 1;

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

    function setDecode(bool $auto = true){
        $this->auto_decode = $auto;
        return $this;
    }

    // alias
    function decode(bool $val){
        return $this->setDecode($val);
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
        if ($decode && $this->response != null){
            $res = json_decode($this->response, $as_array);
        } else {
            $res = null;
        }   

        return $res;
    }

    function setRetries($qty){
        $this->max_retries = $qty;
        return $this;
    }

    function request(string $url, string $http_verb, $body = null, ?Array $headers = null, ?Array $options = null){
        $this->url  = $url;
        $this->verb = strtoupper($http_verb);

        $body    = $body    ?? $this->body    ?? null;
        $headers = $headers ?? $this->headers ?? null;
        $options = $options ?? $this->options ?? null;
        $decode  = $this->auto_decode; 

        if ($this->expiration){
            $res = $this->getCache();

            if ($res !== null){
                if (is_string($res)){
                    //dd('DECODING...');
                    $data = json_decode($res['data'], true); 
                    
                    if ($data !== null){
                        //throw new \Exception("Unable to decode response '$res'");
                        $res['data'] = $data;
                    } else {
                        //dd('DECODED!');
                    }
                }
                
                $this->status   = $res['http_code'];
                $this->errors   = $res['error'];
                $this->response = $res['data'];
                return;
            }
        }

        $ok = null;
        $retries = 0;

        /*
            Con cada intento podría ir incrementando el tiempo máximo para conectar y para obtener resultados
            Esos valores ¨optimos¨ podrían persistirse en un transiente para la url 
        */
        while (!$ok && $retries < $this->max_retries)
        {   
            $res = Url::consume_api($url, $http_verb, $body, $headers, $options, false);
            $this->status   = $res['http_code'];
            $this->errors   = $res['error'];
            $this->response = $res['data'];

            /*
                Si hay errores && el status code es 0 
                =>
                Significa que fall'o la conexion!

                --| STATUS
                0

                --| ERRORS
                Failed to connect to 200.6.78.1 port 80: Connection refused

                --| RES
                NULL
            */

            $ok = empty($this->errors);
            $retries++;

            //d($ok ? 'ok' : 'fail', 'INTENTOS: '. $retries);
        }

        if ($ok && $this->expiration){
            $this->saveResponse($res);
        }
    }

    function get(string $url, $body = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($url, 'GET', $body, $headers, $options);
    }

    function post(string $url, $body = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($url, 'POST', $body, $headers, $options);
    }

    function put(string $url, $body = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($url, 'PUT', $body, $headers, $options);
    }

    function  patch(string $url, $body = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($url, 'PATCH', $body, $headers, $options);
    }

    function delete(string $url, $body = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($url, 'DELETE', $body, $headers, $options);
    }

    /*
        Authentication
    */

    // BASIC
    function setBasicAuth($username, $password){
        $this->setHeaders([
            'Authorization: Basic '. base64_encode("$username:$password")
        ]);

        return $this;
    }

    // JWT
    function setJWTAuth($token_jwt){
        $this->setHeaders([
            "Authorization: Bearer $token_jwt"
        ]);

        return $this;
    }


    /*
        CACHE

        En vez de guardar en disco..... usar Transientes con drivers como Memcached o REDIS !
    */

    protected function getCachePath(){
        static $path;

        if (isset($path[$this->url])){
            return $path[$this->url];
        }

        $filename = str_replace(['%'], ['p'], urlencode(Url::normalize($this->url))) . '.php';
        $filename = str_replace('/', '', $filename);

        $path[$this->url] = sys_get_temp_dir() . '/' . $filename;
        return $path[$this->url];
    }
 
	protected function saveResponse(Array $response){
        if ($this->verb != 'GET'){
            return;
        }

        $path = $this->getCachePath();

        file_put_contents($path, '<?php $res = ' . var_export($response, true) . ';');
    }

    protected function getCache(){
        $path = $this->getCachePath();

        //dd($path, 'PATH CACHE');
        //exit;

        if (file_exists($path)){
            include $path;
            return $res;
        }

        //return null;
    }

}

