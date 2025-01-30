<?php

namespace Boctulus\ApiClient;

use Boctulus\ApiClient\Helpers\Url;
use Boctulus\ApiClient\Helpers\Utils;
use Boctulus\ApiClient\Helpers\Logger;
use Boctulus\ApiClient\Helpers\Strings;
use Boctulus\ApiClient\Helpers\FileCache;
use Boctulus\ApiClient\Exception;

class ApiClientFallback
{
    const HTTP_METH_POST   = "POST";
    const HTTP_METH_GET    = "GET";
    const HTTP_METH_PATCH  = "PATCH";
    const HTTP_METH_PUT    = "PUT";
    const HTTP_METH_DELETE = "DELETE";

    protected $url;
    protected $verb;
    protected $req_headers = [];
    protected $options = [];
    protected $body;
    protected $encode_body = true;
    protected $max_retries = 1;
    protected $cert_ssl = null;
    
    protected $raw_response;
    protected $response;
    protected $status;
    protected $error;
    protected $content_type;
    protected $effective_url;
    protected $auto_decode = true;
    
    // Cache
    protected $expiration;
    protected $read_only = false;
    protected $cache_post_request = false;
    protected $ignore_status_codes = [];
    
    protected $useCurl;
    protected $curl;

    function __construct($url = null)
    {
        $this->useCurl = function_exists('curl_init');
        
        if ($url !== null) {
            $this->setUrl($url);
        }

        if ($this->useCurl) {
            $this->curl = curl_init();
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

    protected function curlRequest($url, $http_verb, $data, $headers, $options)
    {
        curl_setopt_array($this->curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $http_verb,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $this->buildHeaders($headers),
            CURLOPT_HEADERFUNCTION => function($curl, $header) use (&$resHeaders) {
                $this->parseHeader($header);
                return strlen($header);
            }
        ] + $options);

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
        $parts = explode("\r\n\r\n", $raw, 2);
        
        // Parse headers
        $headers = isset($parts[0]) ? explode("\n", $parts[0]) : [];
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

    // Métodos de Cache
    public function setCache(int $expiration_time = 60)
    {
        $this->expiration = $expiration_time;
        return $this;
    }

    public function cache(int $expiration_time = 60)
    {
        return $this->setCache($expiration_time);
    }

    public function clearCache()
    {
        $path = $this->getCachePath();
        if (file_exists($path)) {
            unlink($path);
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

    protected function getCachePath()
    {
        $input = md5(serialize([
            $this->url,
            $this->verb,
            $this->req_headers,
            $this->body
        ]));

        return FileCache::getCachePath($input);
    }

    protected function shouldCache()
    {
        if ($this->expiration === null) return false;
        if ($this->read_only) return false;
        
        return in_array($this->status, $this->ignore_status_codes) || 
              ($this->status >= 200 && $this->status < 400);
    }

       protected function saveResponse(array $response)
    {
        $path = $this->getCachePath();
        file_put_contents($path, '<?php return ' . var_export($response, true) . ';');
    }

    protected function getCache()
    {
        $path = $this->getCachePath();
        if (file_exists($path)) {
            $cached = include $path;
            if (FileCache::expired(filemtime($path), $this->expiration)) {
                return null;
            }
            return $cached;
        }
        return null;
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
}