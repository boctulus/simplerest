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

    Puertos:

    22  por defecto para SFTP
    990 por defecto para FTPS
*/
class FtpController extends MyController
{
    protected $host;
    protected $user;
    protected $pass;
    protected $port;

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

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

