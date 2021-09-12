<?php

use ByJG\Session\JwtSession;

require_once __DIR__ . "/JwtSessionTest.php";

class JwtSessionRsaTest extends JwtSessionTest
{
    protected function setUp()
    {
        $secret = <<<PRIVATE
***PRIVATE KEY REMOVED***
PRIVATE;

        $public = <<<PUBLIC
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5PMdWRa+rUJmg6QMNAPI
Xa+BJVN7W0vxPN3WTK/OIv5gxgmj2inHGGc6f90TW/to948LnqGtcD3CD9KsI55M
ubafwBYjcds1o9opZ0vYwwdIV80cOVZX1IUZFTbnyyKcXeFmKt49A52haCiy4iNx
cRK38tOCApjZySx/NzMDeaXuWe+1nd3pbgYa/I8MkECa5EyabhZJPJo9fGoSZIkl
Nnyq4TfAUSwl+KN/zjj3CXad1oDT7XDDgMJDUu/Vxs7h3CQI9zILSYcL9zwttbLn
JW1WcLlAAIaAfABtSZboznsStMnYto01wVknXKyERFs7FLHYqKQANIvRhFTptseh
owIDAQAB
-----END PUBLIC KEY-----
PUBLIC;

        $this->sessionConfig = (new \ByJG\Session\SessionConfig('example.com'))
            ->withRsaSecret($secret, $public);

        $this->object = new JwtSession($this->sessionConfig);
    }
}
