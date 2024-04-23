<?php

namespace simplerest\core\libs;

class Cookie 
{
    public static function set($name, $value, $expiry = 0, $path = '/', $domain = '', $secure = false, $httponly = false) {
        if (isset($_COOKIE[$name])){
            unset($_COOKIE[$name]);
        }
        
        setcookie($name, $value, $expiry, $path, $domain, $secure, $httponly);
    }

    public static function get($name = null) {
        return ($name != null) ? ($_COOKIE[$name] ?? null) : $_COOKIE;
    }

    public static function delete($name, $path = '/', $domain = '') {
        unset($_COOKIE[$name]);
        setcookie($name, '', time() - 1, $path, $domain);
    }
}


