<?php

namespace Boctulus\ApiClient\Helpers;

use Boctulus\ApiClient\Helpers\Strings;
use Boctulus\ApiClient\Helpers\ApiClient;

class Url
{
    /*
     * Determines the boolean value of a query parameter.
     *
     * This function checks if a query parameter exists and evaluates its boolean value.
     * It accepts a key corresponding to the query parameter name and an optional default value.
     * The function returns true if the parameter value is 'on', 'yes', or '1',
     * false if the parameter value is 'off', 'no', or '0', and the default value otherwise.
     *
     * @param string $key The name of the query parameter to check.
     * @param bool $default (optional) The default value to return if the parameter is not set. Default is false.
     * @return bool The boolean value of the query parameter.
     *
     * 
        Ej:

        $sku          = ['01004','17', '001', '01006'];
        $sync_categos = Url::boolOption('C');
        $inventory    = Url::boolOption('I');

        try {
            Sync::init($sync_categos, $sku ?? null, $inventory);
        } catch (\Exception $e){
            Logger::logError($e);
        }
     */
    static function boolOption(string $key, bool $default = false){
        if (!isset($_GET[$key])){
            return $default;
        }

        $val = strtolower($_GET[$key]);

        if (in_array($val, ['on', 'yes', '1'])){
            return true;
        }

        if (in_array($val, ['off', 'no', '0'])){
            return false;
        }

        return $default;
    }

