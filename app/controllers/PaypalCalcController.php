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
        Supuestamente sigue siendo 5.7% + 0.30 pero ...

        -------

        https://www.paypal.com/us/webapps/mpp/paypal-fees

        Desde Chile:

        79,35 USD --- 4198,25 --- 3,998.53 PHP (72.04 USD)

        1 USD = 52.9080 cuando el real es 1 USD = 55.50 
            
        PayPal esta llevandose aprox 10%
    */

    static protected $per   = 10.0;
    static protected $fixed = 0;
    static protected $round_fn = null; // 'floor';
    static protected $paypal_me = 'paypal.me/kodeservices';
    static protected $email = 'revelynpaduapadua+2@gmail.com';

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

    function email(){
        return static::$email;
    }

    function mail(){
        return static::$email;
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

