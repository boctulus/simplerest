<?php

namespace simplerest\background\tasks;

use simplerest\core\libs\Task;

class __NAME__ extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit;
    static protected $memory_limit;
    static protected $overlap = false;

	function run(...$args){
		// your logic here
	}
}
