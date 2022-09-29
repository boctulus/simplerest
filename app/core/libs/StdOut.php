<?php declare(strict_types=1);

namespace simplerest\core\libs;

class StdOut
{
    static $render = true;

    static function pprint($v, bool $additional_carriage_return = false){
        if (self::$render){
            d($v, null, $additional_carriage_return);
        }
    }

    static function hideResponse(){
        self::$render = false;
    }

    static function showResponse(bool $status = true){
        self::$render = $status;
    }
}

