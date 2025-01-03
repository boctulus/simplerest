<?php

namespace simplerest\background\tasks;

use simplerest\core\libs\Task;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;

class ZTask extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit;
    static protected $memory_limit;
    static protected $overlap = false;

	function run(){
		// your logic here

        Logger::debug();

        while (1){
            dd("Generando string ...");
            Logger::log(Strings::randomString());
            sleep(1);
        }
	}
}
