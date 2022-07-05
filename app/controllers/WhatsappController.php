<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class WhatsappController extends MyController
{
    protected $phone = '+34 644 14 9161';

    function __construct()
    {
        $this->phone = str_replace(['+', ' '], '', $this->phone);
    }

    function index()
    {
        return $this->link();            
    }

    function link($phone = null, $message = null){
        if ($message === null){
            $message = "Hola";
        }

        $phone = $phone ?? $this->phone;

        return "https://api.whatsapp.com/send?phone=$phone&text=$message";
    }

    function call($phone = null, $message = null){
        return $this->link($phone, $message);
    }

    // El fron controller requiere explicitamentar el action del controlador. No funciona. Podria fixearse
    function __call($name, $arguments)
    {
        if (!is_numeric($name)){
            throw new \InvalidArgumentException("Numero de telefono no valido");
        }

        return $this->link($name, ...$arguments);
    }
}

