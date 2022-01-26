<?php

namespace simplerest\jobs\tasks;

use simplerest\core\libs\Task;
use simplerest\core\libs\Files;

class UnoTask extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit   ;
    static protected $memory_limit;
    static protected $dontOverlap = false;

	function run(...$params){
		// your logic here
		Files::logger(get_class());
        sleep(20);
	}
}
