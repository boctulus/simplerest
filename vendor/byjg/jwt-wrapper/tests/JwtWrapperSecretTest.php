<?php

use ByJG\Util\JwtWrapper;

require_once __DIR__ . '/JwtWrapperHashTest.php';

class JwtWrapperSecretTest extends JwtWrapperHashTest
{
    /**
     * @throws \ByJG\Util\JwtWrapperException
     */
    protected function setUp()
    {
        $private = <<<TEXT
***PRIVATE KEY REMOVED***
TEXT;
        $public = <<<TEXT
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5PMdWRa+rUJmg6QMNAPI
Xa+BJVN7W0vxPN3WTK/OIv5gxgmj2inHGGc6f90TW/to948LnqGtcD3CD9KsI55M
ubafwBYjcds1o9opZ0vYwwdIV80cOVZX1IUZFTbnyyKcXeFmKt49A52haCiy4iNx
cRK38tOCApjZySx/NzMDeaXuWe+1nd3pbgYa/I8MkECa5EyabhZJPJo9fGoSZIkl
Nnyq4TfAUSwl+KN/zjj3CXad1oDT7XDDgMJDUu/Vxs7h3CQI9zILSYcL9zwttbLn
JW1WcLlAAIaAfABtSZboznsStMnYto01wVknXKyERFs7FLHYqKQANIvRhFTptseh
owIDAQAB
-----END PUBLIC KEY-----
TEXT;

        $this->jwtKey = new \ByJG\Util\JwtRsaKey($private, $public);

        unset($_SERVER["HTTP_AUTHORIZATION"]);
        $this->object = new JwtWrapper($this->server, $this->jwtKey);
    }


    /**
     * @throws \ByJG\Util\JwtWrapperException
     * @expectedException \Firebase\JWT\SignatureInvalidException
     */
    public function testTokenWrongSecret()
    {
        $jwt = $this->object->createJwtData($this->dataToToken);
        $token = $this->object->generateToken($jwt);

        $private = <<<TEXT
***PRIVATE KEY REMOVED***
TEXT;
        $public = <<<TEXT
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA9QTmRYW+S+9QeylWIz3c
AMAjaIsJeO32/2IKYS54BBgd9xYpByUUabiua8YvKwv5lmWv2P/llzUQz5ppU1nk
iZljeofkEmxdxKTaLhX5Cd4WZteCEf3SfAY3XMoCeNfXFqKt53SAULH2Ao0HzS+t
Xdru+dES1uD3jcIDMcD+vbQPhDosXM96b95Wmi9O5OBbNmZQsBcqv4Ixd8KXokWu
Nm3Pyo1oa6nCEk08H2wx/26zQ/Jf3TsVJ+jYd0UgKsfQxtMU2tMIvJV8bzjS++HH
hd/O/oW8go0AAOuSygoPWE0y88dAH3bHiWlpRupT8ENCYeMpObpTRXsAFwUn49io
fQIDAQAB
-----END PUBLIC KEY-----
TEXT;

        $jwtWrapper = new JwtWrapper($this->server, \ByJG\Util\JwtRsaKey::getInstance($private, $public));

        $jwtWrapper->extractData($token);
    }
}
