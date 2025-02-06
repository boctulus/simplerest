<?php

namespace Boctulus\ApiClient;

use Boctulus\ApiClient\Helpers\XML;
use Boctulus\ApiClient\Helpers\Url;
use Boctulus\ApiClient\Helpers\Utils;
use Boctulus\ApiClient\Helpers\Logger;
use Boctulus\ApiClient\Helpers\Strings;
use Boctulus\ApiClient\Helpers\FileCache;
use Boctulus\ApiClient\Exception;
use Boctulus\ApiClient\Interface\ICache;

class ApiClientFallback
{
    /*
        User Agents
    */
    const USER_AG_FIREFOX = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:98.0) Gecko/20100101 Firefox/98.0';
    const USER_AG_SAFARI  = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.3 Safari/605.1.15';
    const USER_AGT_EDGE   = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.79 Safari/537.36 Edg/100.0.4896.79';
    const USER_AG_CHROME  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.75 Safari/537.36';
    const USER_AG_POSTMAN = 'PostmanRuntime/7.34.0';


    // Cookies
    protected $cookieJar;
    protected $curl;

    // Request
    protected $url;
    protected $verb;
    protected $req_headers;
    protected $options = [];
    protected $body;
    protected $req_body;
    protected $encode_body;
    protected $max_retries = 1;
    protected $cert_ssl  = null;

    // Username & password
    protected $username;
    protected $password;

    // Response
    protected $raw_response;
    protected $response;

    protected $filename;
    protected $res_headers;
    protected $auto_decode;
    protected $status;
    protected $ignore_status_codes = [];
    protected $error;

    // Response Info
    protected $effective_url;
    protected $content_type;

    // Cache
    protected $expiration;
    protected $read_only = false;
    protected $cache_post_request = false;

    // Mock
    protected $mocked;

    // Logs
    protected $log_req    = false;
    protected $log_res    = false;
    protected $logger_fn  = 'log';

    // Extras
    protected $query_params = [];
    protected $debug   =  false;
    protected $show_req = false;
    protected $show_res = false;
    protected $useCurl;


    function __construct($url = null)
    {        
        $this->useCurl = function_exists('curl_init');
        // $this->useCurl = false;
        
        if ($url !== null) {
            $this->setUrl($url);
        }

        if ($this->useCurl) {
            $this->curl = curl_init();
        } else {
            require_once __DIR__ . '/Constants/Curl.php';
        }
    }

    function getRawResponse(){
        return $this->raw_response;
    }


    // alias de getStatus()
    function status(){
        return $this->getStatus();
    }

    // alias de getError()
    function error(){
        return $this->error;
    }


    function data(bool $raw = false){
        if ($raw === false){
            if ($this->auto_decode && Strings::isJSON($this->response)){
                return json_decode($this->response, true);
            }
        }

        return $raw ? $this->raw_response : $this->response;
    }

    function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    protected function curlRequest($url, $http_verb, $data, $headers = null, $options = null)
    {
        // Configuración base para cURL
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $http_verb,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HEADERFUNCTION => function ($curl, $header) use (&$resHeaders) {
                $this->parseHeader($header);
                return strlen($header);
            }
        ];

        // Agregar headers si no son null
        if ($headers !== null) {
            $curlOptions[CURLOPT_HTTPHEADER] = $this->buildHeaders($headers);
        }

        // Combinar opciones adicionales si existen
        if (is_array($options)) {
            $curlOptions += $options;
        }

        curl_setopt_array($this->curl, $curlOptions);

        $response = curl_exec($this->curl);
        $this->error = curl_error($this->curl);
        $this->status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->content_type = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);
        $this->effective_url = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);

        return $response;
    }


    protected function fallbackExec($url, $http_verb, $data, $headers, $options)
    {
        $curl = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'curl.exe' : 'curl';
        $cmd = [$curl, '-sSi', '-X', escapeshellarg($http_verb)];

        // Headers
        foreach ($this->buildHeaders($headers) as $header) {
            $cmd[] = '-H';
            $cmd[] = escapeshellarg($header);
        }

        // Body
        if (!empty($data)) {
            $cmd[] = '--data';
            $cmd[] = escapeshellarg($data);
        }

        // SSL
        if ($this->cert_ssl === false) {
            $cmd[] = '--insecure';
        } elseif (isset($options[CURLOPT_CAINFO])) {
            $cmd[] = '--cacert';
            $cmd[] = escapeshellarg($options[CURLOPT_CAINFO]);
        }

        // Timeouts
        if (isset($options[CURLOPT_TIMEOUT])) {
            $cmd[] = '--max-time';
            $cmd[] = $options[CURLOPT_TIMEOUT];
        }

        $cmd[] = escapeshellarg($url);

        exec(implode(' ', $cmd) . ' 2>&1', $output, $exitCode);
        
        return $this->parseCurlOutput($output, $exitCode);
    }

    protected function parseCurlOutput($output, $exitCode)
    {
        $raw = implode("\n", $output);
        
        // Detectar tipo de retorno de carro usado
        $crlf = Strings::carriageReturn($raw);
        $separator = $crlf . $crlf; // Separador de headers/body
        
        $parts = explode($separator, $raw, 2);
        
        // Parse headers
        $headers = isset($parts[0]) ? explode($crlf, $parts[0]) : [];
        foreach ($headers as $header) {
            $this->parseHeader($header);
        }

        $this->status = $this->extractStatusCode($headers);
        $this->raw_response = isset($parts[1]) ? $parts[1] : '';
        $this->error = $exitCode !== 0 ? "cURL CLI error (Code $exitCode)" : '';

        return $this->raw_response;
    }

    protected function parseHeader($header)
    {
        if (strpos($header, 'Content-Type:') === 0) {
            $this->content_type = trim(substr($header, 13));
        }
    }

    protected function extractStatusCode($headers)
    {
        foreach ($headers as $header) {
            if (preg_match('/HTTP\/\d\.\d (\d+)/', $header, $matches)) {
                return (int)$matches[1];
            }
        }
        return 0;
    }

    function getRequestHeaders(){
        return $this->req_headers;
    }

    function getHeaders(){
        return $this->res_headers;
    }

    function getEffectiveUrl(){
        return $this->effective_url;
    }

    protected function consumeAPI($url, $http_verb, $data, $headers, $options)
    {
        if ($this->useCurl) {
            return $this->curlRequest($url, $http_verb, $data, $headers, $options);
        }
        
        return $this->fallbackExec($url, $http_verb, $data, $headers, $options);
    }

    public function followLocations($max_redirs = 10)
    {
        if ($this->useCurl) {
            $this->setOption(CURLOPT_FOLLOWLOCATION, true);
            $this->setOption(CURLOPT_MAXREDIRS, $max_redirs);
        } else {
            $this->options['max_redirs'] = $max_redirs;
        }
        return $this;
    }

    
    public function readOnly(bool $flag = true)
    {
        $this->read_only = $flag;
        return $this;
    }

    public function ignoreStatusCodes(array $codes)
    {
        $this->ignore_status_codes = $codes;
        return $this;
    }


   protected function buildHeaders($headers)
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "$key: $value";
        }
        return $formatted;
    }

    public function request($url, $http_verb, $body = null, $headers = null, $options = null)
    {
        $this->url = $url ?? $this->url;
        $this->verb = strtoupper($http_verb);
        $this->req_headers = $headers ?? $this->req_headers;
        $this->options = $options ?? $this->options;

        if ($this->encode_body && is_array($body)) {
            $body = json_encode($body);
        }

        $retries = 0;
        $ok = false;

        if ($this->expiration == null){
            $expired = true;
        } else {
            $cached_path     = $this->getCachePath();
            $expired         = is_file($cached_path) ? FileCache::expired(filemtime($cached_path), $this->expiration) : true;  // fixex on jun-17/24
        }

        if (!$expired){
            $res = $this->getCache();

            if ($res !== null){
                if (is_string($res)){
                    //dd('DECODING...');
                    if (Strings::isJSON($res)){
                        $data = json_decode($res['data'], true); 

                        if ($data !== null){
                            //throw new \Exception("Unable to decode response '$res'");
                            $res['data'] = $data;
                        } else {
                            //dd('DECODED!');
                        }
                    }                        
                }
                
                // Solo sino hay errores (hago un return y con eso) evito continuar obteniendo una respuesta fresca
                if (empty($res['error']))
                {    
                    $this->status   = $res['http_code'] ?? 200;
                    $this->error    = $res['error'] ?? '';
                    $this->response = $res['data'] ?? $res;


                    return $this;
                }
            }
        }

        while (!$ok && $retries < $this->max_retries) {
            $this->raw_response = $this->consumeAPI(
                $this->url,
                $this->verb,
                $body,
                $this->req_headers,
                $this->options
            );

            $ok = empty($this->error);
            $retries++;
        }

        if ($this->expiration && $this->raw_response !== null && !$this->read_only){
            if (!empty($this->ignore_status_codes)){
                foreach ($this->ignore_status_codes as $code){
                    if ($this->status == $code){
                        $this->saveResponse($this->raw_response);
                        break;
                    }
                }
            } else if ($this->status >=200 && $this->status < 400){
                $this->saveResponse($this->raw_response);
            }            
        }       

        $this->response = $this->raw_response;
        return $this;
    }

    // Métodos HTTP
    public function get($url = null, $headers = null, $options = null)
    {
        return $this->request($url ?? $this->url, 'GET', null, $headers, $options);
    }

    public function post($url = null, $body = null, $headers = null, $options = null)
    {
        return $this->request($url ?? $this->url, 'POST', $body, $headers, $options);
    }

    public function put($url = null, $body = null, $headers = null, $options = null)
    {
        return $this->request($url ?? $this->url, 'PUT', $body, $headers, $options);
    }

    public function delete($url = null, $headers = null, $options = null)
    {
        return $this->request($url ?? $this->url, 'DELETE', null, $headers, $options);
    }

    // Métodos de acceso a resultados
    public function getStatus()
    {
        return $this->status;
    }

    public function getError()
    {
        return $this->error;
    }

    function getResponse($decode = null, $as_array = null){       
        if ($decode === null){
            $decode = $this->auto_decode;
        }

        if ($as_array == null){
            $as_array = true;
        }

        $data = $this->response;

        // dd($this->content_type. 'CONTENT TYPE');        

        if ((!empty($this->content_type) && Strings::startsWith('application/json', $this->content_type)) || ($decode && Strings::isJSON($data))){
            $data = json_decode($this->response, $as_array);
        } else 
        
        if ((!empty($this->content_type) && Strings::containsAny(['/xml', '+xml'], $this->content_type))  || ($decode && XML::isValidXML($data))){
            $data = XML::toArray($data);
        }
        
        $res = [
            'data'      => $data,
            'http_code' => $this->status,
            'error'     => $this->error
        ];

        return $res;
    }


    public function getContentType()
    {
        return $this->content_type;
    }

    function setUrl($url)
    {
        $this->url = Url::normalize($url);
        return $this;
    }

    function setHeaders(array $headers)
    {
        $this->req_headers = $headers;
        return $this;
    }

    function setBody($body, $encoded = true)
    {
        $this->body = $body;
        $this->encode_body = $encoded;
        return $this;
    }

    function setDecode(bool $auto = true){
        $this->auto_decode = $auto;
        return $this;
    }

    // alias
    function decode(bool $val = true){
        return $this->setDecode($val);
    }

    function noDecode(){
        return $this->setDecode(false);
    }

    function disableSSL()
    {
        $this->cert_ssl = false;
        
        if ($this->useCurl) {
            $this->setOption(CURLOPT_SSL_VERIFYHOST, 0);
            $this->setOption(CURLOPT_SSL_VERIFYPEER, 0);
        }
        
        return $this;
    }

    function getFilename(){
        return $this->filename;
    }

    /*
        CACHE

        En vez de guardar en disco..... usar Transientes con drivers como Memcached o REDIS !

        Debe generar un HASH con todos los parametros y sino son iguales... se considera otra cache
    */

    function getCachePath(){
        if (empty($this->url)){
            throw new \Exception("Undefined URL");
        }

        $input = str_replace(['https://', 'http://'], '', $this->url);

        if ($this->cache_post_request && $this->verb == 'POST'){
            if (is_array($this->req_body)){
                $this->req_body = md5(json_encode($this->req_body));
            }

            $input .= "+body={$this->req_body}";
        }

        $full_path = FileCache::getCachePath($input);

        return $full_path;
    }

	protected function saveResponse($response)
    {
        if ($this->cache_post_request === false && $this->verb != 'GET'){
            return;
        }

        $path = $this->getCachePath();

        if ($path === null){
            return;
        }

        file_put_contents($path, '<?php return ' . var_export($response, true) . ';');
    }

    protected function getCache(){
        $path = $this->getCachePath();

        if ($path === null){
            return;
        }

        if (file_exists($path)){
            if ($this->debug){
                dd($path, 'CACHE PATH');
            }

            return include $path;
        }
    }

    /*
        @param $expiration_time int seconds 
    */
    function setCache(int $expiration_time = 60){
        $this->expiration = $expiration_time;
        return $this;
    }

    // alias de setCache()
    function cache(int $expiration_time = 60){
        return $this->setCache($expiration_time);
    }

    /*
        Revisar. No funcionaria bien
    */
    function cacheUntil(string $datetime){
        $diffInSeconds = function(string $date2, string $date1 = '') {
            $d1 = new \DateTime($date1);
            $d2 = new \DateTime($date2);
        
            return $d2->getTimestamp() - $d1->getTimestamp();
        };

        $expiration_time = $diffInSeconds($datetime);

        // dd($expiration_time, 'EXP TIME (SECS)');

        return $this->setCache($expiration_time);
    }

    /*
        Que diferencia hay con FileCache::forget($this->url) ????
    */
    function clearCache(){
        unlink($this->getCachePath());
        return $this;
    }

    function enablePostRequestCache(){
        $this->cache_post_request = true;
        return $this;
    }

}