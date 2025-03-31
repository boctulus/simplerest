<?php

namespace Boctulus\Simplerest\Background\Tasks;

use Boctulus\Simplerest\Core\Libs\Task;
use Boctulus\Simplerest\Core\Libs\Logger;

class UnaTask extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit   ;
    static protected $memory_limit;
    static protected $overlap = false;

	function run(...$params){
		// your logic here

        Logger::debug();

        dd("Generando numero ...");
		Logger::log('Num: #' . rand(1000,7000));
        sleep(5);
	}
}
