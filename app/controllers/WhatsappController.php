<?php

namespace simplerest\controllers;

use simplerest\core\libs\Arrays;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyController;


class WhatsappController extends MyController
{
    /*
        https://tinyurl.com/wa-boctulus
    */
    protected $phones = [
        'uk' => '+44 754 1919915',
        'ph' => '+63 9620738513',
        'es' => '+34 644 149161',
    ];

    function __construct()
    {
       $this->setPhone(Arrays::arrayValueFirst($this->phones));
    }

    function help(){
        echo <<<STR
        WHATSAPP COMMAND HELP

        Ex.

        php com whatsapp
        php com whatsapp {numero}
        php com whatsapp {numero} '{mensaje}'
        php com whatsapp alias=ph
        php com whatsapp getPhone es
        php com whatsapp tinyurl
        STR;

        print_r(PHP_EOL);
    }

    private function sanitize($phone){
        $phone = str_replace([' ', '-', '+'], '', $phone);
        $phone = preg_replace("/[^0-9]/", "", $phone);

        return $phone;
    }

    function setPhone($phone){
        $this->phones[] = $this->sanitize($phone); 
    }

    /*
        Ej:
        
        php com whatsapp getPhone uk
    */
    function getPhone($alias){
        return ($this->phones[$alias]); 
    }

    function index($alias = null)
    {
        if (!empty($alias)){
            $phone = $this->phones[$alias];
            return $this->_link($phone);
        }

        return $this->_link();            
    }

    /*
        Devuelve:

        https://tinyurl.com/wa-boctulus-uk
        https://tinyurl.com/wa-boctulus-es
        https://tinyurl.com/wa-boctulus-ph
        
        o sino se especifica alias,...

        https://tinyurl.com/wa-boctulus
    */

    function tinyurl($alias = null){
        return 'https://tinyurl.com/wa-boctulus' . ($alias != null ? "-$alias" :'');
    }

    private function _link($phone = null, $message = null){
        $phone = $phone ?? Arrays::arrayValueFirst($this->phones);        

        $phone = $this->sanitize($phone);

        if (empty($message)){
            return "https://wa.me/$phone";
        } else {
            return "https://api.whatsapp.com/send?phone=$phone&text=$message";
        }
    }   

    // Ej: php com whatsapp to '+55 11 91846‑0531'
    function to($phone = null, $message = null){
        return $this->_link($phone, $message);
    }

    /*
        Habilita nuevos comandos y opciones

        Ej:

        php com whatsapp {numero}
        php com whatsapp alias=ph
    */
    function __call($name, $arguments)
    {
        if (is_numeric($name) || Strings::startsWith('+', $name)) {
            return $this->_link($name, ...$arguments);
        } else if (Strings::contains("=", $name)){
            return $this->index(Strings::after($name,"="), $arguments);
        }        
    }
}

