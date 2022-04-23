<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Request;
use simplerest\core\Response;
use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;

class PaypalController extends MyController
{
    /*  
        https://www.paypal.com/us/webapps/mpp/paypal-fees
    */

    static protected $per   = 5.7;
    static protected $fixed = 0.75;

    function __construct()
    {
        parent::__construct();        
    }

    /*
        @param cantidad enviada
        @return fees 
    */
    function fee($amount){
        return static::$fixed + ($amount * static::$per * 0.01);
    }

    /*
        @param cantidad que deseo recibir
        @return cantidad que se debe enviar
    */
    function get($amount){
        return $amount + $this->fee($amount);
    }

    /*
        @param  cantidad a enviar
        @return cantidad recibida
    */
    function send($amount){
        return $amount - $this->fee($amount);
    }

    function index(){
    }
}