    static function validate(string $url): bool {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    static function validateOrFail(string $url): void {
        if (!self::validate($url)) {
            throw new \InvalidArgumentException("URL '$url' is invalid");
        }
    }

    static function inArray(array $links, $link) {
        foreach ($links as $existingLink) {
            $existingLinkId = parse_url($existingLink, PHP_URL_QUERY);
            $linkId = parse_url($link, PHP_URL_QUERY);

            if ($existingLinkId === $linkId) {
                return true;
            }
        }
        return false;
    }

    static function getHeaders($url, bool $verify_ssl = true)
    {
        $options = [];
        
        if (!$verify_ssl){
            $options["ssl"] = [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ];
        }

        $context = stream_context_create($options);

        return get_headers($url, 1, $context);
    }

    /*
        Obtiene la url final luego de redirecciones

        (no siempre funciona)

        https://stackoverflow.com/a/7555543/980631
    */
    static function getFinalUrl($url, bool $verify_ssl = true) {
        stream_context_set_default(array(
            'http' => array(
                'method' => 'HEAD'
            )
        ));

        $headers = static::getHeaders($url, $verify_ssl);

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

        $p['path'] = isset($p['path']) ? rtrim($p['path'], '/') : '';
        $query     = isset($p['query']) ? "?{$p['query']}" : '';
        $p['host'] = str_replace('//', '/', $p['host']);

        return "{$p['scheme']}://{$p['host']}{$p['path']}$query";
    }

    /**
     * Check if a URL is absolute.
     *
     * @param string $url The URL to check.
     * @return bool True if the URL is absolute, false otherwise.
     */
    static function isAbsoluteURL(string $url): bool
    {
        return preg_match('/^(https?:)?\/\//', $url);
    }

    /**
     * Check if a URL is relative.
     *
     * @param string $url The URL to check.
     * @return bool True if the URL is relative, false otherwise.
     */
    static function isRelativeURL(string $url): bool
    {
        return !static::isAbsoluteURL($url);
    }

    /*  
        Convierte una url o slug de relativo a absoluta

        Ej:

        Url::toAbsolute('/WebRoot/StoreES2/Shops/63993920/Categories/Category1/../../MediaGallery/Categories/Category1/AMB_BLANCOS_30X60.jpg');
    */
    static function toAbsolute($url){
        return Files::normalize($url, Files::LINUX_DIR_SLASH);
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

    static function getSlugs($url = null, $as_string = false){
        $url          = $url ?? static::currentUrl();

        $segments_str = parse_url($url, PHP_URL_PATH);
        $segments_str = Strings::rTrim('/', Strings::lTrim('/', $segments_str));

        if ($as_string){
            return (trim($segments_str) !== '' ? '/' : '') . $segments_str;
        }
    
        $slugs = explode('/', $segments_str);
        
        if (count($slugs) === 1 && empty(trim($slugs[0]))){
            $slugs = [];
        }

        return $slugs;
    }

	static function queryString($url = null) : Array {
        if ($url !== null){
            return static::parseStrQuery($url);
    }

        if (!isset($_SERVER['QUERY_STRING'])){
            return [];
        }

		return static::parseStrQuery($_SERVER['QUERY_STRING']);
	}


    static function query($url = null){
        return static::queryString($url);
    }
  
    /*
        Si esta cerrado el puerto 443 puede demorar demasiado en contestar !
    */
    static function hasSSL( $domain ) {
        /*
            Si el puerto 443 esta cerrado,....

            Warning: fsockopen(): unable to connect to ssl://{dominio}:443
            (No se puede establecer una conexión ya que el equipo de destino denegó expresamente dicha conexión)
        */

        $ssl_check = @fsockopen( 'ssl://' . $domain, 443, $errno, $errstr, 30 );
        
        if ( $ssl_check ) { 
            fclose( $ssl_check ); 
        }
        
        return (bool) $ssl_check;
    }

    /*
        https://gist.github.com/jubalm/3447495
    */
    static function isSSL() {
        if ( isset($_SERVER['HTTPS']) ) {
            if ( 'on' == strtolower($_SERVER['HTTPS']) )
                return true;
        
            if ( '1' == $_SERVER['HTTPS'] )
                return true;
        } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
            return true;
        
        }

        return false;
    }

    static function httpProtocol(){
        $config = config();

        if (isset($config['https']) && $config['https'] != null){
            $is_ssl = ($config['https'] && !strtolower($config['https']) == 'off');
        } else {
            /*
                Chequear si isSSL() funciona bien porque hasSSL() puede quedarse esperando si el puerto esta cerrado
            */

            //$is_ssl = self::hasSSL($_SERVER['HTTP_HOST'])
           $is_ssl = static::isSSL();
        }

        return $is_ssl ? 'https' : 'http';
    }

    static function getProtocol( $url = null){
        if (empty($url)){
            return static::httpProtocol();
        }
       
        if (Strings::startsWith('http://', $url)){
            return 'http';
        }

        if (Strings::startsWith('https://', $url)){
            return 'https';
        }

        return null;
    }

    static function getProtocolOrFail( $url = null){
        $protocol = static::getProtocol($url);

        if (empty($protocol)){
            throw new \InvalidArgumentException("Impossible to determinatte if the protocol is http or https");
        }

        return $protocol;
    }
    
    /*
        Devuelve algo como:

        Host: http://simplerest.lan:80
    */
    static function constructHostHeader() {
        $host     = static::getHostname();
        $protocol = static::httpProtocol();
    
        $port = ($protocol == 'https') ? '443' : '80'; // default port for http and https
        $header = "Host: $host:$port\r\n";
        return $header;
    }  

    /**
     * urlCheck - complement for parse_url
     *
     * @param  string $url
     *
     * @return bool
     */
    static function urlCheck(string $url){
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

    static function isPostman(){
        static $is;

        if ($is !== null){
            return $is;
        }

        if (!isset($_SERVER['HTTP_USER_AGENT'])){
            $is = false;
            return $is;
        }

		$is = Strings::startsWith('Postman', $_SERVER['HTTP_USER_AGENT']);
        return $is;
	}

    static function isInsomnia(){
        static $is;

        if ($is !== null){
            return $is;
        }

        if (!isset($_SERVER['HTTP_USER_AGENT'])){
            $is = false;
            return $is;
        }

        $is = Strings::startsWith('insomnia', $_SERVER['HTTP_USER_AGENT']);
        return $is;
    }

    static function isPostmanOrInsomnia(){
		static $is;

		if ($is !== null){
			return $is;
		}

		$is = static::isPostman() || static::isInsomnia();

		return $is;
	}


    /*
        Usar antes de parseStrQuery()
    */
    static function hasQueryParam(string $url,  $param = null){
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
        @return string|array
    */
    static function getQueryParam(string $url = null, $param = null, $autodecode = true) {
        if (empty($url)){
            $url = static::currentUrl();
        }

        if ($param === null){
            return static::getQueryParams($url);
        }

        if (!Strings::startsWith('http', $url)){
            throw new \InvalidArgumentException("URL '$url' is invalid");
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

        if ($autodecode && $x !== null && Strings::contains('%2F', $x)){
            $x = urldecode($x);
        }

        return $x;
    }

    static function removeParam($url, $parameter) {
        $urlParts = parse_url($url);
    
        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], $params);

            if (!isset($params[$parameter])){
                return $url;
            }
    
            unset($params[$parameter]);
    
            $newQuery = http_build_query($params);
    
            $urlParts['query'] = $newQuery;
        }
    
        $newUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];
    
        if (isset($urlParts['fragment'])) {
            $newUrl .= '#' . $urlParts['fragment'];
        }
    
        if (isset($urlParts['query']) && !empty($urlParts['query'])) {
            $newUrl .= '?' . $urlParts['query'];
        }
    
        return $newUrl;
    }

