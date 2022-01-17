<?php declare(strict_types=1);

namespace simplerest\core\traits;

use simplerest\core\libs\Factory;
use simplerest\core\libs\DB;
use simplerest\libs\Debug;
use simplerest\core\libs\Url; 

trait ExceptionHandler
{
    function generateCallTrace()
    {
        $postman = Url::is_postman();
		
		$cli  = (php_sapi_name() == 'cli');
		$br   = ($cli || $postman) ? PHP_EOL : '<br/>';
        $p    = ($cli || $postman) ? PHP_EOL . PHP_EOL : '<p/>';
        $t    = ($cli) ? "\t" : '';

        $e = new \Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();
       
        for ($i = 0; $i < $length; $i++)
        {
            $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }
       
        return $t . implode($br . $t, $result);
    }

    
    function exception_handler($e) {
        DB::closeAllConnections();
       
        if (config()['debug']){

            $backtrace = var_export(debug_backtrace(), true) . PHP_EOL . PHP_EOL;
            $error_location = 'Error on line number '.$e->getLine().' in file - '.$e->getFile();
        
            Factory::response()->sendError($e->getMessage(), 500, $backtrace, $error_location);
        } else {
            Factory::response()->sendError($e->getMessage(), 500);
        }
        
    }
    
}
