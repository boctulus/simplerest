<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;

/*
    php com paypal_calc
    php com paypal_calc fee 100
    php com paypal_calc get 100
    php com paypal_calc send 100
*/

class PaypalCalcController extends MyController
{
    /*  
        https://www.paypal.com/us/webapps/mpp/paypal-fees
    */

    static protected $per   = 5.4;
    static protected $fixed = 0.3;
    static protected $round_fn = null; // 'floor';
    static protected $paypal_me = 'paypal.me/kodeservices';

    function __construct()
    {
        parent::__construct();        
    }

    function help(){
        echo <<<STR
        PAYPAL_CALC COMMAND HELP

        Ex.

        php com paypal_calc
        php com paypal_calc fee 100
        php com paypal_calc get 100
        php com paypal_calc send 100
        STR;

        print_r(PHP_EOL);
    }

    function index(){
        return 'https://'.static::$paypal_me;
    }

    /*
        @param cantidad enviada
        @return fees 
    */
    function fee($amount){
        $res = static::$fixed + ($amount * static::$per * 0.01);
        
        if (static::$round_fn !== null){
            $res = call_user_func(static::$round_fn, $res);
        }

        return $res;
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
}

