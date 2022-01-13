<?php declare(strict_types=1);

namespace simplerest\core\libs;

class StdOut
{
    static $_printable = true;

    static function pprint($v, bool $additional_carriage_return = false){
        if (self::$_printable){
            dd($v, null, $additional_carriage_return);
        }
    }

    static function setPrintable(bool $printable){
        self::$_printable = $printable;
    }

    static function hideResponse(){
        static::setPrintable(false);
    }

    static function showResponse(){
        static::setPrintable(true);
    }
}

