<?php

namespace simplerest\jobs\tasks;

use simplerest\core\libs\Task;
use simplerest\core\libs\Files;

class DosTask extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit   ;
    static protected $memory_limit;
    static protected $dontOverlap = false;

	function run(string $name, int $age){
		// your logic here
		Logger::log("Hello $name. Are you $age years old?");
        sleep(3);
	}
}
