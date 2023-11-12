<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\IMail;
use simplerest\core\Model;
use simplerest\core\libs\DB;
use simplerest\core\libs\Factory;

abstract class MailBase
{
    protected static $errors      = null; 
    protected static $status      = null; 
    protected static $silent      = false;
    protected static $debug_level = null;
    protected static $mailer      = null;

     // change mailer
    static function setMailer(string $name){
        static::$mailer = $name;
    }
    
    static function errors(){
        return static::$errors;
    }

    static function status(){
        return (empty(static::$errors)) ? 'OK' : 'error';
    }

    static function silentDebug($level = null){    
        static::$silent = !empty($level);
    }
}

