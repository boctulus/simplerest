<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

/*
    DOC

    https://github.com/Nicolab/php-ftp-client

    Requiere FTP extension !!!
*/
class FtpController extends MyController
{
    protected $host = 'ftp://c2380219.ferozo.com';
    protected $user = 'pablob@matchdayauctions.com';
    protected $pass = 'Fdp102938Pro';
    protected $port;

    function basic()
    {
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($this->host);
        $ftp->login($this->user, $this->pass);
    }

    // (FTP-SSL)
    function ftps(){
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($this->host, true, $this->port ?? 990);
        $ftp->login($this->user, $this->pass);
    }
}

