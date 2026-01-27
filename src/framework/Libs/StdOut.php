<?php declare(strict_types=1);

namespace Boctulus\Simplerest\Core\Libs;

class StdOut
{
   /** @var bool Controla si se muestra la salida en pantalla */
   static $render = true;

   /** @var string|null Ruta del archivo donde se guardará la salida */
   static $path;

   /** @var bool Indica si se debe incluir fecha/hora en el log */
   static $log_includes_datetime;

   /**
    * Muestra y opcionalmente guarda el valor formateado
    * 
    * @param mixed $v Valor a mostrar/guardar
    * @param bool $additional_carriage_return Agrega salto de línea extra
    * @param bool $save Indica si debe guardarse en el archivo configurado
    * @return void
    */
    static function print($v, bool $additional_carriage_return = false, $save = false){
        if (static::$path !== null){
            ob_start();
            VarDump::dd($v, null, $additional_carriage_return);
            $content = ob_get_contents();
            ob_end_clean();

            if (static::$log_includes_datetime){
                $content = at(). "\t" . $content;
            }

            if ($save){
                file_put_contents(static::$path, $content, FILE_APPEND);
            }              
        }

        if (static::$render){
            VarDump::dd($v, null, $additional_carriage_return);
        }
    }

    // alias
    static function dd($v, bool $additional_carriage_return = false){
        static::pprint($v, $additional_carriage_return);
    }

    /**
    * Configura la salida para que se escriba en un archivo específico
    * 
    * @param string $path Ruta del archivo donde se escribirá
    * @param bool $only Si es true, solo escribe al archivo. Si es false, también muestra en pantalla
    * @return void
    */
    static function toFile(string $path, bool $only = true){
        static::$path   = $path;
        static::$render = !$only; 
    }

    /**
    * Configura la salida para que se escriba en el archivo de log
    * 
    * @param bool $only Si es true, solo escribe al log. Si es false, también muestra en pantalla
    * @param bool $include_datetime Si es true, incluye fecha/hora en cada entrada
    * @return void
    */
    static function toLog(bool $only = true, bool $include_datetime = true){
        static::$log_includes_datetime = $include_datetime;
        static::toFile(LOGS_PATH . '/' . Config::get()['log_file'], $only);
    }

    /**
    * Deshabilita la salida en pantalla
    * 
    * @return void
    */
    static function hideResponse(){
        self::$render = false;
    }

     /**
    * Configura si se muestra la salida en pantalla
    * 
    * @param bool $status True para mostrar, false para ocultar
    * @return void
    */
    static function showResponse(bool $status = true){
        self::$render = $status;
    }
}

