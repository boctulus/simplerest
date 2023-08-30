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
*/
class TestFtpController extends MyController
{
    function t1()
    {
        // Connect to a server FTP :

        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($host);
        $ftp->login($login, $password);
    }

    function t2(){
        // Connect to a server FTP via SSL (on port 990 or another port) :

        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($host, true, 990);
        $ftp->login($login, $password);
    }
}

