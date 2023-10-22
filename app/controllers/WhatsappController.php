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
       $this->setPhone($this->phone);
    }

    private function sanitize($phone){
        $phone = str_replace([' ', '-', '+'], '', $phone);
        $phone = preg_replace("/[^0-9]/", "", $phone);

        return $phone;
    }

    function setPhone($phone){
        $this->phone = $this->sanitize($phone); 
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

        $phone = $this->sanitize($phone);

        if (empty($message)){
            return "https://wa.me/$phone";
        } else {
            return "https://api.whatsapp.com/send?phone=$phone&text=$message";
        }
    }   

    function get($phone = null, $message = null){
        return $this->link($phone, $message);
    }

    // Ej: php com whatsapp to '+55 11 91846‑0531'
    function to($phone = null){
        return $this->link($phone);
    }

    /*
        El front controller requiere explicitamentar el action del controlador. No funciona. Podria fixearse

        Otra opcion seria tener un router para la consola
    */
    function __call($name, $arguments)
    {
        if (!is_numeric($name)){
            throw new \InvalidArgumentException("Comando '$name' no valido");
        }

        return $this->link($name, ...$arguments);
    }
}

