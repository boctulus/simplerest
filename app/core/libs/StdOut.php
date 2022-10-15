<?php declare(strict_types=1);

namespace simplerest\core\libs;

class StdOut
{
    static $render = true;
    static $path;

    static function pprint($v, bool $additional_carriage_return = false){
        if (static::$path !== null){
            ob_start();
            d($v, null, $additional_carriage_return);
            $content = ob_get_contents();
            ob_end_clean();

            file_put_contents(static::$path, $content, FILE_APPEND);
            return;
        }

        if (static::$render){
            d($v, null, $additional_carriage_return);
        }
    }

    static function toFile($path){
        static::$path = $path;
    }

    static function toLog(){
        static::$path = LOGS_PATH . '/' . config()['log_file'];
    }

    static function hideResponse(){
        self::$render = false;
    }

    static function showResponse(bool $status = true){
        self::$render = $status;
    }
}

