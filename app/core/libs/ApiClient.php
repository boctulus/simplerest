<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Url;

/*
    Wrapper para Url::consume_api()

    @author Pablo Bozzolo

    Ver interesante uso de cURL aqui:
    
    https://stackoverflow.com/a/42375193/980631
    https://stackoverflow.com/a/20605980/980631
*/
class ApiClient
{
    protected $url;
    protected $verb;
    protected $headers;
    protected $options = [];
    protected $body;
    protected $encode_body;
    protected $auto_decode;
    protected $status;
    protected $errors;
    protected $response;
    protected $expiration;
    protected $max_retries = 1;

    function setUrl($url){
        $this->url = $url;
        return $this;
    }
    
    // alias
    function url($url){
        return $this->setUrl($url);
    }

    function __construct($url = null)
    {
        $this->setUrl($url);
    }

    function setHeaders(Array $headers){
        $this->headers = $headers;
        return $this;
    }

    function setOption($key, $val){
        $this->options[$key] = $val;
        return $this;
    }

    // alias
    function option($key, $val){
        $this->options[$key] = $val;
        return $this;
    }

    function setOptions(Array $options){
        $this->options = $options;
        return $this;
    }

    function addOptions(Array $options){
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    // redirect
    function followLocations($max_redirs = 10){
        $options = [];

        $this->options[CURLOPT_FOLLOWLOCATION] = ($max_redirs > 0);
        $this->options[CURLOPT_MAXREDIRS] = $max_redirs;

        return $this;
    }

    // alas
    function redirect($max_redirs = 10){
        return $this->followLocations($max_redirs);
    }

    function setBody($body, $encoded = true){
        $this->body = $body;
        $this->encode_body = $encoded;
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
    function setCache(int $expiration_time = 60){
        $this->expiration = $expiration_time;
        return $this;
    }

    function cache(int $expiration_time = 60){
        return $this->setCache($expiration_time);
    }

    function getStatus(){
        return $this->status;
    }

    function getErrors(){
        return $this->errors;
    }

    function getResponse(bool $decode = true, bool $as_array = true){       
        // deberia $decode poder venir en null y en tal caso usar $this->auto_decode
        
        if ($decode){
            $data = json_decode($this->response, $as_array);
        } else {
            $data = $this->response;
        }   

        $res = [
            'data' => $data,
            'http_code' => $this->status,
            'errors' => $this->errors
        ];

        return $res;
    }

    function setRetries($qty){
        $this->max_retries = $qty;
        return $this;
    }

    function disableSSL(){
        $this->options = [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ];

        return $this;
    }

    /*
        Set SSL certification
    */
    function setSSLCrt($crt_path){
        $this->addOptions([
            CURLOPT_CAINFO => $crt_path,
            CURLOPT_CAPATH => $crt_path,
        ]);
        
        return $this;
    }

    function request(string $url, string $http_verb, $body = null, ?Array $headers = null, ?Array $options = null){
        $this->url  = $url;
        $this->verb = strtoupper($http_verb);

        if (!empty($this->options) && !empty($options)){
            $options = array_merge($this->options, $options);
        } else {
            $options = $options ?? $this->options ?? null;
        }

        $body    = $body    ?? $this->body    ?? null;
        $headers = $headers ?? $this->headers ?? null;        
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

                return $this;
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
            $res = Url::consume_api($url, $http_verb, $body, $headers, $options, false, $this->encode_body);
            $this->status   = $res['http_code'];
            $this->errors   = $res['error'];
            $this->response = $res['data'];

            d($res['content_type'], 'CONTENT TYPE');
            d($res['effective_url'], 'EFFECTIVE URL');

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

        // dd($res, 'RES');

        if ($this->expiration){
            $this->saveResponse($res);
        }

        return $this;
    }

    function get($url = null, ?Array $headers = null, ?Array $options = null){        
        return $this->request($this->url ?? $url, 'GET', null, $headers, $options);
    }

    function delete($url = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($this->url ?? $url, 'DELETE', null, $headers, $options);
    }

    function post($url = null, $body = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($this->url ?? $url, 'POST', $body, $headers, $options);
    }

    function put($url = null, $body = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($this->url ?? $url, 'PUT', $body, $headers, $options);
    }

    function  patch($url = null, $body = null, ?Array $headers = null, ?Array $options = null){
        return $this->request($this->url ?? $url, 'PATCH', $body, $headers, $options);
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

        // Evito problemas con nombres largos
        if (strlen($filename) > 250){
            return null;
        }

        $path[$this->url] = sys_get_temp_dir() . '/' . $filename;
        return $path[$this->url];
    }
 
	protected function saveResponse(Array $response){
        if ($this->verb != 'GET'){
            return;
        }

        $path = $this->getCachePath();

        if ($path === null){
            return;
        }

        file_put_contents($path, '<?php $res = ' . var_export($response, true) . ';');
    }

    protected function getCache(){
        $path = $this->getCachePath();

        if ($path === null){
            return;
        }

        if (file_exists($path)){
            include $path;
            return $res;
        }
    }

    /*

        Tomado de CodeIgniter ---------------------------------------->

    */

    public function simple_ftp_get($url, $file_path, $username = '', $password = '')
	{
		// If there is no ftp:// or any protocol entered, add ftp://
		if ( ! preg_match('!^(ftp|sftp)://! i', $url))
		{
			$url = 'ftp://' . $url;
		}

		// Use an FTP login
		if ($username != '')
		{
			$auth_string = $username;

			if ($password != '')
			{
				$auth_string .= ':' . $password;
			}

			// Add the user auth string after the protocol
			$url = str_replace('://', '://' . $auth_string . '@', $url);
		}

		// Add the filepath
		$url .= $file_path;

		//$this->option(CURLOPT_BINARYTRANSFER, TRUE);
		$this->option(CURLOPT_VERBOSE, TRUE);

		return $this->get();
	}

	/* =================================================================================
	 * ADVANCED METHODS
	 * Use these methods to build up more complex queries
	 * ================================================================================= */

	public function setCookies($params = array())
	{
		if (is_array($params))
		{
			$params = http_build_query($params, '', '&');
		}

		$this->option(CURLOPT_COOKIE, $params);
		return $this;
	}

	public function httpHeader($header, $content = NULL)
	{
		$this->headers[] = $content ? $header . ': ' . $content : $header;
		return $this;
	}

	public function httpMethod($method)
	{
		$this->options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
		return $this;
	}

	public function httpLogin($username = '', $password = '', $type = 'any')
	{
		$this->option(CURLOPT_HTTPAUTH, constant('CURLAUTH_' . strtoupper($type)));
		$this->option(CURLOPT_USERPWD, $username . ':' . $password);
		return $this;
	}

	public function proxy($url = '', $port = 80)
	{
		$this->option(CURLOPT_HTTPPROXYTUNNEL, TRUE);
		$this->option(CURLOPT_PROXY, $url . ':' . $port);
		return $this;
	}

	public function proxyLogin($username = '', $password = '')
	{
		$this->option(CURLOPT_PROXYUSERPWD, $username . ':' . $password);
		return $this;
	}

	public function isEnabled()
	{
		return function_exists('curl_init');
	}


}

