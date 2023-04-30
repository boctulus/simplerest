<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Strings;
use simplerest\core\libs\DB;

class WhatsappController extends MyController
{
    /*
        https://tinyurl.com/wa-boctulus
    */
    protected $phone = '+44 754 1919915';

    function __construct()
    {
        $this->phone = str_replace(['+', ' '], '', $this->phone);
    }

    function index()
    {
        return $this->link();            
    }

    /*
        Tiene quemado el numero de UK
    */
    function tinyurl(){
        return 'https://tinyurl.com/wa-boctulus';
    }

    function link($phone = null, $message = null){
        $phone = $phone ?? $this->phone;

        $phone = Strings::removeBeginning('+', $phone);
        $phone = str_replace(' ', '', $phone);

        if (empty($message)){
            return "https://wa.me/$phone";
        } else {
            return "https://api.whatsapp.com/send?phone=$phone&text=$message";
        }
    }   

    function call($phone = null, $message = null){
        return $this->link($phone, $message);
    }

    /*
        El front controller requiere explicitamentar el action del controlador. No funciona. Podria fixearse

        Otra opcion seria tener un router para la consola
    */
    function __call($name, $arguments)
    {
        if (!is_numeric($name)){
            throw new \InvalidArgumentException("Numero de telefono no valido");
        }

        return $this->link($name, ...$arguments);
    }
}

