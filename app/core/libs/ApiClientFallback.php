<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Url;
use simplerest\core\libs\Utils;
use simplerest\core\libs\Logger;
use simplerest\core\libs\FileCache;
use simplerest\core\Exception;

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

    function disableSSL()
    {
        $this->cert_ssl = false;
        
        if ($this->useCurl) {
            $this->setOption(CURLOPT_SSL_VERIFYHOST, 0);
            $this->setOption(CURLOPT_SSL_VERIFYPEER, 0);
        }
        
        return $this;
    }

    function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    protected function consumeAPI($url, $http_verb, $data, $headers, $options)
    {
        if ($this->useCurl) {
            return $this->curlRequest($url, $http_verb, $data, $headers, $options);
        }
        
        return $this->fallbackExec($url, $http_verb, $data, $headers, $options);
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

    // Métodos HTTP simplificados
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

    public function getResponse()
    {
        return $this->response;
    }

    public function getContentType()
    {
        return $this->content_type;
    }
}