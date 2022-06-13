<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class WhatsappController extends MyController
{
    protected $phone = '573007866252';

    function index()
    {
        return $this->link();            
    }

    function link($message = null, $phone = null){
        if ($message === null){
            $message = "Hola";
        }

        $phone = $phone ?? $this->phone;

        return "https://api.whatsapp.com/send?phone=$phone&text=$message";
    }
}

