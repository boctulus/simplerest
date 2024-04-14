<?php

namespace simplerest\core\libs;

use simplerest\core\libs\Strings;

class CookieJar
{
    private $cookieFile;

    public function __construct(string $cookieFile = 'cookies.txt')
    {
        $this->cookieFile = $cookieFile;
    }

    public function getCookies()
    {
        return file_exists($this->cookieFile) ? file_get_contents($this->cookieFile) : '';
    }

    public function saveCookies($cookies)
    {
        file_put_contents($this->cookieFile, $cookies);
    }
}
