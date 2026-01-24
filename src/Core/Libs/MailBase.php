<?php

namespace Boctulus\Simplerest\Core\Libs;

use Boctulus\Simplerest\Core\Interfaces\IMail;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Factory;

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

