<?php

namespace Boctulus\Simplerest\Background\Tasks;

use Boctulus\Simplerest\Core\Libs\Task;
use Boctulus\Simplerest\Core\Libs\Logger;

class OtraTask extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit   ;
    static protected $memory_limit;
    static protected $overlap = false;

	function run(...$params){
		// your logic here
		Logger::log("Hola Mundo");
        sleep(2);
	}
}
