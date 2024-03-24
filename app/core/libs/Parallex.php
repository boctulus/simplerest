<?php

namespace simplerest\core\libs;

use simplerest\core\interfaces\IProcessable;

/*
    Parallex Task Manager

    @author Pablo Bozzolo boctulus@gmail.com
*/

class Parallex
{
    protected static $offset;
    protected static $min_secs_t_locked = 120; // Tiempo mínimo de bloqueo en segundos
    protected static $max_secs_t_locked = 300; // Tiempo máximo de bloqueo en segundos
    protected static $transient_name    = 'parallex';
    protected static $processHandler;

    public function __construct(IProcessable $processHandler, $min_t_locked = null, $max_t_locked = null)
    { 
        static::$processHandler = $processHandler;

        if ($min_t_locked !== null){
            static::$min_secs_t_locked = $min_t_locked;
        }

        if ($max_t_locked !== null){
            static::$max_secs_t_locked = $max_t_locked;
        }

        // Verificar si se superó el tiempo máximo de bloqueo y desbloquear si es necesario
        static::checkMaxTimeLocked();
    }

    // Función para verificar y desbloquear si se superó el tiempo máximo de bloqueo
    protected static function checkMaxTimeLocked(){
        $transient = static::getTransient();

        if ($transient !== false && $transient['lock']) {
            $start_time = $transient['locked_time'];

            if ($start_time !== null) {
                
                $current_time = time();
                $elapsed_time = $current_time - $start_time;

                if ($elapsed_time > static::$max_secs_t_locked) {
                    // Desbloquear si se superó el tiempo máximo de bloqueo
                    static::setLock(false);
                }
            }
        }
    }

    public static function setTransient($data)
    {
        set_transient(static::$transient_name, $data);
    }

    public static function initTransient($offset = null, $lock = null)
    {
        if ($offset === null) {
            $offset = 0;
        }

        if ($lock === null) {
            $lock = false;
        }

        $data = [
            'rows'        => static::$processHandler::count(),
            'offset'      => $offset,
            'lock'        => $lock,
            'locked_time' => $lock ? time() : null,
        ];

        dd($data, "Initializing Transient");

        static::setTransient($data);
    }

    public static function getTransient()
    {
        return get_transient(static::$transient_name);
    }

    public static function deleteTransient()
    {
        delete_transient(static::$transient_name);
    }

    public static function isLocked()
    {
        $transient = static::getTransient();

        if ($transient === false) {
            return false;
        }

        return $transient['lock'];
    }

    public static function isTimeLocked()
    {
        $transient = static::getTransient();

        if ($transient === false) {
            return false;
        }

        // Verificar si ha pasado al menos un minuto desde el inicio
        $start_time = $transient['locked_time'];

        if ($start_time === null) {
            return false;
        }

        $current_time = time();
        $elapsed_time = $current_time - $start_time;

        return ($elapsed_time <= static::$min_secs_t_locked);
    }
    
    // lock / unlock
    public static function setLock(bool $val)
    {
        $transient = static::getTransient();

        if (static::isTimeLocked()){
            return false;
        }

        // Actualizar timestamp cuando se bloquea
        if ($val === true) {
            $transient['locked_time'] = time();
        } else {
            $transient['locked_time'] = null;
        }

        if ($transient['lock'] && $val ===  false){
            dd("[^] Unlocking...");
        } else {
            if ($transient['lock'] === false && $val){
                dd("[^] Locking... ");
            }
        }

        $transient['lock'] = $val;

        set_transient(static::$transient_name, $transient);

        return true;
    }

    public static function setOffset(int $val)
    {
        $transient = static::getTransient();

        if ($transient === false) {
            static::initTransient($val);
            return false;
        }

        $transient['offset'] = $val;

        // Al setear offset quito el otro tipo de bloqueo
        if ($val === 0){
            $transient['lock'] = false;
        }

        set_transient(static::$transient_name, $transient);

        return true;
    }

    public static function getOffset()
    {
        $transient = static::getTransient();

        if ($transient === false) {
            return false;
        }

        return $transient['offset'] ?? null;
    }

    public static function isDone($rows, $offset)
    {
        $res = ($offset >= $rows - 1);

        if ($res) {
            debug("Done. ALL lots were already processed");
        }

        return $res;
    }

    public static function run(int $limit){
        if (static::getTransient() === false){
            $rows = static::$processHandler::count();
    
            // Bloqueo antes de comenzar
            static::initTransient(0, true);
    
            static::$processHandler::run(null, 0, $limit);
    
            // Valido para la primer pagina
            if ($rows > $limit){
                $offset = $limit;
            }
            
            if (static::isDone($rows, $offset)){
                // Bloqueo por completo
                static::setOffset(-1);            
            } else {
                static::setOffset($offset);  
            }
    
            static::setLock(false);      
        } else {
            $data = static::getTransient();
    
            dd($data, 'T');
    
            // Si hay datos en el transient, continuar desde donde se quedó
            $rows        = $data['rows'];
            $offset      = $data['offset'];
            $lock        = $data['lock'];
    
            /// Verificar si ya se procesaron todos los registros
            if ($offset >= $rows) {
                // Bloque total porque se ha completado el procesamiento de todos los lotes        
                $offset = -1;
            } else {
                // Verificar si el proceso está bloqueado
                if (!$lock) {
                    // Bloqueo antes de comenzar
                    static::setLock(true);
    
                    // Proceso lote
                    static::$processHandler::run(null, $offset, $limit);
    
                    // Calcular el nuevo offset para la siguiente iteración
                    $offset = $offset + $limit;
    
                    if (static::isDone($rows, $offset)){
                        // Bloqueo por completo
                        static::setOffset(-1);            
                    } else {
                        static::setOffset($offset);  
                    }
    
                    static::setLock(false);                
                    
                    dd($data, 'T');
                } else {
                    dd("LOCKED");
                }
            }
        }
    }
}