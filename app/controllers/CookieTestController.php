<?php

namespace simplerest\controllers;


use simplerest\core\libs\Cookie;
use simplerest\core\libs\Strings;
use simplerest\core\libs\CookieJar;
use simplerest\core\controllers\Controller;

class CookieTestController extends Controller
{
    // Crea una cookie
    function create()
    {
        Cookie::set('my_cookie', 'Pablo', 5);  
    }

    // Lee e imprime
    function get()
    {
        $ch = curl_init ("http://simplerest.lan/cookie_test/create");
    
        $cookieJar = new CookieJar();

        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true); /// 

        curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookieJar->getCookies());
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar->getCookieFile());    

        $output = curl_exec ($ch);

        $info         = curl_getinfo($ch);    
        $cookie_info  = curl_getinfo($ch, CURLINFO_COOKIELIST);

        curl_close($ch);

        // $cookies = [];
        // preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $output, $cookies);

        dd($output, 'OUTPUT'); // si
        dd($info, 'INFO'); // si
        dd($cookie_info, 'COOKIE INFO'); // no
        // dd($cookies, 'COOKIES'); 
    }
}

