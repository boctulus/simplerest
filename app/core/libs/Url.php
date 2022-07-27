<?php declare(strict_types=1);

namespace simplerest\core\libs;

use simplerest\core\libs\ApiClient;

class Url
{
    static protected $headers;
    static protected $filename;
    static protected $effective_url;
    static protected $content_type;

    /*
        Obtiene la url final luego de redirecciones

        (no siempre funciona)

        https://stackoverflow.com/a/7555543/980631
    */
    static function getFinalUrl($url) {
        stream_context_set_default(array(
            'http' => array(
                'method' => 'HEAD'
            )
        ));

        $headers = get_headers($url, 1);

        if ($headers !== false && isset($headers['Location'])) {
            return $headers['Location'];
        }

        return false;
    }

    static function lastSlug(string $url){
		return $slug =  Strings::last(rtrim($url, '/'), '/');
	}

     /*
        Normaliza urls a fin de que así el "path" de la url termine o no con "/",
        queden sin era barra antes de la parte de queries con lo cual
        al momento de "cachear" no habrá duplicados.

        Ej:

        https://www.easyfarma.cl/categoria-producto/dermatologia/proteccion-solar/?page=2

        es convertido en

        https://www.easyfarma.cl/categoria-producto/dermatologia/proteccion-solar?page=2

    */
    static function normalize(string $url){
        if (!Strings::startsWith('http', $url)){
            throw new \InvalidArgumentException("Invalid url '$url'");
        }

        $p = parse_url($url);

        $p['path'] = rtrim($p['path'], '/');
        $query     = isset($p['query']) ? "?{$p['query']}" : '';

        return "{$p['scheme']}://{$p['host']}{$p['path']}$query";
    }

    // Body decode
    static function bodyDecode(string $data)
    {
        $headers  = apache_request_headers();
        $content_type = $headers['Content-Type'] ?? null;

        if (!empty($content_type)){
            // Podría ser un switch-case aceptando otros MIMEs

            if (Strings::contains('application/x-www-form-urlencoded', $headers['Content-Type'])){
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

    /*
		Patch for parse_str() native function

		It could be more efficient and precise if I use a preg_replace_callback and
		take note about which parameter was substituted
	*/
	static function parseStrQuery(string $s) : Array{
		$rep = '__DOT__';

		$s = str_replace('.', $rep, $s);

		parse_str($s, $result);

		foreach ($result as $k => $v){
			$pos = strpos($k, $rep);

			if ($pos !== false){
				$k2 =  str_replace($rep, '.', $k);
				$result[$k2] = $v;
				unset($result[$k]);
			} else {
                // parche 2022
                $result[$k] = str_replace($rep, '.', $result[$k]);
            }
		}

		return $result;
	}

	static function queryString() : Array {
        if (!isset($_SERVER['QUERY_STRING'])){
            return [];
        }

		return static::parseStrQuery($_SERVER['QUERY_STRING']);
	}

    static function has_ssl( $domain ) {
        $ssl_check = @fsockopen( 'ssl://' . $domain, 443, $errno, $errstr, 30 );
        $res = !! $ssl_check;
        if ( $ssl_check ) { fclose( $ssl_check ); }
        return $res;
    }

    static function http_protocol(){
        $config = config();

        if (isset($config['HTTPS']) && $config['HTTPS'] != null){
            if ($config['HTTPS'] == 1 || strtolower($config['HTTPS']) == 'on'){
                $protocol = 'https';
            } else {
                $protocol = 'http';
            }
        } else {
            $protocol = self::has_ssl($_SERVER['HTTP_HOST']) ? 'https' : 'http';
        }

        return $protocol;
    }

    /**
     * url_check - complement for parse_url
     *
     * @param  string $url
     *
     * @return bool
     */
    static function url_check(string $url){
        $sym = null;

        $len = strlen($url);
        for ($i=0; $i<$len; $i++){
            if ($url[$i] == '?'){
                if ($sym == '?' || $sym == '&')
                    return false;

                $sym = '?';
            }elseif ($url[$i] == '&'){
                if ($sym === null)
                    return false;

                $sym = '&';
            }
        }
        return true;
    }

    static function is_postman(){
        if (!isset($_SERVER['HTTP_USER_AGENT'])){
            return false;
        }

		return (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'PostmanRuntime') !== false);
	}

    static function is_insomnia(){
        if (!isset($_SERVER['HTTP_USER_AGENT'])){
            return false;
        }

        return Strings::startsWith('insomnia', $_SERVER['HTTP_USER_AGENT']);
    }

    /*
        Usar antes de parseStrQuery()
    */
    static function hasQueryParam(string $url, ?string $param = null){
        if ($param === null){
            return !empty(Strings::after($url, '?'));
        }

        $p = parse_url($url);

        if (!isset($p['query'])){
            return null;
        }

        $query_arr = static::parseStrQuery($p['query']);

        return isset($query_arr[$param]);
    }

    static function getQueryString(string $url){
        return Strings::after($url, '?');
    }

    /*
        Uso:

        Si se envia algo como 'http://google.com?x=3&y=4'

        retorna algo como:

        Array
        (
            [x] => 3
            [y] => 4
        )
    */
    static function getQueryParams(string $url){
        $str = static::getQueryString($url);

        if (empty($str)){
            return [];
        }

        parse_str($str, $output);

        return $output;
    }

    /*
        funcion auxiliar para parseStrQuery()
    */
    static function getQueryParam(string $url, ?string $param = null){
        if ($param === null){
            return static::getQueryParams($url);
        }

        $query = parse_url($url, PHP_URL_QUERY);

        $x = null;
        if ($query != null){
            $q = explode('&', $query);
            foreach($q as $p){
                if (Strings::startsWith($param . '=', $p)){
                    $_x = explode('=', $p);
                    $x  = $_x[count($_x)-1];
                }
            }
        }

        return $x;
    }

    static function currentUrl(){
        if (is_cli()){
            return '';
        }

        $actual_link = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $actual_link;
    }

    static function getBaseUrl(?string $url = null)
    {
        if (is_cli()){
            return '';
        }

        if (is_null($url)){
            $url = static::currentUrl();
        }

        $url_info = parse_url($url);
        $base_url = $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':'. $url_info['port'] : '');
        return  $base_url;
    }

    static function consume_api(string $url, string $http_verb, $body = null, ?Array $headers = null, ?Array $options = null, $decode = true, $encode_body = true)
    {
        if (!extension_loaded('curl'))
		{
            throw new \Exception("Extension curl no cargada");
        }

        if ($headers === null){
            $headers = [];
        } else {
            if (!Arrays::is_assoc($headers)){
                $_hs = [];
                foreach ($headers as $h){
                    list ($k, $v)= explode(':', $h, 2);
                    $_hs[$k] = $v;
                }

                $headers = $_hs;
            }
        }

        if ($options === null){
            $options = [];
        }

        $keys = array_keys($headers);

        $content_type_found = false;
        foreach ($keys as $key){
            if (strtolower($key) == 'content-type'){
                $content_type_found = $key;
                break;
            }
        }

        $accept_found = false;
        foreach ($keys as $key){
            if (strtolower($key) == 'accept'){
                $accept_found = $key;
                break;
            }
        }

        if (!$content_type_found){
            $headers = array_merge(
                [
                    'Content-Type' => 'application/json'
                ],
                ($headers ?? [])
            );
        }


        if ($accept_found) {
            if (Strings::startsWith('text/plain', $headers[$accept_found]) ||
                Strings::startsWith('text/html', $headers[$accept_found])){
                $decode = false;
            }
        }

        if ($encode_body && is_array($body)){
            $data = json_encode($body);
        } else {
            $data = $body;
        }

        $curl = curl_init();

        $http_verb = strtoupper($http_verb);

        if ($http_verb != 'GET' && !empty($data)){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            if ($encode_body){
                $headers['Content-Length']   = strlen($data);
            }
        }

        $h = [];
        foreach ($headers as $key => $header){
            $h[] = "$key: $header";
        }

        $options = [
            CURLOPT_HTTPHEADER => $h
        ] + ($options ?? []);

        curl_setopt_array($curl, $options);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '' );
        curl_setopt($curl, CURLOPT_TIMEOUT, 0 );

        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_verb);