    static function encodeParams(array $data, string $numeric_prefix = "",  $arg_separator = '&', int $encoding_type = PHP_QUERY_RFC1738){
        return http_build_query($data, $numeric_prefix, $arg_separator, $encoding_type);
    }

    /*
        Ej:

        $offset = 0;
        $limit  = 99999;
        $order  = [
            "stock" => "DESC"
        ];

        $url  = Url::buildUrl("http://relmotor.lan/woo_commerce_filters/product_search", [
            "offset" => $offset,
            "limit"  => $limit,
            "order"  => $order
        ]);

        Resultado:

        http://relmotor.lan/woo_commerce_filters/product_search?offset=0&limit=99999&order%5Bstock%5D=DESC
    */
    static function buildUrl(string $base_url, array $data, string $numeric_prefix = "",  $arg_separator = null, int $encoding_type = PHP_QUERY_RFC1738){
        return Files::removeTrailingSlash($base_url) . '?'. static::encodeParams($data);
    }
    
    /*
        Agrega o cambia un parametro en una url

        Ej:

        echo Url::addQueryParam('http://simplerest.lan/api/v1/products', 'q', 'fiesta') . "\n";
        echo Url::addQueryParam('http://simplerest.lan/api/v1/products?v=1', 'q', 'fiesta') . "\n";
        echo Url::addQueryParam('http://simplerest.lan/api/v1/products?v=1', 'v', '3') . "\n";
    */
    public static function addQueryParam(string $url, $param_name, $param_value) {
        $parsed_url = Strings::before($url, '?');
        $query_arr  = Url::getQueryParams($url);

        $query_arr[$param_name] = $param_value;
     
        return static::buildUrl($parsed_url, $query_arr);
    }

    static function currentUrl(){
        if (is_cli()){
            return '';
        }

        $actual_link = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        return $actual_link;
    }

    static function getCurrentUrl(){
        return static::currentUrl();
    }

    static function getHostname($url = null, bool $include_protocol = false)
    {
        static $cachedHostnames = [];
     
        if (is_cli() && empty($url)){
            return config()['app_url'];
        }

        if (is_null($url)){
            $url = static::currentUrl();
        }
    
        if (isset($cachedHostnames[$url])) {
            return $cachedHostnames[$url];
        }
    
        $url_info = parse_url($url);

        $hostname = ($url_info['host'] ?? '') . (isset($url_info['port']) ? ':'. $url_info['port'] : '');

        if ($include_protocol){
            $result = $url_info['scheme'] . '://' . $hostname;
        } else {
            $result = $hostname;
        }

        $cachedHostnames[$url] = $result;

        return $result;
    }

     /*
        Salida:

        https://practicatest.cl
    */
    static function getBaseUrl($url = null){
        return static::getHostname($url, true);
    }

    /*
        Salida:

        practicatest.cl
    */
    static function getDomain( $url = null){
        return static::getHostname($url, false);
    }

    /*
        Wrapper sobre ApiClient
    */
    static function getUrlContent(string $url, $cache_exp = false){
        return (new ApiClient($url))->cache($cache_exp)->disableSSL()->get()->getBody();
    }

    static function download(string $url, $dest_path = null, bool $disable_ssl = true, Array $options = []){
        if (empty($dest_path)){
            $dest_path = STORAGE_PATH;
        }
    
        $client = new ApiClient($url);

        $options = array_merge([
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.82 Safari/537.36',
            CURLOPT_HEADER    => true,
            CURLOPT_VERBOSE   => true
        ], $options);

        $client->setOptions([
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
                throw new \Exception("Filename not found");
            }

            $bytes = file_put_contents($dest_path . $filename, $data);

            return ($bytes !== 0);
        }

        return false;
    }

    /*
        https://stackoverflow.com/a/13646735/980631
    */
    static function getVisitorIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                  $_SERVER['REMOTE_ADDR']    = $_SERVER["HTTP_CF_CONNECTING_IP"];
                  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
    
        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }
    
        return $ip;
    }
   
    // alias
    static function ip(){
        return static::getVisitorIP();
    }
}

