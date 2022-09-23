<?php declare(strict_types=1);

namespace simplerest\core\traits;

use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;
use simplerest\libs\Debug;
use simplerest\core\libs\Url; 

trait ExceptionHandler
{
    /**
     * exception_handler
     *
     * @param  mixed $e
     *
     * @return void
     */
    function exception_handler($e) {
        DB::closeAllConnections();
       
        if (config()['debug']){

            $e      = new \Exception();
            $traces = $e->getTrace();

            foreach ($traces as $tx => $trace){
                $args = $exception = $trace['args'];

                foreach ($args as $ax => $arg){
                    $exception = $traces[$tx]['args'][$ax];

                    $trace = $exception->getTraceAsString();
                    $trace = explode("\n", $trace);

                    $traces[$tx]['args'][$ax] = [
                        'message' => $exception->getMessage(),
                        'prev'   => $exception->getPrevious(),
                        'code'   => $exception->getCode(),
                        'file'   => $exception->getFile(),
                        'line'   => $exception->getLine(),
                        'trace'  => $trace
                    ];
                }
            }
        

            $backtrace      = json_encode($traces, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;
            $error_location = 'Error on line number '.$e->getLine().' in file - '.$e->getFile();
        
            error($e->getMessage(), 500, $backtrace, $error_location);
        } else {
            error($e->getMessage(), 500);
        }
        
    }
    
}
