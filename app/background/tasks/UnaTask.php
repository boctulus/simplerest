<?php

namespace simplerest\background\tasks;

use simplerest\core\libs\Task;
use simplerest\core\libs\Logger;

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