        // https://stackoverflow.com/a/6364044/980631
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_HTTP200ALIASES, [
            400,
            500
        ]);  //


        $__headers  = [];
        $__filename = null;

        $header_fn = function ($cURLHandle, $header) use (&$__headers, &$__filename) {
            $pieces = explode(":", $header);

            if (count($pieces) >= 2)
                $__headers[trim($pieces[0])] = trim($pieces[1]);


            if (isset($__headers['Content-Disposition'])){
                if (preg_match('/filename="([a-z-_.]+)";/i', $__headers['Content-Disposition'], $matches)){
                    $__filename= $matches[1];
                }
            }

            return strlen($header); // <-- this is the important line!
        };

        curl_setopt($curl, CURLOPT_HEADERFUNCTION,
            $header_fn
        );

        $response  = curl_exec($curl);
        $err_msg   = curl_error($curl);
        $http_code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $content_type  = curl_getinfo($curl,CURLINFO_CONTENT_TYPE);
        $effective_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

        curl_close($curl);

        $data = ($decode && $response !== false) ? json_decode($response, true) : $response;


        $ret = [
            'data'          => $data,
            'http_code'     => $http_code,
            'error'         => $err_msg
        ];

        static::$headers       = $__headers;
        static::$filename      = $__filename;
        static::$content_type  = $content_type;
        static::$effective_url = $effective_url;

        return $ret;
    }

    static function getFilename(){
        return static::$filename;
    }

    static function getHeaders(){
        return static::$headers;
    }

    static function getContentType(){
        return static::$content_type;
    }

    static function getEffectiveUrl(){
        return static::$effective_url;
    }

    static function linkDownload(string $url, $dest_path = null, bool $disable_ssl = true, Array $options = []){
        if (empty($dest_path)){
            $dest_path = STORAGE_PATH;
        }
    
        $client = new ApiClient($url);

        $options = array_merge([
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36',
            CURLOPT_HEADER    => true,
            CURLOPT_VERBOSE   => true
        ], $options);

        $client->addOptions([
            $options
        ]);

        $client
        ->when($disable_ssl, function($questo){
            $questo->disableSSL();
        })
        
        
        ->get();

        $res = $client->getResponse(false);

        if (!empty($res) && isset($res['data'])){
            $data     = $res['data'];
            $filename = $client->getFilename();

            if (empty($filename)){
                throw new \Exception("Nombre de archivo no encontrado");
            }

            $bytes = file_put_contents($dest_path . $filename, $data);

            return ($bytes !== 0);
        }

        return false;
    }

}

